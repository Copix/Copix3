<?php
/**
* @package		copix
* @subpackage	smarty_plugins
* @author		Croës Gérald
* @copyright	CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Création d'une liste déroulante
 */
function smarty_function_select ($params, $me) {
	if (isset ($params['assign'])){
		$me->assign ($params['assign'], _tag ('select', $params));
	} else {
		return _tag ('select', $params);
	}
}