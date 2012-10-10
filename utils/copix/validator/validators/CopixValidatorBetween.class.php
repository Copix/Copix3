<?php
/**
 * @package		copix
 * @subpackage	validator
 * @author 		Croës Gérald
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file 
 */

/**
 * Validateur qui permet de vérifier qu'une valeur est comprise entre une valeur minimum et maximum
 * @package copix
 * @subpackage validator
 */
class CopixValidatorBetween extends CopixAbstractValidator {
	protected function _validate ($pValue) {
		$min = $this->requireParam ('min');
		$max = $this->requireParam ('max');
		$this->validateParams ();
		
		if (($pValue > $max) || ($pValue < $min)){
			return _i18n ('copix:copixvalidator.beetween', array ($pValue, $min, $max));
		}
		return true;
	}
}
?>