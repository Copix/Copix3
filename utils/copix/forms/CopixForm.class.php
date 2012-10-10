<?php
/**
 * @package		copix
 * @subpackage	forms
 * @author		Salleyron Julien, Croës Gérald
 * @copyright	CopixTeam
 * @link			http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 * @experimental
 */


/**
 * Classe principale pour CopixForm
 * @package		copix
 * @subpackage	forms
 */
class CopixForm extends CopixParameterHandler {
	
	//Variables sauvé en session
	protected $_id = null;
	
	protected $_fields = array ();
	
	protected $_record = null;
	
	protected $_arPkValue = array ();

	protected $_formUrl = null;
	
	protected $_onSuccess = null;
	
	protected $_validRecord = false;
	
	protected $_datasourceName = null;
	
	protected $_datasourceParams = null;
	
	protected $_formErrors = null;
	
	protected $_edit = true;
	
	protected $_arPk = array ();
	
	protected $_errorMode = false;
	
	protected $_datasource = null;
	
	protected $_renderer = null;
	
	protected $_mode = null;
	
	protected $_autoParamsIsInit = false;
	
	protected $_pk;
	
	protected $_savedRecord = array ();
	
	public function setPk ($pPks) {
		$this->_pk = $pPks;
		return $this;
	}
	
	/**
	 * Test les droits d'edition
	 *
	 * @return bool
	 */
	public function getEditCredential () {
		if (is_bool ($this->_edit)) {
			return $this->_edit;
		} else {
			return _currentUser ()->testCredential ($this->_edit);
		}
	}
	
	/**
	 * Attribuer les droits d'edition
	 *
	 * @param mixed $pEdit soit une chaine soit un boolean
	 * @return CopixForm $this
	 */
	public function setEditCredential ($pEdit) {
		$this->_edit = $pEdit;
		return $this;
	}
	
	/**
	 * Constructeur qui stock l'id du formulaire
	 *
	 * @param string $pId L'id mis en session pour le formulaire
	 */
	public function __construct ($pId) {
		$this->_id = $pId;
	}
	
	/**
	 * Permet le tri des variables stockées en session
	 *
	 */
	public function __sleep () {
		return array ('_id', '_fields', '_record', '_arPkValue', '_formUrl', '_onSuccess', '_validRecord', '_datasourceName', '_datasourceParams', '_formErrors', '_savedRecord');
	}

	/**
	 * Récupère dans le request si on est sur la page a cause d'une redirection car erreur
	 *
	 */
	public function __wakeup () {
		if (_request ('error_'.$this->_id) != null) {
			$this->_errorMode = true;
		}
	}

	/**
	 * Permet de savoir si le formulaire en cours correspond a un record du datasource
	 *
	 * @return boolean
	 */
	public function isValidRecord () {
		return $this->_validRecord;
	}
	
	/**
	 * Attribu l'url de redirection si tout c bien passé
	 *
	 * @param string $pUrl
	 */
	public function setOnSuccessUrl ($pUrl) {
	    if ($pUrl == null) {
	        $pUrl = '#';
	    }
		$this->_onSuccess = _url ($pUrl);
	}
	
	/**
	 * Récupère l'url de redirection
	 *
	 * @return string
	 */
	public function getOnSuccessUrl () {
		return $this->_onSuccess;
	}
	
	/**
	 * Attache tout les champs présent dans le datasource
	 *
	 */
	public function attachDatasourceField () {
		foreach ($this->getDatasource()->getFields () as $datasource_field) {
			if (!isset ($this->_fields[$datasource_field->fieldName])) {
				if (is_string($datasource_field)) {
					$this->attachField($datasource_field, CopixFieldFactory::get ('varchar'));
				} else {
					
					$params = array ();
					if (isset ($datasource_field->name)) {
						$params['label'] = $datasource_field->name;
					}
					if (isset ($datasource_field->edit)) {
						$params['edit'] = $datasource_field->edit;
					}
					if (!isset($datasource_field->type)) {
						$datasource_field->type = 'varchar';
					}
					switch ($datasource_field->type) {
						case 'autoincrement':
							$this->attachField($datasource_field->fieldName, CopixFieldFactory::get ('hidden'), $params);
							break;
						default:
							$this->attachField($datasource_field->fieldName, CopixFieldFactory::get ('varchar'), $params);
							break;
					}
				}
			}
		}
	}
	
	public function getId () {
		return $this->_id;
	}
	
	/**
	 * Ajoute des messages des erreurs sur le formulaire ou sur les champs qui le compose
	 *
	 * @param mixed $pErrors soit une chaine (sera attribué au formulaire) 
	 *                       soit un tableau clé=>valeur 
	 *                       la clé etant un champs
	 *                       la valeur etant le message
	 *                       si le champs n'existe pas l'erreur est ajouté 
	 *                       pour le formulaire
	 */
	public function setErrors ($pErrors) {
		$this->_formErrors = new CopixErrorObject();
		foreach ($pErrors as $key=>$error) {
			if (($field = $this->getField ($key)) !== false) {
				$field->addError ($error);
			} else {
				$this->_formErrors->addErrors($error);
			}
		}
	}
	
	public function getFormErrors () {
		return $this->_formErrors;
	}
	
	/**
	 * Retourne si on est dans le cas d'une erreur
	 *
	 * @return boolean
	 */
	public function errorMode () {
		return $this->_errorMode;
	}
	
	/**
	 * Attache un champ au formulaire
	 *
	 * @param    string       $pName Nom du champs
	 * @param    ICopixField  $pField un type de champ
	 * @param    array        $pParams liste de paramètre du champ
	 * @return   CopixForm
	 */
	public function attachField ($pName, ICopixField $pField, $pParams = array ()) {
		if (!isset ($this->_fields[$pName])) {
			$this->_fields[$pName] = new CopixFieldContainer($pName, $pParams);
		}
		$this->_fields[$pName]->setParams ($pParams);
		$this->_fields[$pName]->setField ($pField);
		$this->_fields[$pName]->setFormId ($this->_id);
		$this->_fields[$pName]->fillFromRecord ($this->_record);
		return $this;
	}
	
	/**
	 * Rempli la liste des champs depuis le request
	 *
	 * @return CopixForm $this
	 */
	public function fillFromRequest () {
		$this->_record = new stdClass();
		foreach ($this->_fields as $field) {
			$field->fillFromRequest ();
			$field->fillRecord ($this->_record);
			if ($this->getDatasource () !== false) {
				if ($this->_validRecord) {
					foreach ($this->_arPkValue as $key=>$value) {
						$this->_record->$key = $value;
					}
				}
			}
		}
		$this->_autoParamsIsInit = true;
		return $this;
	}
	
	/**
	 * Retourne le HTML de tout les champs
	 *
	 * @param string $pTemplate un template ou afficher les fields
	 * @return string le HTML
	 */
	public function getAllHTML ($pTemplate = 'copix:templates/copixform.tpl') {
		$tpl = new CopixTpl ();
		$tpl->assign ('fields', $this->_fields);
		return $tpl->fetch ($pTemplate);
	}
	
	/**
	 * Retourne du HTML pour un champs en particulier
	 *
	 * @param string $pName Nom du champs
	 * @param string $pKind quel morceau de l'affichage d'un champs all, labl, field, errors
	 * @return string le HTML
	 */
	public function getHTML ($pName, $pKind = 'all') {
		$toReturn = array ();
		if (isset ($this->_fields[$pName])) {
			if ($pKind == 'all' || $pKind == 'label') { 
				$toReturn[] = $this->_fields[$pName]->getLabel ();
			}
			if ($pKind == 'all' || $pKind == 'field') { 
				$toReturn[] = $this->_fields[$pName]->getHTML ();
			}
			if ($pKind == 'all' || $pKind == 'errors') {
				$toReturn[] = $this->_fields[$pName]->getErrors ();
			}
		}
		return implode (' ', $toReturn);
	}
	
	/**
	 * Retourne un champs (type CopixFieldContainer
	 *
	 * @param string $pName le nom du champs
	 * @return CopixFieldContainer 
	 */
	public function getField ($pName) {
		$this->initAutoParams ();
		if (isset ($this->_fields[$pName])) {
			return $this->_fields[$pName];
		}
		return false;
	}
	
	public function getRenderer () {
		//On enregistre l'url du formulaire en enlevant le paramètre d'erreur si il est présent
		$this->_formUrl = CopixUrl::removeParams (_url ('#'), array ('error_'.$this->_id));
		
		//Instanciation du renderer
		if ($this->_renderer == null) {
			$this->initAutoParams ();
			$this->_renderer = new CopixFormRenderer ($this);
		}
		
		return $this->_renderer;
	}
	
	public function initAutoParams () {
		if (!$this->_autoParamsIsInit) {
			if ($this->getParam ('submit', true)) {
			    if (!isset($this->_fields['submit'])) {
				    $this->attachField ('submit', _field ('submit'));
			    }
			}
			
			//On mets les reset ect... ici, a voir si c'est viable ou pas
			$reset = $this->getParam('reset', true);
			$data  = $this->getParam('data', 'auto');
			$force = false;
			$fill  = false;
			switch ($data) {
				case 'session':
					$this->_mode = 'session';
					$reset = false;
					break;
				case 'none':
					$this->_mode = 'create';
					$reset = true;
					break;
				case 'record':
				default:
					$this->_mode = 'edit';
					$fill = true;
			}
			if ($reset) {
				$this->reset ($force);
			}
			if ($fill) {
				if (!$this->fillFromRecord($this->_pk)) {
						$this->_mode = 'create';
				}
			}
			$this->_autoParamsIsInit = true;
		}
	}
	
	public function getMode () {
		$this->initAutoParams ();
		return $this->_mode;
	}
	
	
	
	/**
	 * Retourne la dernière url ou a été affiché le formulaire
	 *
	 * @param array $pParams paramètre a rajouter a l'url
	 * @return string la dernière url du formulaire avec les paramètres passé
	 */
	public function getFormUrl ($pParams = array ()) {
		return _url ($this->_formUrl, $pParams);
	}
	
	public function addConditions ($pDatasource) {
		foreach ($this->_fields as $field) {
			$field->addConditions ($pDatasource);
		}
	}
	
	/**
	 * Remets a zero les données du formulaire
	 *
	 * @param string $pForce Force la remise a zero meme si on est en erreur
	 * @return CopixForm $this
	 */
	public function reset ($pForce = false) {
		if (!$this->errorMode() || $pForce) {
			$this->_formErrors = new CopixErrorObject();
			$this->_record = new stdClass();
			foreach ($this->_fields as $field) {
				$field->reset ();
			}
		}
		return $this;
	}
	
	/**
	 * Rempli le formulaire grace au record courant
	 * De plus cherche a trouvé le record courant
	 * On prend en priorité les valeurs de clé primaire dans le paramètre $pData
	 * ensuite on regarde dans le request
	 * Si toutes les clés primaires ne sont pas validés nous ne sommes pas 
	 * en mode validRecord
	 *
	 * @param array $pData tableau contenant les id
	 * @return CopixForm $this
	 */
	public function fillFromRecord ($pData = array ()) {
		if (!$this->errorMode() && $this->getDatasource () !== false) {
			if (!is_array ($pData)) {
				$arData = array ();
				$arData[$this->_arPk[0]] = $pData;
				$pData = $arData;
			}
		
			foreach ($this->_arPk as $pk) {
				if (isset ($pData[$pk])) {
					$this->_arPkValue[$pk] = $pData[$pk];
				} else {
					if (_request ($pk) == null) {
						$this->_validRecord = false;
						return $this->_validRecord;
					}
					$this->_arPkValue[$pk] = _request ($pk); 
				}
			}
			if (($this->_record = call_user_func_array (array ($this->getDatasource (), 'get'), $this->_arPkValue)) === false) {
				$this->_validRecord = false;
				return $this->_validRecord;
			} else {
			    $this->_savedRecord[serialize ($this->_arPkValue)] = $this->_record;
			}
			
			foreach ($this->_fields as $field) {
				$field->fillFromRecord ($this->_record);
			}
			$this->_validRecord = true;
		}
		return $this->_validRecord;
	}
	
	/**
	 * Renvoi le footer du formulaire
	 *
	 * @return string le HTML
	 */
	public function getFooter () {
		return '</form>';
	}
	
	/**
	 * Lance les différents check du formulaire
	 *
	 * @return bool true ou false
	 */
	public function check () {
	    //On test la concurrence d'accès
	    if ($this->_validRecord && $this->getParam('concurrence', true)) {
	        CopixLock::lock(serialize ($this->_arPkValue));
	        $temprecord = call_user_func_array (array ($this->getDatasource (), 'get'), $this->_arPkValue);
	        if ($temprecord != $this->_savedRecord[serialize ($this->_arPkValue)]) {
	            $this->fillFromRecord ($this->_arPkValue);
	            $this->_formErrors = new CopixErrorObject('L\'enregistrement a echoué en raison d\'une concurrence d\'accès. Veuillez refaire vos modifications');
	            CopixLock::unlock(serialize ($this->_arPkValue));
	            return false;
	        }
	    }
	    
		$toReturn = true;
		foreach ($this->_fields as $field) {
			//Test du datasource, a termes il faudrait voir pour le gérer avec des validators
			
			//Code presque définitif qui devrait rajouter les validators de la dao sur les validators des champs
			/*
			if ($this->getDatasource () !== false) {
				$field->getField ()->attach ($this->getDatasource ()->getValidators ($field->_field));
			}
			//*/
			
			if ($field->check () !== true) {
				$toReturn = false;
			}
		}
		if ($this->getDatasource () !== false) {
			$this->_formErrors = null;
			if (($errors = $this->getDatasource ()->check ($this->_record)) !== true) {
				$toReturn = false;
				$this->_formErrors = new CopixErrorObject($errors);
			}
		}
		if ($this->_validRecord && $this->getParam('concurrence', true)) {
		    CopixLock::unlock(serialize ($this->_arPkValue));
		}
		return $toReturn;
	}
	
	/**
	 * Récupère le record courant
	 *
	 * @return mixed le record
	 */
	public function getRecord () {
		return $this->_record;
	}
	
	/**
	 * Permet d'enregistrer le datasource
	 *
	 * @param array $pDatasourceParams liste des paramètres du datasource
	 * @param string $pDatasourceName Nom du datasource, par défaut C une dao
	 * @return mixed false si le datasource a echoué sinon renvoi le datasource
	 */
	public function setDatasource ($pDatasourceName, $pDatasourceParams = array ()) {
		$this->_datasourceName   = $pDatasourceName;
		$this->_datasourceParams = $pDatasourceParams;
		if (!isset($pDatasourceParams['autofill']) || $pDatasourceParams['autofill'] !== false) {
			$this->attachDatasourceField ();
		}
		return $this;
	}
	
	public function setDao ($pDao, $pParams = array ()) {
		$pParams['dao'] = $pDao;
		return $this->setDatasource ('dao', $pParams);
	}
	
	/**
	 * Renvoi le datasource
	 *
	 * @return ICopixDatasource le datasource
	 */
	public function getDatasource () {
		if ($this->_datasource !== null) {
			return $this->_datasource;
		}
		
		if ($this->_datasourceName === null) {
			return false;
		}
		$this->_datasource = CopixDatasourceFactory::get ($this->_datasourceName, $this->_datasourceParams);
		$this->_arPk = $this->_datasource->getPk ();
		return $this->_datasource;
	}
	
	/**
	 * Lance la sauvegarde du record
	 * Si on avais un record valid on fais un update sinon on fais un insert 
	 *
	 */
	public function save () {
		if ($this->getDatasource () !== false) {
			if ($this->_validRecord) {
				$this->getDatasource ()->update ($this->_record);
			} else {
				$this->_record = $this->getDatasource ()->insert ($this->_record);
			}
			return $this->_record;
		}
		return null;
	}

	public function getFields () {
		return $this->_fields;
	}
	
	/**
	 */
	protected function _reportErrors ($pErrors){
		print_r ($pErrors);
		exit;
	}
}


class CopixFormRenderer {

	private $_form = null;
	
	public function __construct ($pForm) {
		$this->_form = $pForm;
	}
	
	public function __toString () {
		return $this->all ();
	}
	
	public function all () {
		$toReturn  = $this->header ();
		$toReturn .= $this->errors ();
		$toReturn .= $this->body ();
		$toReturn .= $this->footer ();
		return $toReturn;
	}
	
	public function header ($pParams = array ()) {
		if (is_string($pParams)) {
			$pParams = array ('action'=>$pParams);
		}
		
		if(!isset ($pParams['action'])) {
			$pParams['action'] = 'generictools|copixforms|newForm';
		}
		
		$more = '';
		if (isset ($pParams['uploadedfile']) && $pParams['uploadedfile']) {
			$more = ' enctype="multipart/form-data" ';
		}
		
		return '<form name="'.$this->_form->getId ().'" id="'.$this->_form->getId ().'" '.$more.' method="POST" action="'._url ($pParams['action'], array ('currentForm'=>$this->_form->getId ())).'" >';
		
	}
	
	public function footer () {
		return '</form>';
	}
	
	public function body ($pTemplate = 'copix:templates/copixform.tpl') {
		$tpl = new CopixTpl ();
		$tpl->assign ('fields', $this->_form->getFields());
		return $tpl->fetch ($pTemplate);
	}
	
	public function field ($pName, $pParams = array ()) {
		$toReturn = array ();
		if (is_string ($pParams)) {
			$pParams['kind'] = array ($pParams);
		}
		if (!isset($pParams['kind'])) {
			$pParams['kind'] = array ('all');
		}
		
		if (!is_array ($pParams['kind'])) {
		    $pParams['kind'] = array ($pParams['kind']);
		}
		
		$field = $this->_form->getField ($pName);
		if ($field != null) {
		    foreach ($pParams['kind'] as $kind) {
    			if ($kind == 'all' || $kind == 'label') {
    				$toReturn[] = $field->getLabel ();
    			}
    			if ($kind == 'all' || $kind == 'input') {
    				$toReturn[] = $field->getHTML ();
    			}
    			if ($kind == 'all' || $kind == 'errors') {
    			    if ($field->getErrors () != null) {
    				    $toReturn[] = '<span class="fieldError">'.$field->getErrors ().'</span>';
    			    }
    			}
    			if ($kind == 'value') {
    				$toReturn[] = $field->getValue();
    			}
		    }
		}
		return implode (' ', $toReturn);
	}
	
	public function errors () {
		$toReturn = '';
		$errors = $this->_form->getFormErrors ();
		if ($errors != null && count ($errors) > 0) {
			$toReturn = '<div class="errorMessage"><ul><li>'.implode ('</li><li>', $errors->asArray ()).'</li></ul></div>';
		}
		return $toReturn;
	}
}