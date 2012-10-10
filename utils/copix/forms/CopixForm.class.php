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
 * TODO Gérer les pk a '' qui passe en mode create (normalement gérer, mais pas tester)
 * TODO Gérer mieux la phrases de confirm de delete
 */


class CopixField {

   private $_datas        = array();
    
   private $_params       = array ();
   
   private $_type         = null;
   
   private $_defaultValue = null;
   
   private $_id = null;
   
   private $_errors = array();
   
   //Permet de savoir si c'est un champ de CopixList ou de CopixForms
   private $_typeField = null;
   
   public function getErrors() {
       if (count ($this->_errors)>0) {
           return $this->_errors;
       }
       return false;
   }
   
   public function getType () {
       return $this->_type;
   }
   
   public function __construct ($pField, $pParams, $pId, $pTypeField) {
       $this->_typeField = $pTypeField;
       
       $this->_id = $pId;
       
       $this->_params         = $pParams;
       
       $this->_type           = $pParams['type'];
       
       $this->_defaultValue   = isset ($pParams['value']) ? $pParams['value'] : null;
       
       if (!is_array ($pField)) {
           $pField = array($pField);
           $this->_defaultValue = array ($this->_defaultValue);
       } else {
           if (!is_array ($this->_defaultValue)) {
               $this->_defaultValue = array ($this->_defaultValue);
           }
       }
       
       foreach ($pField as $key=>$cField) {
           $field               = new StdClass;
           $field->name         = md5 ($cField.$this->_type);
           $field->field        = $cField;
           $field->value        = isset($this->_defaultValue[$key]) ? $this->_defaultValue[$key] : null;
           $this->_datas[$key]  = $field;
       }
   }
   
   public function assignRecord ($pRecord) {
       foreach ($this->_datas as $key=>&$field) {
           if (isset ($field->field)) {
               $fieldName = $field->field; 
               if (isset ($pRecord->$fieldName)) {
                   $field->value = $pRecord->$fieldName; 
               } else {
                   $field->value = null;
               }
           }
       }
   }
   
   public function getFromRequest ($force = false) {
       foreach ($this->_datas as $key=>&$field) {
               if ($force === true) {
                   $field->value = _request ($field->name,  null);
               } else {
                   if ($this->_typeField === 'list') {
                       $field->value = _request ($field->name,  null, false);
                   } else {
                       $field->value = _request ($field->name,  $field->value, false);
                       
                   }
               }
       }
   }
   
   public function get ($pField = null) {
       if ($pField === null) {
           return isset ($this->_datas[0]) ? $this->_datas[0] : null;
       }
       
       if (isset ($this->_datas[$pField])) {
           return $this->_datas[$pField];
       }
       
       $field           = new StdClass;
       $field->name     = md5 (uniqid().$pField.$this->_type);
       $this->_datas[$pField] = $field;
       
       return $this->_datas[$pField]; 
   }

   public function emptyData () {
       foreach ($this->_datas as $field) {
           $field->value = null; 
       }
   }
   
   public function isEmpty () {
       $valueEmpty = true;
       foreach ($this->_datas as $field) {
           if (isset ($field->value) && $field->value !== null) {
               $valueEmpty = false;
               break;
           }
       }
       return $valueEmpty;
   }
   
    /**
     * Lance la validation des différents champs
     * @param $pField le champ a valider
     * @return array retourne un tableau d'erreur, si le tableau est vide alors il n'y a pas d'erreur
     */
    public function isValid () {
        $this->_errors = $errors = array ();
        if ($this->getParams('valid')!==null) {
	        $arClasses = explode('::',$this->getParams('valid'));
		    if (count($arClasses)!=2) {
		        throw new CopixFormException ('classe de validation incorrect');
		    }
		    $classe = CopixClassesFactory::create ($arClasses[0]);
		    $errors = $classe->$arClasses[1] ($this);
		    if (!is_array($errors)) {
		        $errors = array($errors);
		    }
        }
        $this->_errors = $errors;
        return $errors;
    }
   
   public function getParams ($pParamName) {
       return isset ($this->_params[$pParamName]) ? $this->_params[$pParamName] : null;
   }
   
   public function setParams ($pParamName, $pValue) {
       $this->_params[$pParamName] = $pValue;
   }
   
   /**
	 * Méthode permettant de savoir le mode du champ (gère les droits)
	 *
	 * @param string $pId NAME du champ
	 * @param string $pParam Définition ou surcharge des différentes options des champs
	 * @return string mode ou false si aucun droit
	 */
   public function getMode ($currentMode) {

	    if ($this->getParams('mode')!==null) {
	        return $this->getParams('mode');
	    }

        if ($currentMode == 'edit') {
            if (!$this->_getFieldEdit ()) {
                $currentMode = 'view';
            }
        }
        
        if ($currentMode == 'view') {
            if (!$this->getFieldView ()) {
                return false;
            }
        }
        return $currentMode;
        
   }
   
    /**
     * Recupère les droits de view pour le champs $pId
     * @param $pId string le champ a tester
     */
    public function getFieldView () {
        if ($this->getParams('getview')!==null) {
            if (is_bool($this->getParams('getview'))) {
                return $this->getParams('getview');
            }
            return CopixAuth::getCurrentUser ()->testCredential ($this->getParams('getview'));
        }
        return true;
    }
    
    /**
     * Recupère les droits de edit pour le champs $pId
     * @param $pId string le champ a tester
     */
    private function _getFieldEdit () {
        if ($this->getParams('getedit')!==null) {
            if (is_bool($this->getParams('getedit'))) {
                return $this->getParams('getedit');
            }
            return CopixAuth::getCurrentUser ()->testCredential ($this->getParams('getedit'));
        }
        return true;
    }
   
   public function __get ($pPropertyName) {
       return isset ($this->_datas[0]->$pPropertyName) ? $this->_datas[0]->$pPropertyName : null; 
   }
   
   public function __set ($pPropertyName, $pValue) {
       $this->_datas[0]->$pPropertyName = $pValue;
   }
   
   public function __isset ($pPropertyName) {
       return isset ($this->_datas[0]->$pPropertyName);
   }
}
/**
 * Classe principale pour CopixForm
 * @package		copix
 * @subpackage	forms
 */
class CopixForm {
	
    private $_id             = null;
    
    private $_datasource     = null;
    
    private $_datasourceName = null;
    
    private $_params         = array ();
    
    private $_mode           = 'view';
    
    private $_defaultMode    = 'view';
    
    private $_createRight    = true;
    
    private $_editRight      = true;
    
    private $_deleteRight    = true;
    
    private $_deleteUrl      = null;
    
    private $_arPkValue      = array ();
    
    private $_create         = null;
    
    private $_fields         = array ();
    
    private $_action         = 'generictools|copixforms|CheckRecord';
    
    private $_validUrl       = null;
    
    //En cours de delete ?
    private $_toDelete       = false;
    
    private $_deleteButton   = false;
    
    private $_editButton   = false;
    
    private $_submitButton   = false;
    
    private $_cancelButton   = false;
    
    private $_record         = null;
    /**
	 * _sleep
	 * on détruit le datasource pour pas le mettre en session
	 * et on remets a null les valeur de _editbutton et _deletebutton
	 */
	public function __sleep () {
	    $this->_editButton = false;
	    $this->_deleteButton = false;
	    $this->_submitButton = false;
	    $this->_cancelButton = false;
	    $this->_toDelete     = false;	    
	    if ($this->_datasourceName != 'dao') {
	        $this->_datasource = new CopixSessionObject($this->_datasource, $this->_datasourceName);
	    }
	    return array_keys(get_object_vars($this));
	}
	
	/**
	 * _wakeup
	 * on recrée le datasource
	 */
	public function __wakeup () {
	    if ($this->_datasourceName != 'dao') {
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
	
	public function start ($pDatasourceName, $pParams) {
	    //Instanciation du datasource
	    $this->_datasourceName = $pDatasourceName;
        $this->_params = $pParams;
        $this->_datasource = CopixDatasourceFactory::get ($this->_datasourceName,$this->_params);
	    
        $this->_datasource->temp = 'form';
        
        //Gestion du mode (on prends en paramètre sinon on prend dans le request
	    $this->_mode = isset($pParams['mode']) ? $pParams['mode'] : _request ('mode_'.$this->_id, $this->_defaultMode);
        
	    if (isset ($pParams['createRight'])) {
	        $this->_createRight = $pParams['createRight'];
	    }
	    
	    if (isset ($pParams['editRight'])) {
	        $this->_editRight = $pParams['editRight'];
	    }
	    
	    if (isset ($pParams['deleteRight'])) {
	        $this->_deleteRight = $pParams['deleteRight'];
	    }
	    
    	if (isset ($pParams['validUrl'])) {
	        $this->_validUrl = $pParams['validUrl'];
	    }
	    
	    //Url de redirection apres un delete si rien de préciser on prend la default|default du module dans le context
	    $this->_deleteUrl = isset($pParams['deleteUrl']) ? $pParams['deleteUrl'] : CopixContext::get().'||';
	    
	    $this->_arPkValue = array();
	    
   	    $isFromRequest = true;
	    
	    //Si on est pas en mode error, on recupère les valeurs des champs avec MagicRequest ou dans le template ou mode create
	    if (_request ('error_'.$this->_id) == null) {
	        
	        $this->_create = false;
	        
	        // On récupère les clé primaire dans le datasource
    	    $arPk    = $this->_datasource->getPk ();
    	    $arValue = array ();
    	    foreach ($arPk as $pk) {
    	        // Si aucune valeur pour une des pk ou un paramètre de pk='' alors on passe en mode create
    	        if ((_request ($pk) == null && !isset ($pParams[$pk])) || (isset ($pParams[$pk]) && $pParams[$pk] == null)) {
    	             $this->_create = true;
    	             break;
    	        }
    	        if (isset ($pParams[$pk])) {
    	            $isFromRequest = false;
    	            $arValue[$pk] = $pParams[$pk];
    	        } else {
    	            $arValue[$pk] = _request ($pk);
    	        }
    	    }
    	    //Si pas le mode create on rempli les champs grace au datasource
    	    if (!$this->_create) {
    	        $result = call_user_func_array(array($this->_datasource,'get'),$arValue);
    	        if ($result) {
    			    $this->_record = $result;
    		    } else {
    		        $this->_create = true;
    		    }
    	    }
    	    // Si mode create on force le mode edit et on remets les valeurs des champs a null
	        if ($this->_create) {
	            $this->_mode = 'edit';
  	            $this->_record = new StdClass;
	        }
	        $this->_arPkValue = $arValue;
	    }
	        
	    if (isset ($pParams['action'])) {
	        $this->_action = $pParams['action'];
	    }
	    
	    //Rajoute l'encodage pour si il y a un champ qui upload
	    $encoding = '';
    	if (isset ($pParams['upload']) && $pParams['upload']) {
            $encoding = 'enctype="multipart/form-data" ';
	    }
	    
	    //Si on doit delete, on passe en mode view
	    if (_request ('delete_'.$this->id) || ($isFromRequest && _request('delete'))) {
	        $this->_toDelete = true;
	        $this->_mode='view';
	    }
	    
	    if (!$this->_create && !$this->_getEdit()) {
	        $this->_mode='view';
	    }
	    
	    if (!$this->_getCreate()) {
	        if ($this->_create) {
	            //@TODO i18n
	            throw new Exception ('Vous n\'avez pas les droits de création');
	        }
	    }
	    
	    
	    
	    $toReturn = '<form method="POST" '.$encoding.'id="formpost_'.$this->_id.'" action="'._url($this->_action,array('url'=>CopixUrl::get("#"), 'form_id'=>$this->_id, 'validUrl'=>$this->_validUrl)).'" >';
	    return $toReturn;
	}

	public function end () {
	    $toReturn = $this->getButton ('edit').$this->getButton ('delete').$this->getButton ('submit').$this->getButton ('cancel').'</form>';
	    return $toReturn;
	}
	
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
                     $urlYes = ($this->_validUrl !== null) ? $this->_validUrl : $this->_deleteUrl;
                     $urlNo = ($this->_validUrl !== null) ? _url($this->_validUrl) : _url ('#', array_merge ($this->_arPkValue, array ('delete_'.$this->_id=>null)));                     
	                _tag ('confirm',array('yes'=>_url ('generictools|copixforms|delete', array_merge ($this->_arPkValue, array ('url'=>$urlYes, 'form_id'=>$this->_id))),'no'=>$urlNo),"Voulez supprimer cet enregistrement ?");
                } else {
                    $toReturn .= '<a href="'._url ('#', array_merge ($this->_arPkValue, array('delete_'.$this->_id=>true,'form_id'=>$this->_id))).'" ><input type="button" value="Supprimer" /></a>';
                }
	            break;
	        case 'edit':
	                if ($this->_editButton || !$this->_getEdit () || $this->_mode != 'view' || $this->_toDelete) {
	                    return '';
	                }
	                $this->_editButton = true;
	                $arTemp = $this->_datasource->getPk ();
	                $arPk=array();
	                foreach ($arTemp as $temp) {
	                    $arPk[$temp] = $this->_record->$temp;
	                }
	                $arPk['mode_'.$this->_id] = 'edit';
	                $arPk['mode_id'] = $this->_id;
	                $toReturn .= '<a href="'._url ('#',$arPk).'" ><input type="button" " value="Editer" /></a>';
	            break;
	        case 'submit':
	            if ($this->_submitButton || $this->_mode == 'view') {
	                return '';
	            }
	            $this->_submitButton = true;
	            $toReturn .= '<input type="submit" id="submit" />';
	            break;
	        case 'cancel':
	            if ($this->_cancelButton || $this->_mode == 'view' || ($this->_create && $this->_validUrl === null)) {
	                return '';
	            }
	            $this->_cancelButton = true;  
	            if ($this->_validUrl !== null) {
	                $toReturn .= '<a href="'._url ($this->_validUrl).'" ><input type="button" value="Annuler" /></a>';
	            } else {
    	            $toReturn .= '<a href="'._url ('#',array('error_'.$this->_id=>null,'mode_'.$this->_id=>'view')).'" ><input type="button" value="Annuler" /></a>';
	            }
	            break;
	        default:
	            throw new CopixException  ('type inconnu');
	    }
	    return $toReturn;
	}
	
    /**
     * Lance la suppression d'un enregistrement
     * @param array Tableau de pk
     */
    public function delete($pParams) {
        $arPk = $this->_datasource->getPk ();
    	$arValue = array ();
    	    
    	foreach ($arPk as $pk) {
    	    if (!isset($pParams[$pk])) {
    	         throw new CopixFormException('Impossible de supprimer');
    	    }
    	    $arValue[$pk] = $pParams[$pk];
    	}
    	
    	$result = call_user_func_array(array($this->_datasource,'delete'),$arValue);
    }
	
    public function getField ($pField, $pParams) {
                
       if (!isset($pParams['type'])) {
            throw new Exception ('AAA');
        }
        $more = '';
        if (isset($pParams['more'])) $more = $pParams['more'];
        $id = md5(serialize($pField).$pParams['type'].$more);
        
        if (!isset($this->_fields[$id])) {
            $this->_fields[$id] = new CopixField ($pField, $pParams,$this->_id,'form');
        }
        
        if (CopixRequest::get('error_'.$this->_id)===null) {
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
        
        
        
        //Voir la gestion des values
        $html = '';
        //echo 'type : '.$field->getType ();
        switch ($field->getType ()) {
            case 'textarea':
                if ($currentMode == 'edit') {
                    $toReturn .= '<textarea name="'.$field->name.'">'.$field->value.'</textarea>';
                } else {
                    $toReturn .= $field->value;
                }
	            break;
            case 'varchar':
                if ($currentMode == 'edit') {
                    $toReturn .= _tag('inputtext',array('name'=>$field->name,'maxlength'=>($field->getParams('maxlength')) ,'value'=>$field->value));
                } else {
                    $toReturn .= $field->value;
                }
	            break;
            case 'select':
                if ($field->getParams ('arValues') === null) {
                    throw new CopixException ('Manque arValues');
                }
                $arValues = $field->getParams ('arValues');
                if ($currentMode == 'edit') {
                    $toReturn .= _tag('select',array('values'=>$arValues, 'name'=>$field->name, 'selected'=>$field->value,'objectMap'=>( isset($pParams['objectMap']) ? $pParams['objectMap'] : null)));
                } else {
                    if (!isset ($pParams['objectMap'])) {
                        $toReturn .= (isset($field->value)) ? (isset($arValues[$field->value]) ? $arValues[$field->value] : '') : '';
                    }
                }
                break;
            case 'date'  :
                if ($currentMode == 'edit') {
                    $toReturn .= _tag ('calendar', array ('name'=>$field->name, 'value'=>$field->value));
                } else {
                    $toReturn .= $field->value;
                }
                break;
            case 'hidden':
                break;
            case 'check':
            	$checked = '';
            	if ($field->value == 1) {
            		$checked = 'checked';
            	}
            	if ($currentMode == 'edit') {
            		$toReturn .= '<input type="checkbox" name="'.$field->name.'" value="1" '.$checked.' />';
            	} else {
            		$toReturn .= ($field->value == 1) ? 'oui' : 'non';
            	}
            	
                break;
                    default:
                $arClasses = explode('::',$field->getType ());
                if (count($arClasses)==2) {
                    $Class = _class ($arClasses[0]);
                    $method = $arClasses[1].'HTML';
                    $toReturn .= $Class->$method($field,$currentMode);
                } else {
                    throw new CopixFormException(_i18n('copix:copixlist.message.unknownType',$field->getType ()));
                }
        }
        
        if ($field->getParams('caption')!==null) {
            $toReturn = $field->getParams('caption').' : '.$toReturn;
        }
	    
        if ($currentMode=='edit') {
            if (_request ('error_'.$this->_id)==true) {
	            if ($field->getErrors ()) {
		            foreach ($field->getErrors() as $error) {
		                $toReturn .= '<span class="copixforms_error" >* '._copix_utf8_htmlentities($error).'</span>';
		            }
	            }
	            if (isset($this->_errors[$field->field])) {
	                $toReturn .= '<span class="copixforms_error" >* '._copix_utf8_htmlentities($this->_errors[$field->field]).'</span>';
	            }
	        } 
	        return $toReturn;
        } else {
            if ($this->_getEdit($pId) && $this->_byField) {
               $toReturn = '<span id="div'.$this->fields[$pId]->name.'" class="editField" rel="edit'.$this->fields[$pId]->name.'">'.$toReturn.' <input id="edit'.$this->fields[$pId]->name.'"type="button" rel="'.$this->fields[$pId]->name.'" class="toEdit" value="edit" /></span>';
            }
            return $toReturn;
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
                        throw new CopixListException(_i18n('copix:copixlist.message.unknownType',$field->getType ()));
    		        }
            }
            $fieldErrors = $this->_fields[$key]->isValid ();
            if (count($fieldErrors) > 0) {
                $errors[] = $fieldErrors;
            }
        }
        $check = null;
        if (method_exists($this->_datasource,'check')) {
            $check = $this->_datasource->check ($record);
            if (is_array($check)) {
                $errors = array_merge ($errors, $check);
                $this->_errors = $check;
            }
        }
        foreach ($record as $key=>$field) {
            $this->_record->$key = $field;
        }
        return $errors;
    }

 /**
     * Lance l'enregistrement
     * Teste la validité des champs
     * @return array les pk créé 
     */
    public function doRecord () {
        $errors = $this->createRecord ();
        if (count($errors)>0) {
            CopixLog::log(serialize($errors));
            throw new CopixFormException ("pas valide");
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
            throw new CopixFormException ("pas valide");
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