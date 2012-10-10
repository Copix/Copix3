<?php
/**
 * @package copix
 * @subpackage core
 * @author Croes Gérald
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Interface pour les objets de données
 */
interface ICopixData extends ArrayAccess, Iterator, Countable {
	public function set ($pName, $pValue);
	public function & get ($pName);
	public function exists ($pName);
	public function attachBehaviour ($pName, ICopixDatabehaviour $pBehaviour);
}