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

/**
 * Classe de base pour les CopixField standards fournis avec Copix
 * @package copix
 * @subpackage forms
 */
abstract class CopixAbstractField implements ICopixField {
	private $_type = null;
	
	private $_params = array ();
	
	private $_validators = null;
	
	protected $_container = null;
	
	public function attachContainer (CopixFieldContainer $pContainer) {
		$this->_container = $pContainer;
		$this->_initContainer();
	}
	
	protected function _initContainer () {}
	
	public function __construct ($pType, $pParams = array ()) {
		$this->_type = $pType;
		$this->_params = $pParams;
	}
	
	public function getParams () {
		return $this->_params; 
	}
	
	public function getParam ($pField, $pDefault = null) {
		return isset ($this->_params[$pField]) ? $this->_params[$pField] : $pDefault; 
	}
	
	public function setParam ($pParamName, $pValue) {
	    $this->_params[$pParamName] = $pValue;
	}
	
	public function getType () {
		return $this->_type;
	}
	
	public function fillFromRequest ($pName, $pDefault = null, $pValue = null) {
	    try {
	        CopixRequest::assert($pName);
	    } catch (CopixRequestException $e) {
	        return $pValue;
	    }
		return _request ($pName, null);
	}

	public function addCondition ($pDatasource, $pField, $pValue) {
		if ($pValue != null) {
			if ($this->getParam ('after', true)) {
				$pDatasource->addCondition ($pField, 'like', $pValue.'%');
			} else {
				$pDatasource->addCondition ($pField, 'like', $pValue);
			}
		}
	}
	
	public function fillFromRecord ($pField, $pRecord) {
		return (isset ($pRecord->{$pField})) ? $pRecord->{$pField} : null;
	}
	
	public function fillRecord ($pRecord, $pField, $pValue) {
		$pRecord->{$pField} = $pValue;
	}
	
	public function attach ($pValidator) {
		if ($this->_validators == null) {
			$this->_validators = new CopixCompositeValidator();
		}
		$this->_validators->attach ($pValidator);
		return $this;
	}
	
	public function getValidators () {
		return $this->_validators;
	}
}