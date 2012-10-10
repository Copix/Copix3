<?php
/**
 * @package		copix
 * @subpackage	validator
 * @author		Favre Brice, Salleyron Julien, Croës Gérald
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Validation d'une date
 * 
 * @package		copix
 * @subpackage	validator
 */
class CopixValidatorDate extends CopixAbstractValidator {
	/**
	 * Vérifie que la date donnée respecte les options passées au constructeur
	 * 
	 * $options['min'] Date minimale possible (ou égale) 
	 * $options['max'] Date maximale possible (ou égale)
	 * 
	 * @param mixed $pValue La valeur
	 */
	protected function _validate ($pValue) {
		$toReturn = array ();
		
		// Paramètres d'intervalle de date
		$date_sup = $this->_getDate ($this->getParam ('max'));
		$date_inf = $this->_getDate ($this->getParam ('min'));  
		
		if (($date = $this->_getDate ($pValue)) === false){
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
	
	/**
	 * Récupère la date en considérant le format de fonctionnement donné au constructeur
	 *
	 * @param string $pValue la date a récupérer
	 * @return string la date au format YYYYMMDD/YYYYMMDDHHIISS / HHIISS
	 */
	protected function _getDate ($pValue){
		switch ($this->getParam ('format', 'date', new CopixValidatorInArray (array ('values'=>array ('yyyymmdd', 'yyyymmddhhiiss', 'date', 'datetime', 'timestamp'))))){
			case 'date':
				return CopixDateTime::dateToYYYYMMDD ($pValue);
			case 'datetime':
				return CopixDateTime::DateTimeToyyyymmddhhiiss ($pValue);
			case 'timestamp':
				return CopixDateTime::timeStampToyyyymmddhhiiss ($pValue);
			case 'yyyymmddhhiiss':
				if (CopixDateTime::yyyymmddhhiissToTimeStamp ($pValue) !== false){
					$pValue = substr ($pValue, 0, 8);
				}else{
					return false;
				}	
			case 'yyyymmdd':
				return $pValue;
		}
	}
}