<?php
/**
 * @package		copix
 * @subpackage	validator
 * @author		Salleyron Julien
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Validation d'apres une preg
 * @package		copix
 * @subpackage	validator
 */
class CopixValidatorCallback extends CopixAbstractValidator {
	
	/**
	 * Methode qui fais les tests sur la $pValue
	 *
	 * @param mixed $pValue La valeur
	 */
	protected function _validate ($pValue) {
		$callback = $this->getParam ('callback');
		if (call_user_func ($callback, $pValue) !== true) {
			return _i18n ('copix:copixvalidator.validator.callback');
		}
		return true;
	}
	
	public function __construct ($pParams = array (), $pMessage = null) {
		if ($pParams == null) {
			throw new CopixException (_i18n ('copix:copixvalidator.validator.nocallback'));
		}
		if (!is_array ($pParams)) {
			$pParams = array ('callback'=>$pParams); 
		}
		parent::__construct ($pParams, $pMessage);
	}
}

?>