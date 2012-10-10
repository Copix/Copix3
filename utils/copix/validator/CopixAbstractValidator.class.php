<?php
/**
 * @package		copix
 * @subpackage	validator
 * @author		Salleyron Julien
 * @copyright	CopixTeam
 * @link			http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

abstract class CopixAbstractValidator extends CopixParameterHandler implements ICopixValidator  {
	protected $_message = null;
	protected $_params;
	
	protected function _getMessage ($pValue, $pResult){
		return $this->_message !== null ? $this->_message : ($pResult === false ? ($pValue . ' est une valeur incorrecte pour '.$this->_getName ()) : $pResult);
	}

	private function _getName (){
		return get_class ($this);
	}
	
	public function __construct ($pParams = array (), $pMessage = null){
		$this->setParams ($pParams);
		$this->_message = $pMessage;
	}

	/**
	 * Lance la vérification du validateur. 
	 *
	 * @param	mixed 	$pValue	La valeur à tester
	 * @return 	true en cas de succès. CopixValidatorErrorCollection en cas d'échec
	 */
	public function check ($pValue){
		if (($result = $this->_validate ($pValue)) !== true){
			return new CopixErrorObject ($this->_getMessage ($pValue, $result));
		}
		return true;
	}

	public function assert ($pValue){
		if (($result = $this->check ($pValue)) !== true){
			throw new CopixValidatorException (new CopixErrorObject ($this->_getMessage ($pValue, $result)));
		}
	}

	abstract protected function _validate ($pValue);

	protected function _reportErrors ($pErrors){
    	$errors = array();
    	if(isset($pErrors['missing'])) {
    		$errors[] = _i18n('copix:copix.error.tag.missingParameters', implode(",", array_keys($pErrors['missing'])));
    	}
    	if(isset($pErrors['unknown'])) {
    		$errors[] = _i18n('copix:copix.error.tag.unknownParameters', implode(",", array_keys($pErrors['unknown'])));
    	}
    	if(isset($pErrors['invalid'])) {
    		$errors[] = _i18n('copix:copix.error.tag.invalidValues', implode(",", array_keys($pErrors['invalid'])));
    	}
    	throw new CopixException('[Validator '.$this->_getName ().']: '.implode("; ", $errors));
	}
}
?>