<?php
/**
* @package		copix
* @subpackage	core
* @author		Croes Gérald
* @copyright	CopixTeam
* @link			http://copix.org
* @license 		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* Squelette d'un objet capable de gérer une zone avec un cache.
* @package copix
* @subpackage core
*/
abstract class CopixZone {
    /**
    * Si le cache est actif
    * @var boolean
    */
    protected $_useCache = false;
    
    /**
    * nom des parametres de la zone permettant de l'identifiant de façon unique
    * @var array
    */
    protected $_cacheParams = array ();

    /**
    * Paramètres d'exécution passés à la zone.
    */
    protected $_params = array ();

    /**
    * Nettoie le cache de la zone $name avec pour paramètres $params
    * @param string $pName l'identifiant de la zone à effacer.
    * @param array $pParams tableau des paramètres de la zone. 
    * @static
    */
    public static function clear ($pName, $pParams = array ()){
       $zoneObject = self::_create ($pName);
       $zoneObject->_clear ($pParams);
       //On sait que createZone place le contexte d'exécution de la zone.
       CopixContext::pop ();    	
    }
    
    /**
     * Demande l'exécution d'une zone d'identifiant $name avec ses paramètres $params
     * @param string $pName l'identifiant de la zone à afficher
     * @param array $pParams les paramètres d'exécution de la zone.  
     * @return string le contenu de la zone
     */
    public static function process ($pName, $pParams = array ()){
       $zoneObject = self::_create ($pName);
       $content = $zoneObject->_process ($pParams);
       //On sait que createZone place le contexte d'exécution de la zone.
       CopixContext::pop ();
       return $content;    	
    }

	/**
    * Creation d'un objet zone d'identifiant $pName
    * @param string $name le nom de la zone à instancier.
    * @private
    */
	private static function _create ($pName){
		//Récupération des éléments critiques.
		$fileInfo = new CopixModuleFileSelector ($pName);
		CopixContext::push ($fileInfo->module);

		//Récupère le nom du fichier en fonction du module courant.
		$fileName = $fileInfo->getPath(COPIX_ZONES_DIR). strtolower($fileInfo->fileName) . '.zone.php';

		//inclusion du fichier.
		Copix::RequireOnce ($fileName);
		$objName = 'Zone'.$fileInfo->fileName;
		return new $objName ();
	}

    /**
    * Méthode qui calcul le contenu de la zone en fonction de ses paramètres.
    *  Choisi entre le cache et la génération du contenu
    * 
    * @param array  $pParams les paramètres de contexte pour la zone. (généralement le contenu de l'url)
    * @return   string  le contenu de la zone
    * @access private
    */
    protected function _process ($pParams){
        $this->_params = $pParams;
        $contents = '';

        if ($this->_useCache){
            $module = CopixContext::get ();
            if (CopixCache::exists ($this->_makeId (), 'zones|'.$module.get_class($this))) {
            	$contents = CopixCache::read ($this->_makeId (), 'zones|'.$module.get_class($this));
            } else {
                if ($this->_createContent ($contents)){
                	CopixCache::write ($this->_makeId (), 'zones|'.$module.get_class($this), $contents);
                }
            }             
        }else{
            $this->_createContent ($contents);
        }
        return $contents;
    }

    /**
    * Méthode qui efface le cache de la zone
    * @param array  $pParams les paramètres de contexte pour la zone.
    * @return   boolean  si tout s'est bien passé
    * @access private
    */
    protected function _clear ($pParams){
        $this->_params = $pParams;
        if ($this->_useCache){
            $module = CopixContext::get ();
            CopixCache::clear ($this->_makeId (), 'zones|'.$module.get_class ($this));
        }
        return true;
    }

    /**
    * Méthode de création de contenu pour la zone.
    *
    * Contient le processus de récupération et de création de contenu a partir des paramètres donnés.
    * C'est cette méthode qui sera invoquée par _process pour créer le contenu
    *  s'il n'existe pas en cache
    * @param string   $toReturn   contient le contenu de la zone, à recuperer aprés appel de la methode
    * @return boolean   indique si on peut mettre le contenu généré en cache ou pas
    * @access protected
    */
    abstract protected function _createContent (&$toReturn);

    /**
    * Création de l'identifiant à partir des paramètres de la zone.
    * @return mixed un ensemble d'éléments constituant l'identifiant unique de cache pour la zone
    * @access private
    */
    protected function _makeId (){
        $toReturn = array ();
        foreach ($this->_cacheParams as $key){
            $toReturn[$key] = isset ($this->_params[$key]) ? $this->_params[$key] : null;
        }
        return $toReturn;
    }

    /**
    * Récupère la valeur d'un paramètre donné
    * @param string $pParamName le nom du paramètre à récupérer
    * @param mixed $pParamDefaultValue La valeur par défaut à retourner si le paramètre n'existe pas
    * @return mixed the param value
    */
    public function getParam ($pParamName, $pParamDefaultValue=null){
       return array_key_exists ($pParamName, $this->_params) ? $this->_params[$pParamName] : $pParamDefaultValue;
    }

    /**
     * Retourne le tableau entier de paramètre de la zone
     * @return array le tableau _params
     */
    public function asArray () {
        return $this->_params;
    }
    
    /**
     * Création d'un contenu à partir d'un PPO
     * @param 	CopixPPO	$pPPO	le PPO à utiliser
     * @param	string		$pTemplatename	Le template ou rapprocher le PPO
     * @return string	le contenu rapproché 
     */
    protected function _usePPO ($pPPO, $pTemplateName){
    	$tpl = new CopixTpl ();
    	$tpl->assign ('ppo', $pPPO);
    	return $tpl->fetch ($pTemplateName);
    }
}
?>