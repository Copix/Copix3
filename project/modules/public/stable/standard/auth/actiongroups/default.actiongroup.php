<?php
/**
 * @package standard
 * @subpackage auth 
 * 
 * @author		Gérald Croës
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Opérations de tests uniquement
 * @package standard
 * @subpackage auth
 */
class ActionGroupDefault extends CopixActionGroup {
	function processDefault (){
		return CopixActionGroup::process ('log::form');
	}
}
?>