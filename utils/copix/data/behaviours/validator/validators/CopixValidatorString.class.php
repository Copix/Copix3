<?php
/**
 * @package     copix
 * @subpackage  validator
 * @author      Croës Gérald
 * @copyright   CopixTeam
 * @link        http://copix.org
 * @license     http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file 
 */

/**
 * Validateur qui permet de vérifier qu'une valeur est une chaine de caractères avec un certain nombre d'options
 * @package copix
 * @subpackage validator
 */
class CopixValidatorString extends CopixAbstractValidator {
	/**
	 * Valide que la valeur soit une chaine de caractères
	 * 
	 * Liste des options possibles
	 * $options['maxLength'] - Taille maximale
	 * $options['minLength'] - Taille maximale
	 * $options['contains']  - Sous chaine obligatoire
	 * $options['null']      - indique si les valeurs nulles sont autorisées ou non 
	 *
	 * @param mixed $pValue ce qui doit être une chaine de caractères
	 * @return true si ok, mixed sinon
	 */
	protected function _validate ($pValue) {
		if ($this->getParam ('null') == true){
			if ($pValue === null){
				return true;
			}
		}

		if (!is_string ($pValue)){
			return _i18n ('copix:copixvalidator.string.string', $pValue);			
		}

		$toReturn = array ();
		if ($length = $this->getParam ('maxLength', null)){
			if (strlen ($pValue) > $length){
				$toReturn[] = _i18n ('copix:copixvalidator.string.maxlength', array ($length, strlen ($pValue)));
			}
		}

		if ($length = $this->getParam ('minLength', null)){
			if (strlen ($pValue) < $length){
				$toReturn[] = _i18n ('copix:copixvalidator.string.minlength', array ($length, strlen ($pValue)));
			}
		}

		if ($substr = $this->getParam ('contains', null)){
			if (strpos ($pValue, $substr) === false){
				$toReturn[] = _i18n ('copix:copixvalidator.string.expectToFind', array ($substr, $pValue));
			}
		}
		return empty ($toReturn) ? true : $toReturn;
	}
}