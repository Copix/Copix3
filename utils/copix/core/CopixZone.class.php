<?php
/**
 * @package		copix
 * @subpackage	utils
 * @author		Croes Gérald
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license 	http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Squelette d'un objet capable de gérer une zone avec un cache
 * 
 * @package		copix
 * @subpackage	utils
 */
abstract class CopixZone {
	
	protected $_params;

	/**
	 * Indique si le cache est actif
	 * 
	 * @var boolean
	 */
	protected $_useCache = false;
	
	/**
	 * Nom des parametres de la zone permettant de l'identifer de façon unique
	 * 
	 * @var array
	 */
	protected $_cacheParams = array ();

	/**
	 * Nettoie le cache de la zone $pName avec pour paramètres $pParams
	 * 
	 * @param string $pName Identifiant de la zone à effacer
	 * @param array $pParams Paramètres de la zone
	 */
	public static function clear ($pName, $pParams = array ()) {
		$zoneObject = self::_create ($pName);
		$zoneObject->_clear ($pParams);
		//On sait que createZone place le contexte d'exécution de la zone.
		CopixContext::pop ();		
	}
	
	/**
	 * Construction d'une nouvelle zone
	 */
	protected function __construct (){
		$this->_params = new CopixParameterHandler ();
	}
	
	/**
	 * Demande l'exécution d'une zone d'identifiant $pName avec ses paramètres $pParams
	 * 
	 * @param string $pName Identifiant de la zone à afficher
	 * @param array $pParams Paramètres d'exécution de la zone
	 * @return string Contenu de la zone
	 */
	public static function process ($pName, $pParams = array ()) {
		$zoneObject = self::_create ($pName);
		$content = $zoneObject->_process ($pParams);
		//On sait que createZone place le contexte d'exécution de la zone.
		CopixContext::pop ();
		return $content;		
	}

	/**
	 * Creation d'un objet zone d'identifiant $pName
	 * 
	 * @param string $pName Nom de la zone à instancier
	 * @return ZoneX
	 */
	private static function _create ($pName) {
		//Récupération des éléments critiques.
		$zoneSelector = CopixSelectorFactory::getZone ($pName);
		CopixContext::push ($zoneSelector->getModule ());
		
		$objName = $zoneSelector->getClassName ();
		return new $objName ();
	}

	/**
	 * Méthode qui calcule le contenu de la zone en fonction de ses paramètres. Choisi entre le cache et la génération du contenu
	 * 
	 * @param array $pParams Paramètres de contexte pour la zone, généralement le contenu de l'url
	 * @return string Contenu de la zone
	 */
	protected function _process ($pParams) {
		$this->_params->setParams ($pParams);
		$contents = '';
		$contentCreated = false;
		$module = CopixContext::get ();
				
		$this->_beforeProcess ();
		
		if ($this->_useCache) {			
			if (CopixCache::exists ($this->_makeId (), 'zones|' . $module . get_class ($this))) {
				$contents = CopixCache::read ($this->_makeId (), 'zones|' . $module . get_class ($this));
				$contentCreated = true;
			} else {
				if (($contentCreated = $this->_createContent ($contents)) === true) {
					CopixCache::write ($this->_makeId (), $contents, 'zones|' . $module . get_class ($this));
				}
			}
		} else {
			$contentCreated = $this->_createContent ($contents);
		}
		
		if (($result = $this->_afterProcess ($contents)) !== null) {
			$contents = $result;
			if ($contentCreated && $this->_useCache) {
				CopixCache::write ($this->_makeId (), $contents, 'zones|' . $module . get_class ($this));
			}
		}
		
		return $contents;
	}
	
	/**
	 * Méthode appelée avant l'appel à _process
	 */
	protected function _beforeProcess () { }
	
	/**
	 * Méthode appelée après l'appel à _process, et qui peut changer le contenu retourné par la zone via le retour de _afterProcess
	 *
	 * @param string $pContent Contenu généré par la zone
	 * @return string Null si on ne veut pas changer le contenu retourné par la zone, sinon le contenu à retourner
	 */
	protected function _afterProcess (&$pContent) {
		return null;
	}

	/**
	 * Efface le cache de la zone
	 * 
	 * @param array $pParams Paramètres de contexte pour la zone
	 * @return boolean
	 */
	protected function _clear ($pParams) {
		$this->_params = $pParams;
		if ($this->_useCache) {
			$module = CopixContext::get ();
			CopixCache::clear ($this->_makeId (), 'zones|' . $module . get_class ($this));
		}
		return true;
	}

	/**
	 * Méthode de création de contenu pour la zone.
	 * Contient le processus de récupération et de création de contenu a partir des paramètres donnés.
	 * C'est cette méthode qui sera invoquée par _process pour créer le contenu s'il n'existe pas en cache
	 * 
	 * @param string $toReturn Contient le contenu de la zone, à recupérer après appel de la méthode
	 * @return boolean Indique si on peut mettre le contenu généré en cache ou pas
	 */
	abstract protected function _createContent (&$toReturn);

	/**
	 * Création de l'identifiant à partir des paramètres de la zone
	 * 
	 * @return array Ensemble d'éléments constituant l'identifiant unique de cache pour la zone
	 */
	protected function _makeId () {
		$toReturn = array ();
		foreach ($this->_cacheParams as $key) {
			$toReturn[$key] = (isset ($this->_params[$key])) ? $this->_params[$key] : null;
		}
		return $toReturn;
	}

	/**
	 * Retourne les paramètres de la zone sous forme de tableau
	 * 
	 * @return array
	 */
	public function asArray () {
		return $this->_params;
	}
	
	/**
	 * Création et retour d'un contenu à partir d'un CopixPPO
	 * 
	 * @param CopixPPO $pPPO PPO à utiliser
	 * @param string $pTemplatename Template à utiliser pour le fetch
	 * @return string 
	 */
	protected function _usePPO ($pPPO, $pTemplateName) {
		$tpl = new CopixTpl ();
		$tpl->assign ('ppo', $pPPO);
		return $tpl->fetch ($pTemplateName);
	}

	public function getParam ($pName, $pDefault = null, $pType = null, $pDefaultIfNotValidate = false){
		return $this->_params->getParam ($pName, $pDefault, $pType, $pDefaultIfNotValidate);
	}
	public function requireParam ($pName, $pType = null){
		return $this->_params->requireParam ($pName, $pType);
	}
	public function assertParams (){
		$args = func_get_args ();
		return call_user_func_array (array ($this->_params, 'assertParams'), $args);
	}
	public function getParams (){
		return $this->_params->getParams ();
	}	
}