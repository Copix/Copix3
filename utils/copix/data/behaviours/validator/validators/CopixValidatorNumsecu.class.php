<?php
/**
 * @package		copix
 * @subpackage	validator
 * @author		Brice Favre
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Validation d'un numéro de téléphone
 * @package		copix
 * @subpackage	validator
 */
class CopixValidatorNumsecu extends CopixAbstractValidator {
	
	/**
	 * Methode qui fait les tests sur la $pValue
	 *
	 * @param mixed $pValue La valeur
	 */
	public function _validate ($pValue) {
		// Récupération des paramètres
		$gender = $this->getParam ('gender', null);	
		$birth_month = $this->getParam ('birth_month', null);
		$birth_year = $this->getParam ('birth_year', null);
		if ($pValue == '') {
			return true;
		}
		try {
			CopixFormatter::getNumeroSecuriteSociale (substr ($pValue, 0, -2), substr ($pValue, -2));
		} catch (CopixException $e) {
			return $e->getMessage ();
		}
		
		// Retourne un tableau contenant :
		// Champ 0 => les 5 premiers caractères
		// Champ 1 => Le genre (1 ou 2)
		// Champ 2 => L'annee de naissance 
		// Champ 3 => Le mois de naissance
		preg_match ('/^(\d{1})(\d{2})(\d{2})/', $pValue, $arMatches);
		if ($gender !== null && $arMatches[1] != $gender){
			return _i18n ('copix:copixformatter.error.incorrectdatasecu');
		}
		if ($birth_year != null && $arMatches[2] != $birth_year){
			return _i18n ('copix:copixformatter.error.incorrectdatasecu');
		}
		if ($birth_month != null && $arMatches[3] != $birth_month){
			return _i18n ('copix:copixformatter.error.incorrectdatasecu');
		}
		return true;
	}
}