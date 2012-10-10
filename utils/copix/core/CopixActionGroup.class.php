<?php
/**
* @package		copix
* @subpackage	core
* @author		Croes Gérald
* @copyright	CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Classe de transport d'informations pour les actions
 * @package   copix
 * @subpackage core
 */
class CopixPPO {
	/**
	 * Les données de l'objet
	 * @var array
	 */
	private $props = array ();

	/**
	 * Retourne l'élément ou sauvegarder la donnée
	 * @param	string	$propertyName	le nom de la propriété à récupérer
	 */
	public function & __get ($propertyName){
		return $this->props[$propertyName];
	}

	/**
 	* Constructeur
  	* @param	array	$pArInit	Tableau de variables pour initialiser le ppo  
	*/	
	public function __construct ($pArInit = array ()){
		if (is_array ($pArInit)){
			foreach ($pArInit as $key=>$item){
				$this->$key = $item;
			}
		}
        }
}

/**
 * Classe de base pour les contrôlleurs
 * @package copix
 * @subpackage core
 */
class CopixActionGroup {

	/**
	 * les instances crées
	 */
	static private $_instances = array ();

	/**
    * Extraction du chemin à partir de l'identifiant donné(de la forme module|ag::methName)
    * Si aucun module n'est donné, on utilise le contexte courant.
    * @param string $pAGId l'identifiant d'action que l'on souhaite exécuter
    */
	private static function _extractPath ($pAGId){
		$extract = explode ('|', $pAGId);
		if (count ($extract) == 1){
			return CopixActionGroup::_extractPath (CopixContext::get ().'|'.$pAGId);
		}

		$extractMethod = explode ('::', $extract[1]);
		if (count ($extractMethod) !== 2){
		   throw new Exception (CopixI18N::get ('copix:copix.error.wrongActionGroupPath', $pAGId));
		}

		$extracted = new StdClass ();
		$extracted->module      = strtolower ($extract[0] === '' ? null : $extract[0]);
		$extracted->actiongroup = $extractMethod[0];
		$extracted->method      = $extractMethod[1];

		return $extracted;
	}
	
	/**
	* Récupère l'instance de l'actiongroup donné
	* @param	object	$pActionGroupDescription	description de l'actiongroup dont on souhaite récupérer l'instance
	* @return	CopixActionGroup	
	*/
	public static function instance ($pActionGroupDescription){
        $actionGroupID = $pActionGroupDescription->module.'|'.$pActionGroupDescription->actiongroup;

        if (! isset (self::$_instances[$actionGroupID])){
            $execPath = CopixModule::getPath ($pActionGroupDescription->module);
			$fileName = $execPath.COPIX_ACTIONGROUP_DIR.strtolower (strtolower ($pActionGroupDescription->actiongroup)).'.actiongroup.php';
			if (! Copix::RequireOnce ($fileName)){
				throw new Exception (CopixI18N::get('copix:copix.error.load.actiongroup', $fileName));
			}
			//Nom des objets/méthodes à utiliser.
			$objName  = 'ActionGroup'.$pActionGroupDescription->actiongroup;
			self::$_instances[$actionGroupID] = new $objName ();
		}
		
		return self::$_instances[$actionGroupID];
	}
	
	/**
    * lancement d'une action
    * @param	string	$path identifier 'module|AG::method'
    * @param	array	$vars parameters
    * @param 	boolean	$pFromDesc	indique si l'on viens d'un fichier de description (à raison 
    * de compatibilité)
    * @todo Supprimer $pFromDesc dans les prochaines versions de copix
    */
	public static function process ($path, $vars = array (), $pFromDesc = false){
		$extractedPath = CopixActionGroup::_extractPath ($path);
		if ($extractedPath === null){
			throw new Exception (CopixI18N::get('copix:copix.error.load.actiongroup', $path));
		}
		
		$actiongroup = CopixActionGroup::instance ($extractedPath);
		$methName    = $pFromDesc === false ? 'process'.$extractedPath->method : $extractedPath->method;
		
		if (!method_exists ($actiongroup, $methName)){
			$methName = 'otherAction';		
		}

		//On défini le module
		CopixContext::push ($extractedPath->module);
		foreach ($vars as $varName=>$varValue){
           CopixRequest::set ($varName, $varValue);			
		}

		//On essaye d'exécuter l'action
		try {
			if (($result = $actiongroup->beforeAction ($extractedPath->method)) === null) {
				if ($methName == 'otherAction'){
	            	$toReturn = $actiongroup->$methName ($extractedPath->method);
	            }else{
					$toReturn = $actiongroup->$methName ();
	            }				
            }else{
            	$extractedPath->method = 'beforeAction';
            	$toReturn = $result;
            }
			
            if (($result = $actiongroup->afterAction ($extractedPath->method, $toReturn)) !== null) {
            	$toReturn = $result;
            }
            
			// si on n'a pas fait de return valide
			if (!($toReturn instanceof CopixActionReturn)) {
				throw new CopixException (_i18n ('copix:copix.error.invalidActionReturn', array (gettype ($toReturn))));
			}
			
		}catch (Exception $e){
			try {
				$toReturn = $actiongroup->catchActionExceptions ($e, $extractedPath->method);
			}catch (Exception $e){
				//On est obligé de relancer un try/catch pour pouvoir faire un pop du contexte
				CopixContext::pop ();
				throw $e;
			}
		}
		CopixContext::pop ();
		return $toReturn;
	}
	
	/**
	 * Donne l'opportunité à l'actiongroup de gérer des éléments communs avant chaque actions
	 */
	protected function beforeAction (){} 
	
	/**
	 * Donne l'opportunité à l'actiongroup de gérer des éléments communs après chaque action
	 */
	protected function afterAction (){}
	
	/**
	 * Donne la possibilité à chaque actiongroup de traiter les erreurs
	 * @param	Exception	$e	l'exception à traiter
	 * @throws	Exception
	 */
	protected function catchActionExceptions ($e){
		throw $e;		
	}
	
	/**
	 * Si l'action n'est pas gérée par l'actiongroup actuel, c'est cette méthode qui récupère le traitement
	 */
	protected function otherAction (){
		if (CopixConfig::instance ()->notFoundDefaultRedirectTo !== false){
			return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get (CopixConfig::instance ()->notFoundDefaultRedirectTo));
		}
		return new CopixActionReturn (CopixActionReturn::HTTPCODE, CopixHTTPHeader::get404 (),'Page introuvable');
	}
}

/**
* Contient les infos de retour des actions d'un coordinateur de page
*
* Cet objet permet à CopixController de savoir quoi faire apres une action.
* Il contient un code retour, et des données associées à ce code retour.
* Dans les traitements par défaut, ce code est un entier.
*
* <code>
*  $tpl= new CopixTpl();
*  //...
*  return new CopixActionReturn (CopixActionReturn::PPO, $ppo, 'html');
* </code>
* @package copix
* @subpackage core
*/
class CopixActionReturn {
    /**
    * Code de retour. vaut une des constantes COPIX_AR_*
    * @var int
    */
    var $code = null;

    /**
    * paramètre pour le traitement du retour. sa nature dépend du code retour
    * @var mixed
    */
    var $data = null;

    /**
    * Paramètre supplémentaire pour le traitement du retour.
    * Sa nature et sa présence dépend du code retour
    * @var mixed
    */
    var $more = null;

    /**
     * Affichage dans le template principal
     */
    const DISPLAY = 1;
    /**
     * Une erreur est survenue
     */
    const ERROR = 2;
    /**
     * Redirection à une url
     */
    const REDIRECT = 3;

    /**
     * Affichage d'un fichier statique
     */
    const STATIC_FILE = 5;
    /**
     * Rien ne sera fait de plus
     */
    const NONE = 6;
    /**
     * Affichage dans un autre template que le template principal défini par défaut 
     */
    const DISPLAY_IN = 7;
    /**
     * Téléchargement d'un contenu à partir d'un fichier
     */
    const FILE = 8;
    /**
     * Affichage d'un contenu binaire à partir d'un fichier
     */
    const CONTENT = 9;
    /**
     * Code HTTP
     */
    const HTTPCODE = 10;
    /**
     * Système "MVC"
     */
    const PPO = 11;

    /**
    * Contruction et initialisation du descripteur.
    * @param int    $pCode      Code (Constantes de cette même classe)
    * @param mixed  $pData      Parameters (template / url / ...)
    * @param mixed  $pMore      Extra parameters
    */
    public function __construct ($pCode, $pData = null, $pMore=null){
        $this->data = $pData;
        $this->more = $pMore;
        $this->code = $pCode;
    }
}

/**
 * Création d'une table pour gérer les onglets 
 */
class CopixTabActionGroup extends CopixActionGroup {
    
    protected $_listTabs = array();
    
    protected $_currentTab;
       
    protected $_tpl = 'copix:templates/onglets.tpl';
    
    protected $_arLibelle;
    
    function __construct (){
        $arObjectMethods = get_class_methods (get_class($this));
        // Préinitiatilise les actions
	    foreach ($arObjectMethods as $method) {
	        if (ereg("^process", $method) && $method != "process") {
	            $objTab = new stdClass();
	            $objTab->url = substr($method, 7);
	            $objTab->caption = isset($this->_arLibelle[$method])?$this->_arLibelle[$method]:$method;
	            $objTab->enable = 1;
	            $this->_listTabs[] = $objTab;
	        }
	    }

    }
    
    function beforeAction ($pActionName){
        // récupération de l'onglet courant
        $this->_currentTab = $pActionName;
    }
    
	public function afterAction ($pActionName, $return){
        if ($return->code == CopixActionReturn::PPO) {
                
                $tpl = new CopixTpl();
                $tpl->assign('ppo',$return->data);

                $ppo = new CopixPPO ();
            	$ppo->TITLE_PAGE = $this->_TITLE_PAGE;
            	
                $ppo->main = $tpl->fetch($return->more);
                
                $ppo->currentTab = $this->_currentTab;
                $ppo->arTabs = $this->_listTabs;
                
                return _arPPO($ppo, $this->_tpl); 
        }            
    }
}
?>