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
 * TODO: i18n
 * TODO Les droits
 * TODO Mettre les propriétés corrects
 * TODO Edit par champ
 * TODO Gérer mieux la phrases de confirm de delete
 */


/**
 * Classe principale pour CopixForm
 * @package		copix
 * @subpackage	forms
 */
class CopixForm {
	//Id du formulaire
	private $_id             = null;
	//Instance du datasource
	private $_datasource     = null;
	//Type de datasource
	private $_datasourceName = null;
	//Liste des paramètres passé au formulaire
	private $_params         = array ();
	//onDelete
	private $_onDelete       = 'generictools|copixforms|delete';
	//mode courant
	private $_mode           = 'view';
	//mode par defaut
	private $_defaultMode    = 'view';
	//Droit de creation
	private $_createRight    = true;
	//Droit d edition
	private $_editRight      = true;
	//Droit d effacement
	private $_deleteRight    = true;
	//Url de redirection apres suppression
	private $_deleteUrl      = null;
	//Valid url
	private $_onValid       = null;
	//Tableau contenant les pk pour ce datasource
	private $_arPkValue      = array ();
	//Mode creation ?
	private $_create         = null;
	//Les champs de type CopixField
	private $_fields         = array ();
	//Url d'action (redirection au submit du formulaire
	private $_action         = null;
	//En cours de delete ?
	private $_toDelete       = false;
	//Tableau contenant les erreurs
	private $_errors         = array();
	//Boolean pour savoir si les boutons on été affiché
	private $_deleteButton   = false;
	private $_editButton     = false;
	private $_submitButton   = false;
	private $_cancelButton   = false;

	//Record courant
	private $_record         = null;

	//Boolean pour savoir si on doit faire l'action d'enregistrement sur le datasource
	private $_mustDoRecord   = false;

	/**
	 * _sleep
	 * on détruit le datasource pour pas le mettre en session
	 * et on remets a null les valeur des boutons
	 */
	public function __sleep () {
		$this->_editButton   = false;
		$this->_deleteButton = false;
		$this->_submitButton = false;
		$this->_cancelButton = false;
		$this->_toDelete     = false;
		if ($this->_datasourceName != 'dao' && $this->_datasourceName != null) {
			$this->_datasource = new CopixSessionObject($this->_datasource, $this->_datasourceName);
		}
		return array_keys(get_object_vars($this));
	}

	/**
	 * _wakeup
	 * on recrée le datasource
	 */
	public function __wakeup () {
		if ($this->_datasourceName != 'dao' && $this->_datasourceName != null) {
			$this->_datasource = $this->_datasource->getSessionObject ();
		}
	}

	/**
	 * Constructeur
	 *
	 * @param string $pId l'identifiant de ce formulaire
	 */
	public function __construct ($pId) {
		$this->_id = $pId;
	}


	/**
	 * Retourne le tableau de champs
	 *
	 * @return array tableau de champs
	 */
	public function getFields () {
		return $this->_fields;
	}

	/**
	 * Attribut des errors
	 *
	 * @param array $pErrors Tableau contenant les erreurs a attribuer au formulaire
	 */
	public function setErrors ($pErrors) {
		$this->_errors = $pErrors;
	}

	/**
	 * Récupère tous les paramètres du formulaire
	 * et génère son entête
	 *
	 * @param array $pParams tableau contenant les paramètres (clé=>valeur)
	 * @return le HTML a afficher
	 */
	public function start ($pParams) {
		//Instanciation du datasource si il y en a un
		$this->_datasourceName = isset($pParams['datasource']) ? $pParams['datasource'] : null;
		$this->_params = $pParams;
		if ($this->_datasourceName != null) {
			$this->_datasource = CopixDatasourceFactory::get ($this->_datasourceName,$this->_params);
			$this->_mustDoRecord = true;
		}
	  
		//Gestion du mode (on prends en paramètre sinon on prend dans le request)
		$this->_mode        = isset ($pParams['mode']) ? $pParams['mode'] : _request ('mode_'.$this->_id, $this->_defaultMode);

		//Droit de création
		$this->_createRight = isset ($pParams['createRight']) ? $pParams['createRight'] : $this->_createRight;
	  
		//Droit d'edition
		$this->_editRight   = isset ($pParams['editRight']) ? $pParams['editRight'] : $this->_editRight;
		 
		//Droit de suppression
		$this->_deleteRight = isset ($pParams['deleteRight']) ? $pParams['deleteRight'] : $this->_deleteRight;
	  
		//Url de redirection apres un delete si rien de préciser on prend l'url de l'action
		$this->_deleteUrl   = isset ($pParams['deleteUrl']) ? $pParams['deleteUrl'] : $this->_action;
	  
		$this->_arPkValue   = array();
		$isFromRequest      = true;
	  
		//Si on est pas en mode error, on recupère les valeurs des champs avec MagicRequest ou dans le template ou mode create
		if (_request ('error_'.$this->_id) == null) {
			 
			$this->_create = false;
			 
			// On récupère les clé primaire dans le datasource
			$arPk = array();
			if ($this->_datasourceName != null) {
				$arPk    = $this->_datasource->getPk ();
			}
			$arValue = array ();
			foreach ($arPk as $pk) {
				// Si aucune valeur pour une des pk ou un paramètre de pk='' alors on passe en mode create
				if ((_request ($pk) == null && !isset ($pParams[$pk])) || (isset ($pParams[$pk]) && $pParams[$pk] == null)) {
					$this->_create = true;
					break;
				}
				//les pk préciser dans les paramètres surpasse ceux du request
				if (isset ($pParams[$pk])) {
					$isFromRequest = false;
					$arValue[$pk] = $pParams[$pk];
				} else {
					$arValue[$pk] = _request ($pk);
				}
			}
				
			//Si pas le mode create on rempli les champs grace au datasource
			if (!$this->_create) {
				$result = false;
				if ($this->_datasourceName != null) {
					$result = call_user_func_array (array($this->_datasource, 'get'), $arValue);
				}
				// Si tout va bien on rempli, si on obtiens rien ou qu'il n'y a pas de datasource on passe en mode create
				if ($result) {
					$this->_record = $result;
				} else {
					$this->_create = true;
				}
			}
			// Si mode create on force le mode edit et on supprime le record
			if ($this->_create) {
				$this->_mode = 'edit';
				$this->_record = new StdClass;
			}
			$this->_arPkValue = $arValue;
		}
	  
		//Rajoute l'encodage pour si il y a un champ qui upload
		$encoding = '';
		if (isset ($pParams['upload']) && $pParams['upload']) {
			$encoding = 'enctype="multipart/form-data" ';
		}
	  
		//Si on doit delete, on passe en mode view
		if (_request ('delete_'.$this->_id) || ($isFromRequest && _request ('delete'))) {
			$this->_toDelete = true;
			$this->_mode='view';
		}
	  
		//Si on est pas en creation et que l'on a pas les d'edition, on passe en mode visualisation
		if (!$this->_create && !$this->_getEdit ()) {
			$this->_mode='view';
		}
	  
		//Si on est en mode creation et que l'on en as pas les droits Exception
		if (!$this->_getCreate () && $this->_create) {
			//@TODO i18n
			throw new CopixFormException (_i18n('copix:copixform.message.noCreateRight'));
		}
	  
		//On génère les paramètres d'action du formulaire
		$this->_onDelete = isset ($pParams['onDelete']) ? $pParams['onDelete'] : 'generictools|copixforms|delete';
		$this->_action = isset ($pParams['action']) ? $pParams['action'] : 'generictools|copixforms|CheckRecord';
		$this->_onValid = isset ($pParams['onValid']) ? $pParams['onValid'] : null;
		$arParamsUrl = array('url'=>CopixUrl::get("#"), 'form_id'=>$this->_id, 'onValid'=>$this->_onValid);
		//Gestion de l'entete du formulaire
		$toReturn = '<form method="POST" '.$encoding.'id="formpost_'.$this->_id.'" action="'._url($this->_action,$arParamsUrl).'" ><span id="error_formpost_'.$this->_id.'" class="copixforms_error"></span>';
		return $toReturn;
	}

	/**
	 * Gestion de la fin du formulaire
	 *
	 * @return HTML
	 */
	public function end () {
		//Affichage des différents bouton a la fin du formulaire
		$toReturn = $this->getButton ('edit').$this->getButton ('delete').$this->getButton ('submit').'</form>';

		//Gestion des messages d'erreurs non affichées dans le formulaire
		if (count($this->_errors)>0) {
			$htmlError = '';
			foreach ($this->_errors as $key=>$error) {
				if (is_array($error)) {
					foreach ($error as $err) {
						$htmlError .= $key.' : '.$err.'<br />';
					}
				} else {
					$htmlError .= $key.' : '.$error.'<br />';
				}
				unset($this->_errors[$key]);
			}
			_tag('mootools');
			CopixHTMLHeader::addJSCode("
	    	window.addEvent('domready', function () {
	    		$('error_formpost_".$this->_id."').setHTML('".stripslashes($htmlError)."');
	    	});
	    	
	    	");
		}
		return $toReturn;
	}

	/**
	 * Permet de récupérer un des boutons du formulaire
	 *
	 * @param string $pType type de bouton
	 * @param string $pContent le HTML pour afficher le bouton, si il n'est pas renseigné, on obtiens le code par defaut du bouton
	 * @return HTML du bouton
	 */
	public function getButton ($pType, $pContent = null) {
		$toReturn = '';
		switch ($pType) {
			case 'delete':
				if ($this->_deleteButton || !$this->_getDelete() || $this->_mode != 'view') {
					return '';
				}
				$this->_deleteButton = true;
				if ($this->_toDelete) {
					//$toReturn .= '<div id"deleter">Supprimer ? <a href="'._url ('generictools|copixforms|delete', array_merge ($this->_arPkValue, array ('url'=>$this->_deleteUrl, 'form_id'=>$this->_id))).'">yes</a> <a href="'._url ('#', array_merge ($this->_arPkValue, array ('delete_'.$this->_id=>null))).'">no</a></div>'
					$urlYes = ($this->_onValid !== null) ? $this->_onValid : $this->_deleteUrl;
					$urlNo = ($this->_action !== null) ? _url($this->_action) : _url ('#', array_merge ($this->_arPkValue, array ('delete_'.$this->_id=>null)));
					_tag ('confirm',array('yes'=>_url ($this->_onDelete, array_merge ($this->_arPkValue, array ('url'=>$urlYes, 'form_id'=>$this->_id))),'no'=>$urlNo),_i18n('copix:copixform.message.delete'));
				} else {
					$toReturn .= '<a href="'._url ('#', array_merge ($this->_arPkValue, array('delete_'.$this->_id=>true,'form_id'=>$this->_id))).'" ><input type="button" value='._i18n('copix:copixform.button.delete').' /></a>';
				}
				break;
			case 'edit':
				if ($this->_editButton || !$this->_getEdit () || $this->_mode != 'view' || $this->_toDelete) {
					return '';
				}
				$this->_editButton = true;
				$arTemp = array();
				if ($this->_datasourceName != null) {
					$arTemp = $this->_datasource->getPk ();
				}
				$arPk=array();
				foreach ($arTemp as $temp) {
					$arPk[$temp] = $this->_record->$temp;
				}
				$arPk['mode_'.$this->_id] = 'edit';
				$arPk['mode_id'] = $this->_id;
				$toReturn .= '<a href="'._url ('#',$arPk).'" ><input type="button" " value="'._i18n('copix:copixform.button.edit').'" /></a>';
				break;
			case 'submit':
				if ($this->_submitButton || $this->_mode == 'view') {
					return '';
				}
				$this->_submitButton = true;
				if ($pContent == null) {
					$toReturn .= '<input type="submit" id="submit" value="'._i18n('copix:copixform.button.submit').'" />';
				} else {
					$toReturn .= '<span onClick="$(\'formpost_'.$this->_id.'\').submit()">'.$pContent.'</span>';
				}
				break;
			case 'cancel':
				if ($this->_cancelButton || $this->_mode == 'view' || ($this->_create && $this->_validUrl === null)) {
					return '';
				}
				$this->_cancelButton = true;
				if ($this->_validUrl !== null) {
					$toReturn .= '<a href="'._url ($this->_validUrl).'" ><input type="button" value="'._i18n('copix:copixform.button.cancel').'" /></a>';
				} else {
					$toReturn .= '<a href="'._url ('#',array('error_'.$this->_id=>null,'mode_'.$this->_id=>'view')).'" ><input type="button" value="'._i18n('copix:copixform.button.cancel').'" /></a>';
				}
				break;
			default:
				throw new CopixFormException  (_i18n ('copix:copixform.message.unknownType',$pType));
		}
		return $toReturn;
	}
	
     /**
     * Rempli les champs du formulaire depuis CopixRequest
     * @return array retourne un tableau d'erreur, si le tableau est vide alors il n'y a pas d'erreur
     */
    public function createRecord () {
        $this->errors = $errors = array ();
        $record = new StdClass;
        foreach ($this->_fields as $key=>$field) {
            $this->_fields[$key]->getFromRequest (true); 
        	$this->_fields[$key]->errors = array();
          	switch ($this->_fields[$key]->getType ()) {
                 case 'varchar' :
                 case 'textarea':                     
                 case 'hidden'  :
                 case 'select'  :
                 case 'int'     :
                 case 'check'   :
                 case 'date'    :
                     $fieldName = $this->_fields[$key]->field;
                     $record->$fieldName = $this->_fields[$key]->value;
                     break;
          	     default:
    		        $arClasses = explode('::',$field->getType ());
    		        if (count($arClasses)==2) {
    		            $Class = CopixClassesFactory::create($arClasses[0]);
    		            $method = $arClasses[1].'Value';
    		            $this->_fields[$key]->value = $Class->$method($this->_fields[$key], $record);
    		        } else {
                        throw new CopixFormException(_i18n('copix:copixlist.message.unknownType',$field->getType ()));
    		        }
            }
            $fieldErrors = $this->_fields[$key]->isValid ();
            if (count($fieldErrors) > 0) {
                $errors[$this->_fields[$key]->field] = $fieldErrors;
            }
        }
        $check = null;
        if ($this->_datasourceName != null) {
	        if (method_exists($this->_datasource,'check')) {
	            $check = $this->_datasource->check ($record);
	            if (is_array($check)) {
	                $errors = array_merge ($errors, $check);
	                $this->_errors = $check;
	            }
	        }
        }
        foreach ($record as $key=>$field) {
            $this->_record->$key = $field;
        }
        return $errors;
    }

	/**
	 * Lance la suppression d'un enregistrement
	 * @param array $pParams Tableau de pk
	 */
	public function delete ($pParams) {
		if ($this->_datasourceName == null) {
			throw new CopixFormException (_i18n('copix:copixform.message.noDatasource'));
		}
		$arPk = $this->_datasource->getPk ();
		$arValue = array ();
			
		foreach ($arPk as $pk) {
			if (!isset($pParams[$pk])) {
				throw new CopixFormException(_i18n('copix:copixform.message.noDelete'));
			}
			$arValue[$pk] = $pParams[$pk];
		}
		$result = call_user_func_array (array ($this->_datasource,'delete'),$arValue);
	}

	/**
	 * Gestion d'un champ
	 *
	 * @param string $pField Gestion des champs
	 * @param array $pParams les paramètres
	 * @return le HTML du bouton
	 */
	public function getField ($pField, $pParams) {
		//Si pas de type exception
		if (!isset($pParams['type'])) {
			throw new CopixFormException  (_i18n('copix:copixform.message.needType'));
		}

		$more = '';
		if (isset($pParams['more'])) $more = $pParams['more'];
		$id = md5(serialize($pField).$pParams['type'].$more);

		if (!isset($this->_fields[$id])) {
			$this->_fields[$id] = new CopixField ($pField, $pParams,$this->_id,'form');
		} else {
			foreach ($pParams as $key=>$params) {
				$this->_fields[$id]->setParams($key,$params);
			}
		}

		if (CopixRequest::get('error_'.$this->_id)===null) {
			if (isset($this->_fields[$id])) {
				$this->_fields[$id]->assignDefaultValue(isset($pParams['value']) ? $pParams['value'] : null);
			}
			$this->_fields[$id]->assignRecord ($this->_record);
		}

		if (!$this->_create || _request ('error_'.$this->_id) != null) {
			$this->_fields[$id]->getFromRequest ();
		} else {
			$this->_fields[$id]->emptyData ();
		}

		$field = $this->_fields[$id];


		 
		if ($this->_getEdit ()) {
			$currentMode = $field->getMode ($this->_mode);
		} else {
			$currentMode = $field->getMode ('view');
		}



		$toReturn = $field->getHTML($currentMode);

		if ($field->getParams('caption')!==null) {
			$toReturn = $field->getParams('caption').' : '.$toReturn;
		}
	  
		if ($currentMode=='edit') {
			if (_request ('error_'.$this->_id)==true) {
				/*
				
				 if ($field->getErrors ()) {
					foreach ($field->getErrors() as $key=>$error) {
						if (is_array($error)) {
							foreach ($error as $err) {
								$toReturn .= '<span class="copixforms_error" >* '._copix_utf8_htmlentities($err).'</span>';
							}
						} else {
							$toReturn .= '<span class="copixforms_error" >* '._copix_utf8_htmlentities($error).'</span>';
						}
					}
				}
				//*/
				if (isset($this->_errors[$field->field])) {
					if (is_array($this->_errors[$field->field])) {
						foreach ($this->_errors[$field->field] as $err) {
							$toReturn .= '<span class="copixforms_error" >* '._copix_utf8_htmlentities($err).'</span>';
						}
					} else {
						$toReturn .= '<span class="copixforms_error" >* '._copix_utf8_htmlentities($this->_errors[$field->field]).'</span>';
					}
					unset ($this->_errors[$field->field]);
				}
			}
		}
		return $toReturn;
	}

	/**
	 * Lance les checks des differents champs
	 */
	public function doValid () {
		$errors = $this->createRecord ();
		if (count($errors)>0) {
			CopixLog::log(serialize($errors));
			throw new CopixFormCheckException ($errors);
		}
	}

	/**
	 * Lance l'enregistrement
	 * Teste la validité des champs
	 * @return array les pk créé
	 */
	public function doRecord () {
		if (!$this->_mustDoRecord) {
			return array ();
		}
		if ($this->_datasourceName == null) {
			throw new CopixFormException (_i18n('copix:copixform.message.noDatasource'));
		}
		$this->errors = array();
		$result = null;
		try {
			if (!isset($this->_create) || $this->_create) {
				CopixLog::log('create : '.var_export($this->_record,true),'copixforms');
				$result = $this->_datasource->save($this->_record);
			} else {
				CopixLog::log('update : '.var_export($this->_record,true),'copixforms');
				$result = $this->_datasource->update($this->_record);
			}
		} catch (CopixDAOCheckException $e) {
			$this->errors = $e->getErrors();
			CopixLog::log(serialize($this->errors));
			throw new CopixFormCheckException ($e->getErrors());
		}
		$arPk = array ();
		foreach ($this->_datasource->getPk () as $pk) {
			$arPk[$pk] = isset($result->$pk) ? $result->$pk : null;
		}
		return $arPk;
	}

	//Parti qui gère les droits



	private function _getEdit () {
		if (is_bool($this->_editRight)) {
			return $this->_editRight;
		}
		return CopixAuth::getCurrentUser ()->testCredential ($this->_editRight);
	}

	private function _getDelete () {
		if (is_bool($this->_deleteRight)) {
			return $this->_deleteRight;
		}
		return CopixAuth::getCurrentUser ()->testCredential ($this->_deleteRight);
	}

	private function _getCreate () {
		if (is_bool($this->_createRight)) {
			return $this->_createRight;
		}
		return CopixAuth::getCurrentUser ()->testCredential ($this->_createRight);
	}


}
?>