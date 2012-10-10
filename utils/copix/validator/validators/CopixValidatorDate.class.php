<?php
/**
 * @package		copix
 * @subpackage	validator
 * @author		Favre Brice, Salleyron Julien
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Validation d'un email
 * @package		copix
 * @subpackage	validator
 */

class CopixValidatorDate extends CopixAbstractValidator {
	
	/**
	 * Methode qui fait les tests sur la $pValue
	 *
	 * @param mixed $pValue La valeur
	 * Paramètres pouvant être passée :
	 * date_format : format de la date
	 * date_sup et date_inf permettre de définir un intervalle de date 
	 * entre lesquelles la date doit être située
	 */
	protected function _validate ($pValue) {
		$toReturn = array ();

		// Paramètres d'intervalle de date
		$date_sup = CopixDateTime::dateToYYYYMMDD ($this->getParam ('max'));
		$date_inf = CopixDateTime::dateToYYYYMMDD ($this->getParam ('min'));  
		
		if (($date = CopixDateTime::dateToYYYYMMDD ($pValue)) === false){
			return _i18n ('copix:copixdatetime.validator.invaliddate', $pValue);
		}

		if (!is_null ($date_sup)) {
			if ($date_sup < $date) {
				 $toReturn[] = _i18n ('copix:copixdatetime.validator.datesup', array($pValue, $date_sup));
			}
		}

		if (!is_null ($date_inf)) {
			if ($date_inf > $date) {
				 $toReturn[] = _i18n ('copix:copixdatetime.validator.dateinf', array ($pValue, $date_inf));
			}
		}
		return empty ($toReturn) ? true : $toReturn;
	}
}
?>