<?php
/**
 * @package		copix
 * @subpackage	forms
 * @author		Salleyron Julien
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 * @experimental
 */


class CopixFieldContainer extends CopixParameterHandler {
	
	protected $_fieldType  = null;
	
	protected $_fieldParams = null;
	
	protected $_localValue = null;
	
	protected $_defaultValue = null;
	
	protected $_validators = null;
	
	protected $_errors = true;
	
	protected $_name = null;
	
	protected $_label = null;
	
	protected $_field = null;
	
	protected $_objField = null;
	
	protected $_edit = true;
	
	protected $_formId = null;
	
	public function setLabel ($pLabel) {
		$this->_label = $pLabel;
	}
	
	public function getFormId () {
		return $this->_formId;
	}
	
	public function setFormId ($pId) {
		$this->_formId = $pId;
	}
	
	public function getFormMode () {
		if (($form = _form ($this->_formId)) != null) {
			return $form->getEditCredential () ? 'edit' : 'view';
		}
		return "edit";
	}
	
	public function addError ($pErrors) {
		if (!$this->_errors instanceof CopixErrorObject) {
			$this->_errors = new CopixErrorObject();
		}
		$this->_errors->addErrors ($pErrors);
	}
	
	public function setFieldParams ($pParams) {
	    $this->_fieldParams = $pParams;
	}
	
	public function getFieldParams () {
	    return $this->_fieldParams;
	}
	
	public function getField ($pBoolInstanciation = true) {
		if (isset ($this->_objField)) {
			return $this->_objField;
		}
		if (!$pBoolInstanciation) {
			return null;
		}
		$field = CopixFieldFactory::get ($this->_fieldType, $this->_fieldParams);
		if ($this->_validators != null) {
		   $field->attach ($this->_validators);
		}
		$field->attachContainer ($this);
		return ($this->_objField = $field);
	}
	
	public function setField ($pField) {
		$this->_fieldType = $pField->getType ();
		$this->_fieldParams = $pField->getParams ();
		$pField->attachContainer ($this);
		$this->_objField = $pField;
	}
	
	public function __construct ($pName, $pParams = array ()) {
		$this->_name = $pName;
		$this->setParams ($pParams);
	}
	
	public function setParams ($pParams) {
		parent::setParams($pParams);
		$this->_field = $this->getParam('field');
		if ($this->_field == null) {
			$this->_field = $this->_name;
		}
		$this->_label = $this->getParam('label');
		if ($this->_label == null && $this->_label !== false) {
			$this->_label = $this->_name;
		}
		$this->_edit = $this->getParam('edit', true);
		$this->_defaultValue = $this->getParam('value');
	}
	
	public function fillFromRequest () {
	    $mode = ($this->getEditCredential ()) ? $this->getFormMode () : 'view';
	    if ($mode != 'view') {
		    $this->_localValue = $this->getField ()->fillFromRequest ($this->_name, $this->_defaultValue, $this->_localValue);
	    }
	}
	
	public function fillFromRecord ($pRecord) {
		$value = $this->getField ()->fillFromRecord ($this->_field, $pRecord);
		$this->_localValue = isset ($value) ? $value : $this->_defaultValue;
	}
	
	public function fillRecord ($pRecord) {
	    $mode = ($this->getEditCredential ()) ? $this->getFormMode () : 'view';
	    //if ($mode != 'view') {
		    $this->getField ()->fillRecord ($pRecord, $this->_field, $this->_localValue);
	    //}
	}
	
	public function getLabel () {
		if ($this->_label !== false) {
			return $this->_label;
		}
		return null;
	}
	
	public function getHTML () {
		$mode = ($this->getEditCredential ()) ? $this->getFormMode () : 'view';
		return $this->getField()->getHTML ($this->_name, $this->_localValue, $mode);
	}
	
	public function getErrors ($pTemplate = 'copix:templates/validator.error.tpl') {
		if ($this->_errors !== true) {
			$tpl = new CopixTpl ();
			$tpl->assign ('errors', $this->_errors->asArray ());
			return $tpl->fetch ($pTemplate);
		}
		return null;
	}
	
	public function __sleep () {
		$field = $this->getField (false);
		if ($field != null) {
		    $this->_validators = $field->getValidators ();
		}
		return array ('_name', '_label', '_field', '_localValue', '_defaultValue', '_fieldType', '_fieldParams', '_validators', '_errors', '_edit', '_formId');
	}

	public function check () {
		$this->_errors = true;
		if ($this->getParam('require',false) && ($this->_localValue == null)) {
			$this->_errors = new CopixErrorObject('Champs obligatoire');
		}
		if ($this->getField ()->getValidators () != null) {
			if ($this->_localValue != null) {
				$this->_errors = $this->getField ()->getValidators ()->check ($this->_localValue);
			}
		}
		return $this->_errors;
	}
	
	public function reset () {
		$this->_errors = true;
		$this->_localValue = $this->_defaultValue;
	}
	
	public function getEditCredential () {
		if (is_bool ($this->_edit)) {
			return $this->_edit;
		} else {
			return _currentUser ()->testCredential ($this->_edit);
		}
	}
	
	public function getValue () {
		return $this->_localValue;
	}
	
    public function setValue ($pValue) {
		$this->_localValue = $pValue;
		return $this;
	}
	
	public function getFieldName () {
		return $this->_field;
	}

	public function addConditions ($pDatasource) {
		$this->getField ()->addCondition ($pDatasource, $this->_field, $this->_localValue);
	}
	
	protected function _reportErrors ($pErrors){
		print_r ($pErrors);
		exit;
	}
}

?>
