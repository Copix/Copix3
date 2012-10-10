<?php
/**
 * @package copix
 * @subpackage filter
 * @author Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Retourne un droit sous la forme 0766
 * 
 * @package copix
 * @subpackage filter
 */
class CopixFilterOctalPermissions extends CopixAbstractFilter {
	/**
	 * Retourne un droit sous la forme 0766
	 *
	 * @param string $pValue
	 * @return string
	 */
	public function get ($pValue) {
		// base du code depuis php.net
		return substr (sprintf ('%o', $pValue), -4);
	}	
}