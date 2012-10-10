<?php
/**
 * @package		copix
 * @subpackage	validator
 * @author		Gérald Croës, Salleyron Julien
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Interface respectée par tous les validateurs
 * @package copix
 * @subpackage validator
 */
interface ICopixValidator extends ICopixDataBehaviour {
	public function check ($pValue);
	public function assert ($pValue);
}