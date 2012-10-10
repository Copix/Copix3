<?php
/**
 * @package		copix
 * @subpackage	utils
 * @author		Croes Gérald, Steevan BARBOYON
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Objet pouvant être mis en session
 * 
 * @package		copix
 * @subpackage	utils
 */
class CopixSessionObject extends CopixSerializableObject {

        protected static $_globalReferences = array ();

        /**
	 * Retourne l'objet directement
	 * 
	 * @return object
	 */
	public function & getSessionObject () {
		return $this->getRemoteObject ();
	}
}