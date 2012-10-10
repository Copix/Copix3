<?php
/**
 * @package copix
 * @subpackage validator
 * @author Croës Gérald
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file 
 */

/**
 * Validateur qui permet de vérifier qu'une valeur est comprise dans un tableau donné
 * @package copix
 * @subpackage validator
 */
class CopixValidatorInArray extends CopixAbstractValidator {
	protected function _validate ($pValue) {
		return in_array ($pValue, $this->getParam ('values'));
	}
}
?>