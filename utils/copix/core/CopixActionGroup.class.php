<?php
/**
 * @package		copix
 * @subpackage	core
 * @author		Croes Gérald
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Classe de transport d'informations (utiliser pour les actions, le transfère de paramètres, etc...)
 * 
 * @package		copix
 * @subpackage	core
 */
class CopixPPO implements ArrayAccess {
	/**
	 * Retourne l'élément où sauvegarder la donnée
	 * 
	 * @param string $propertyName Nom de la propriété à récupérer
	 */
	public function &__get ($propertyName) {
		return $this->$propertyName;
	}
	
	/**
	 * Surcharge de isset pour que les sous éléments "CopixPpo" soient correctement détectés comme vide lorsqu'il y a lieu
	 *
	 * @param string $propertyName
	 * @return boolean
	 */
	public function __isset ($propertyName){
		if ($this->$propertyName instanceof CopixPpo){
			$test = get_object_vars ($this->$propertyName);
			return ! empty ($test);
		}
		return isset ($this->$propertyName); 
	}

	/**
	 * Constructeur
	 * 
	 * @param array $pArInit Tableau de variables pour initialiser le ppo
	 */
	public function __construct ($pArInit = array ()) {
		if (is_array ($pArInit)) {
			foreach ($pArInit as $key => $item) {
				$this->$key = $item;
			}
		}
	}
	
	/**
	 * Implémentation de ArrayAccess, pour la récupération de $pOffset
	 *
	 * @param string $pOffset
	 * @return mixed
	 */
	public function offsetGet ($pOffset) {
		return $this->$pOffset;
	}
	
	/**
	 * Implémentation de ArrayAccess, pour la définition de $pOffset
	 *
	 * @param string $pOffset
	 * @param mixed $pValue
	 */
	public function offsetSet ($pOffset, $pValue) {
		if ($pOffset === null){
			$vars = get_object_vars ($this);
			if (count ($vars) === 0){
				$pOffset = 0;
			}else{
		   		$pOffset = max (array_keys (get_object_vars ($this)))+1;
			}
		}
		$this->$pOffset = $pValue;
	}
	
	/**
	 * Implémentation de ArrayAccess, vérifie l'existance de $pOffset
	 *
	 * @param string $pOffset
	 * @return boolean
	 */
	public function offsetExists ($pOffset) {
		return isset ($this->$pOffset); 
	}
	
	/**
	 * Implémentation de ArrayAccess, supprime $pOffset
	 *
	 * @param string $pOffset
	 */
	public function offsetUnset ($pOffset) {
		$this->$pOffset = null;
	}
	
	/**
	 * En cas de demande d'affichage directe
	 */
	public function __toString (){
		return '';
	}
	
	
	/**
	 * Merge, assigne les propriétés de l'objet en cours avec celles d'un autre objet/tableau/element
	 * Les propriétés existantes ne sont pas ajoutées
	 * 
	 * @param 	mixed $pToMerge Objet à ajouter
	 * @return  CopixPPO 
	 */
	public function merge ($pToMerge){
		if (is_array ($pToMerge)){
			$pToMerge = new CopixPPO ($pToMerge);
		}elseif (is_object ($pToMerge)){
			if (!( $pToMerge instanceof CopixPPO)){
				$pToMerge = new CopixPPO (get_object_vars ($pToMerge));
			}			
		}else{
			$pToMerge = array ($pToMerge);
		}

		foreach (get_object_vars ($pToMerge) as $name=>$prop){
			if (! isset ($this->$name)){
				$this->$name = $prop;
			}
		}
		return $this;
	}
}

/**
 * CopixRecursivePpo
 * Identique à CopixPpo sauf qu'il permet l'utilisation des appels imbriqués sans notice du style 
 *  $ppo['test']->foo->foo['test'][] = 1
 * Attention if ($ppo->unsetValue){} vaudra vrai car chaque valeur non définie retournera un objet vide
 */
class CopixRPPO extends CopixPpo {
	/**
	 * Retourne l'élément où sauvegarder la donnée
	 * 
	 * @param string $propertyName Nom de la propriété à récupérer
	 */
	public function &__get ($propertyName) {
		$this->$propertyName = new CopixRPpo ();
		return $this->$propertyName;
	}
}


/**
 * Classe de base pour les contrôlleurs
 * 
 * @package		copix
 * @subpackage	core
 */
class CopixActionGroup {

	/**
	 * Instances crées
	 * 
	 * @ar array
	 */
	static private $_instances = array ();

	/**
	 * Extraction du chemin à partir de l'identifiant donné, de la forme module|ag::methName
	 * Si aucun module n'est donné, on utilise le contexte courant.
	 * 
	 * @param string $pAGId Identifiant d'action que l'on souhaite exécuter
	 * @return object Objet avec 3 propriétés : module, actiongroup et method
	 * @throws Exception
	 */
	private static function _extractPath ($pAGId) {
		$extract = explode ('|', $pAGId);
		if (count ($extract) == 1) {
			return CopixActionGroup::_extractPath (CopixContext::get () . '|' . $pAGId);
		}

		$extractMethod = explode ('::', $extract[1]);
		if (count ($extractMethod) !== 2) {
			throw new Exception (_i18n ('copix:copix.error.wrongActionGroupPath', $pAGId));
		}

		$extracted = new StdClass ();
		$extracted->module = strtolower ($extract[0] === '' ? null : $extract[0]);
		$extracted->actiongroup = $extractMethod[0];
		$extracted->method = $extractMethod[1];

		return $extracted;
	}

	/**
	 * Récupère l'instance de l'actiongroup donné.
	 * 
	 * @param object $pActionGroupDescription Description de l'actiongroup dont on souhaite récupérer l'instance. Doit avoir 2 propriétés : module et actiongroup
	 * @return CopixActionGroup
	 * @throws Exception
	 */
	public static function instance ($pActionGroupDescription) {
		$actionGroupID = $pActionGroupDescription->module . '|' . $pActionGroupDescription->actiongroup;

		if (!isset (self::$_instances[$actionGroupID])) {
			$execPath = CopixModule::getPath ($pActionGroupDescription->module);
			$fileName = $execPath . COPIX_ACTIONGROUP_DIR . strtolower (strtolower ($pActionGroupDescription->actiongroup)) . '.actiongroup.php';
			if (!Copix::RequireOnce ($fileName)) {
				throw new Exception (_i18n ('copix:copix.error.load.actiongroup', $fileName));
			}
			// nom des objets/méthodes à utiliser.
			$objName = 'ActionGroup' . $pActionGroupDescription->actiongroup;
			self::$_instances[$actionGroupID] = new $objName ();
		}

		return self::$_instances[$actionGroupID];
	}

	/**
	 * Execution d'une action
	 * 
	 * @param string $pPath Identifier 'module|AG::method'
	 * @param array $pVars Paramètres
	 * @param bool $pFromDesc Indique si l'on viens d'un fichier de description (à raison de compatibilité)
	 * @return CopixActionReturn
	 * @todo Supprimer $pFromDesc dans les prochaines versions de copix
	 * @throws Exception
	 */
	public static function process ($pPath, $pVars = array (), $pFromDesc = false) {
		$extractedPath = CopixActionGroup::_extractPath ($pPath);
		if ($extractedPath === null) {
			throw new Exception (_i18n ('copix:copix.error.load.actiongroup', $pPath));
		}

		$actiongroup = CopixActionGroup::instance ($extractedPath);
		$methName = ($pFromDesc === false) ? 'process' . $extractedPath->method : $extractedPath->method;

		if (!method_exists ($actiongroup, $methName)) {
			$methName = 'otherAction';
		}

		// on défini le module
		CopixContext::push ($extractedPath->module);
		foreach ($pVars as $varName => $varValue) {
			CopixRequest::set ($varName, $varValue);
		}

		// on essaye d'exécuter l'action
		try {
			if (($result = $actiongroup->_beforeAction ($extractedPath->method)) === null) {
				if ($methName == 'otherAction') {
					$toReturn = $actiongroup->$methName ($extractedPath->method);
				} else {
					$toReturn = $actiongroup->$methName ();
				}
			} else {
				$extractedPath->method = '_beforeAction';
				$toReturn = $result;
			}
				
			if (($result = $actiongroup->_afterAction ($extractedPath->method, $toReturn)) !== null) {
				$toReturn = $result;
			}

			// si on n'a pas fait de return valide
			if (!($toReturn instanceof CopixActionReturn)) {
				throw new CopixException (_i18n ('copix:copix.error.invalidActionReturn', array (gettype ($toReturn))));
			}
				
		} catch (Exception $e) {
			try {
				$toReturn = $actiongroup->_catchActionExceptions ($e, $extractedPath->method);
			} catch (Exception $e) {
				// on est obligé de relancer un try/catch pour pouvoir faire un pop du contexte
				CopixContext::pop ();
				throw $e;
			}
		}
		CopixContext::pop ();
		return $toReturn;
	}

	/**
	 * Donne l'opportunité à l'actiongroup de gérer des éléments communs avant chaque actions. Destinée à être surchargée
	 * 
	 * @param string $pActionName Nom de l'action
	 * @return mixed
	 */
	protected function _beforeAction ($pActionName){
		return $this->beforeAction ($pActionName);
	}
	
	/**
	 * Donne l'opportunité à l'actiongroup de gérer des éléments communs avant chaque actions. Destinée à être surchargée au besoin
	 * 
	 * @param string $pActionName Nom de l'action
	 * @return mixed
	 */
	protected function beforeAction ($pActionName) {}

	/**
	 * Donne l'opportunité à l'actiongroup de gérer des éléments communs après chaque action. Destinée à être surchargée au besoin
	 * 
	 * @param string $pActionName Nom de l'action
	 * @param CopixActionReturn Retour de l'action (processXXX)
	 * @return mixed
	 */
	protected function _afterAction ($pActionName, $pActionReturn){
		return $this->afterAction ($pActionName, $pActionReturn);
	}
	
	/**
	 * Donne l'opportunité à l'actiongroup de gérer des éléments communs après chaque action. Destinée à être surchargée au besoin
	 * 
	 * @param string $pActionName Nom de l'action
	 * @param CopixActionReturn Retour de l'action (processXXX)
	 */
	protected function afterAction ($pActionName, $pActionReturn) {}

	/**
	 * Donne la possibilité à chaque actiongroup de traiter les erreurs
	 * 
	 * @param Exception $pException Exception à traiter
	 * @throws Exception
	 */
	protected function _catchActionExceptions ($pException){
		return $this->catchActionExceptions ($pException);
	}
	
	/**
	 * Donne la possibilité à chaque actiongroup de traiter les erreurs
	 * 
	 * @param Exception $pException Exception à traiter
	 * @throws Exception
	 */
	protected function catchActionExceptions ($pException) {
		throw $pException;
	}

	/**
	 * Si l'action n'est pas gérée par l'actiongroup actuel, c'est cette méthode qui récupère le traitement
	 * 
	 * @return CopixActionReturn
	 */
	protected function otherAction () {
		if (CopixConfig::instance ()->notFoundDefaultRedirectTo !== false) {
			return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get (CopixConfig::instance ()->notFoundDefaultRedirectTo));
		}
		return new CopixActionReturn (CopixActionReturn::HTTPCODE, CopixHTTPHeader::get404 (), _i18n ('copix:copix.error.404'));
	}
}

/**
 * Contient les infos de retour des actions d'un coordinateur de page *
 * Cet objet permet à CopixController de savoir quoi faire après une action.
 * Il contient un code retour, et des données associées à ce code retour.
 * Dans les traitements par défaut, ce code est un entier.
 *
 * <code>
 * $tpl= new CopixTpl ();
 * //...
 * return new CopixActionReturn (CopixActionReturn::PPO, $ppo, 'html');
 * </code>
 * @package		copix
 * @subpackage	core
 */
class CopixActionReturn {
	/**
	 * Code de retour. vaut une des constantes COPIX_AR_*
	 * 
	 * @var int
	 */
	public $code = null;

	/**
	 * Paramètre pour le traitement du retour. Sa nature dépend du code retour
	 * 
	 * @var mixed
	 */
	public $data = null;

	/**
	 * Paramètre supplémentaire pour le traitement du retour. Sa nature et sa présence dépend du code retour
	 * 
	 * @var mixed
	 */
	public $more = null;

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
	 * 
	 * @param int $pCode Code (Constante de cette même classe)
	 * @param mixed $pData Paramètres (template / url / ...)
	 * @param mixed $pMore Paramètres supplémentaires
	 */
	public function __construct ($pCode, $pData = null, $pMore = null) {
		$this->data = $pData;
		$this->more = $pMore;
		$this->code = $pCode;
	}
}

/**
 * Création d'une table pour gérer les onglets
 * 
 * @package		copix
 * @subpackage	core
 */
class CopixTabActionGroup extends CopixActionGroup {

    /**
     * Liste des onglets
     * 
     * @var array
     */
    protected $_listTabs = array ();

    /**
     * Onglet actuel
     * 
     * @var string
     */
    protected $_currentTab = null;

    /**
     * Lien vers le template des onglets
     * 
     * @var string
     */
    protected $_tpl = 'copix:templates/onglets.tpl';

    /**
     * Liste des libellés de méthodes
     * 
     * @var array
     */
    protected $_arLibelle;

    /**
     * Constructeur
     */
    public function __construct () {
        $arObjectMethods = get_class_methods (get_class ($this));
        // Préinitiatilise les actions
        foreach ($arObjectMethods as $method) {
            if (ereg ("^process", $method) && $method != "process") {
                $objTab = new stdClass ();
                $objTab->url = _url (substr ($method, 7));
                $objTab->caption = isset ($this->_arLibelle[$method]) ? $this->_arLibelle[$method] : $method;
                $objTab->enable = 1;
                $this->_listTabs[] = $objTab;
            }
        }
    }

    /**
     * Executée avant le processXXX. Sauvegarde du nom de l'onglet courant.
     *
     * @param string $pActionName Nom de l'action
     * @return mixed
     */
    public function _beforeAction ($pActionName) {
        // récupération de l'onglet courant
        $this->_currentTab = $pActionName;
        return parent::_beforeAction ($pActionName);
    }

    /**
     * Executée après processXXX. En fonction de l'onglet courant, on affiche les données dans le bon template.
     *
     * @param string $pActionName Nom de l'action
     * @param CopixActionReturn $pReturn Retour du processXXX
     */
    public function _afterAction ($pActionName, $pReturn) {
        if ($pReturn->code == CopixActionReturn::PPO) {
            $tpl = new CopixTpl ();
            $tpl->assign ('ppo', $pReturn->data);

            $ppo = new CopixPPO ();
            $ppo->TITLE_PAGE = $this->_TITLE_PAGE;

            $ppo->main = $tpl->fetch ($pReturn->more);

            $ppo->currentTab = $this->_currentTab;
            $ppo->arTabs = $this->_listTabs;
            if (($pReturn = parent::_afterAction ($pActionName, $pReturn)) !== null) {
                return $pReturn;
            }
            return _arPPO ($ppo, $this->_tpl);
        }

        return parent::_afterAction ($pActionName, $pReturn);
    }
}

/**
 * ActionGroup qui gère les formulaires
 * 
 * @package		copix
 * @subpackage	core
 */
class CopixFormActionGroup extends CopixActionGroup {
	
	/**
	 * Formulaire en cours de gestion
	 * 
	 * @var object
	 */
	private $_form;
	
	/**
	 * Surcharge du beforeAction qui instancie le _form et qui met dans le CopixRequest tout les champs du form (form|champs)
	 */
	public function _beforeAction ($pActionName) {
		$this->_form = CopixFormFactory::get (_request ('form_id'));
		$validUrl  = _request('onValid');
        $urlParams = array ();
   	    $urlParams['mode_'._request('form_id')] = 'view';
   	    $urlParams['error_'._request('form_id')]=false;
   	    $urlParams['form_id'] = _request('form_id');
   	    $urlParams['url'] = _request('url');
        $this->_form->doValid();
       	$this->_form->doRecord();
	    foreach ($this->_form->getFields () as $fields) {
			foreach ($fields->getDatas () as $field) {
				CopixRequest::set ('form|' . $field->field, $field->value);
			}
		}
	}
	
	/**
	 * Catch les exceptions de check de formulaire générées dans le process de l'actiongroup
	 *
	 * @param object $pException Exception
	 * @return object CopixActionReturn
	 */
	public function _catchActionExceptions ($pException) {
		if ($pException instanceof CopixFormCheckException) {
			$urlParams['mode_'._request('form_id')] = 'edit';
			$urlParams['error_'._request('form_id')] = true;
			$this->_form->setErrors ($pException->getErrors ());
			return _arRedirect( _url (_request ('url'), $urlParams));
		} else {
			return $this->catchActionExceptions ($pException);
		}
	}
}
?>