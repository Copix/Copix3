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
 * Validation d'un email
 * @package		copix
 * @subpackage	validator
 */
class CopixValidatorEmail extends CopixAbstractValidator {
	/**
	 * Methode qui fait les tests sur la $pValue
	 *
	 * @param mixed $pValue La valeur
	 */
	protected function _validate ($pValue) {
		try {
			CopixFormatter::getMail ($pValue);
		} catch (CopixException $e) {
			return $e->getMessage ();
		}
		return true;
	}
}
?>