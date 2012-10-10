<?php
/**
 * @package		copix
 * @subpackage	utils
 * @author		Gérald Croës
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Objet pouvant être mis en Cookie
 * 
 * @package		copix
 * @subpackage	utils
 */
class CopixCookieObject extends CopixSerializableObject {
	/**
	 * Retourne l'objet directement
	 * 
	 * @return object
	 */
	public function &getCookieObject () {
		return $this->getRemoteObject ();
	}
}