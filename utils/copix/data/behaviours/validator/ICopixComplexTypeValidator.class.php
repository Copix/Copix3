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
 * Interface respectée par les validateurs de type complexes
 * @package copix
 * @subpackage validator
 */
interface ICopixComplexTypeValidator extends ICopixValidator {
	public function attachTo (ICopixValidator $pValidator, $pPropertyPath);
	public function required ($pPropertyPath);
}