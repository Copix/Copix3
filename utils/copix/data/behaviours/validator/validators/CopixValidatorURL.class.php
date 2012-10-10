<?php
/**
 * @package copix
 * @subpackage validator
 * @author Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Vérifie qu'une adresse est valide
 * 
 * @package copix
 * @subpackage validator
 */
class CopixValidatorURL extends CopixAbstractValidator {
	/**
	 * Valide une adresse
	 *
	 * @param string $pValue
	 * @return mixed
	 */
	protected function _validate ($pValue) {
		$toReturn = array ();
		$value = strtolower ($pValue);
		
		if (!preg_match ('$(http|https)://.*$', $value)) {
			$toReturn[] = _i18n ('copix:copixvalidator.url.invalid', $pValue);
		}

		return (count ($toReturn) == 0) ? true : $toReturn;
	}
}