<?php
/**
 * @package		copix
 * @subpackage	forms
 * @author		Salleyron Julien
 * @copyright	CopixTeam
 * @link			http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 * @experimental
 */

class CopixFieldException extends CopixException {}

/**
 * Class gérant les différents champs de formulaire
 *
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
	 
	public function getDatas () {
		return $this->_datas;
	}
	 
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
	 
	public function assignDefaultValue ($pDefaultValue) {
		$pDefaultValue = is_array($pDefaultValue) ? $pDefaultValue : array($pDefaultValue);
		foreach ($this->_datas as $key=>$cField) {
			$this->_defaultValue[$key] = isset($pDefaultValue[$key]) ? $pDefaultValue[$key] : null;
		}
	}
	 
	public function assignRecord ($pRecord) {
		foreach ($this->_datas as $key=>&$field) {
			if (isset ($field->field)) {
				$fieldName = $field->field;
				if (isset ($pRecord->$fieldName)) {
					$field->value = $pRecord->$fieldName;
				} else {
					$field->value = $this->_defaultValue[$key];
				}
			} else {
				$field->value = null;
			}
		}
	}
	 
	public function getFromRequest ($force = false) {
		foreach ($this->_datas as $key=>&$field) {
			if ($force === true) {
			    if ($this->_type != 'hidden') {
				    $field->value = _request ($field->name,  null);
			    }
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
		foreach ($this->_datas as $key=>$field) {
			$field->value = $this->_defaultValue[$key];
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
				throw new CopixFieldException (_i18n('copixfield.message.validIncorrect'));
			}
			$classe = CopixClassesFactory::create ($arClasses[0]);
			$errors = $classe->$arClasses[1] ($this);
			if ($errors === null) {
				$errors = array ();
			}
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
	 

	public function getHTML ($currentMode = 'edit') {
		$toReturn = '';
		switch($this->_type) {
			case 'bool':
				$checked = '';
				if ($this->value == 1) {
					$checked = 'checked';
				}
				if ($currentMode == 'edit') {
					$toReturn .= '<input type="checkbox" name="'.$this->name.'" value="1" '.$checked.' />';
				} else {
					$toReturn .= ($this->value == 1) ? 'oui' : 'non';
				}

				break;
			case 'textarea':
				if ($currentMode == 'edit') {
					$toReturn .= '<textarea name="'.$this->name.'" '.$this->getParams('extra').'>'.$this->value.'</textarea>';
				} else {
					$toReturn .= $this->value;
				}
				break;
			case 'varchar':
			case 'sup':
			case 'inf':
				if ($currentMode == 'edit') {
					$extra = $this->getParams('extra');
					$toReturn .= _tag('inputtext',array('name'=>$this->name,'maxlength'=>($this->getParams('maxlength')) ,'value'=>$this->value, 'extra'=>$extra));
				} else {
					$toReturn .= $this->value;
				}
				break;
			case 'autocomplete':
				$pParams = $this->_params;
				$pParams['field'] = $this->field;
				if (!isset($pParams['datasource'])) {
					$pParam['datasource']='dao';
				}
				$pParams['name'] = $this->name;
				$toReturn .= _tag('autocomplete',$pParams);
				break;
			case 'multipleselect':
			case 'select':
			case 'checkbox':
				if ($this->getParams ('arValues') === null) {
					//TODO i18n
					throw new CopixFieldException ('Manque arValues');
				}
				$arValues = $this->getParams ('arValues');
				if ($currentMode == 'edit') {
					$toReturn .= _tag($this->_type, array('values'=>$arValues, 'name'=>$this->name, 'selected'=>$this->value,'emptyValues'=>$this->getParams('emptyValues'), 'objectMap'=>( $this->getParams('objectMap'))));
				} else {
					//TODO gerer le view en objectMap
					if ($this->getParams('objectMap')!=null) {
						$toReturn .= (isset($this->value)) ? (isset($arValues[$this->value]) ? $arValues[$this->value] : '') : '';
					}
				}
				break;
			case 'date'  :
				if ($currentMode == 'edit') {
					$toReturn .= _tag ('calendar', array ('name'=>$this->name, 'value'=>$this->value));
				} else {
					$toReturn .= $this->value;
				}
				break;
			case 'hidden':
			case 'hiddendif':
				break;
			default:
				$arClasses = explode('::',$this->_type);
				if (count($arClasses)==2) {
					$Class = _class ($arClasses[0]);
					$method = $arClasses[1].'HTML';
					$toReturn .= $Class->$method($this,$currentMode);
				} else {
					throw new CopixFieldException(_i18n('copix:copixlist.message.unknownType',$this->_type));
				}
		}
		return $toReturn;
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



?>