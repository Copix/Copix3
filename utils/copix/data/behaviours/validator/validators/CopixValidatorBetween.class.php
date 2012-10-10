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
	/**
	 * Vérifie que $pValue est située dans une plage de valeur (passée en option)
	 * 
	 * $options['min'] la valeur minimale (ou égale)
	 * $options['max'] la valeur maximale (ou égale)
	 */
	protected function _validate ($pValue) {
		$min = $this->requireParam ('min', _validator ('numeric', array ('allowDecimal'=>true)));
		$max = $this->requireParam ('max', _validator ('numeric', array ('allowDecimal'=>true)));
		if (($pValue > $max) || ($pValue < $min)){
			return _i18n ('copix:copixvalidator.beetween', array ($pValue, $min, $max));
		}
		return true;
	}
}