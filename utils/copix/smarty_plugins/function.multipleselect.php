<?php
/**
* @package		copix
* @subpackage	smarty_plugins
* @author		Salleyron Julien
* @copyright	CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Création d'une liste déroulante à séléction multiple
 */
function smarty_function_multipleselect ($params, $me) {
	if (isset ($params['assign'])){
		$me->assign ($params['assign'], _tag ('multipleselect', $params));
	} else {
		return _tag ('multipleselect', $params);
	}
}
?>