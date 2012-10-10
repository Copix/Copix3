<?php
/**
 * @package    standard
 * @subpackage test
 * @author	   Gérald Croës
 * @copyright  CopixTeam
 * @link       http://copix.org
 * @licence    http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Ce filtre retourne toujours "Copix" (destiné uniquement aux tests unitaires)
 * @package standard
 * @subpackage test
 */
class FilterTest extends CopixAbstractFilter {
	public function get ($pValue){
		return 'Copix';
	} 
}