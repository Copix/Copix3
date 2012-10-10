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
 * Interface respectée par les validateur composés
 * @package copix
 * @subpackage validator
 */
interface ICopixCompositeValidator extends ICopixValidator {
	public function attach (ICopixValidator $pValidator);
}