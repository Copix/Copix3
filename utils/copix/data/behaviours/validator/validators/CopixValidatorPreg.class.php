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
class CopixValidatorPreg extends CopixAbstractValidator {

	/**
	 * Methode qui fais les tests sur la $pValue
	 *
	 * @param mixed $pValue La valeur
	 */
	protected function _validate ($pValue) {
		$patterns = $this->getParam ('pattern');
		if (!is_array ($patterns)) {
			$patterns = array ($patterns);
		}
		$toReturn = array ();
		foreach ($patterns as $pattern) {
			if (!preg_match($pattern, $pValue)) {
				$toReturn[] = _i18n ('copix:copixvalidator.validator.preg', $pattern);
			}
		}
		return empty ($toReturn) ? true : $toReturn;
	}
	
	public function __construct ($pParams = array (), $pMessage = null) {
		if ($pParams == null) {
			throw new CopixException (_i18n ('copix:copixvalidator.validator.nopreg'));
		}
		if (!is_array ($pParams) || ! array_key_exists ('pattern', $pParams)) {
			$pParams = array ('pattern'=>$pParams); 
		}
		parent::__construct ($pParams, $pMessage);
	}
}