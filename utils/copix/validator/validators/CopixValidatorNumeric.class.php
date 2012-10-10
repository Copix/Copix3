<?php
/**
 * @package copix
 * @subpackage validator
 * @author 		Croës Gérald
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file 
 */

/**
 * Validateur qui permet de vérifier qu'une valeur représente une valeur numérique
 * @package copix
 * @subpackage validator
 */
class CopixValidatorNumeric extends CopixAbstractValidator {
	/**
	 * Valide que la valeur représente un nombre
	 * 
	 * Liste des options possibles
	 * $options['max'] - Valeur maximale
	 * $options['minLength'] - Valeur minimale
	 * $options['allowDecimal']  - Autorise les valeurs flottantes 
	 *
	 * @param mixed $pValue la valeur qui doit représenter une valeur numérique
	 * @return true si ok, mixed sinon
	 */
	protected function _validate ($pValue) {
		if (CopixFilter::getNumeric ($pValue, $this->getParam ('allowDecimal', false)) != $pValue){
			return _i18n ('copix:copixvalidator.numeric.numeric', $pValue); 
		}

		$toReturn = array ();
		//si les valeurs décimales ne sont pas autorisées
		if ($this->getParam ('allowDecimal', false) == false){
			if (is_float ($pValue)){
				$toReturn[] = _i18n ('copix:copixvalidator.numeric.intval', array ($pValue));
			}
		}
		
		if ($max = $this->getParam ('max', null)){
			if ($pValue > $max){
				$toReturn[] = _i18n ('copix:copixvalidator.numeric.max', array ($max, $pValue));
			}
		}
		
		if ($min = $this->getParam ('min', null)){
			if ($pValue < $min){
				$toReturn[] = _i18n ('copix:copixvalidator.numeric.min', array ($min, $pValue));
			}
		}

		return empty ($toReturn) ? true : $toReturn;
	}
}