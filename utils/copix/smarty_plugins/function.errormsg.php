<?php
/**
 * @package 	copix
 * @subpackage	smarty_plugins
 * @author		Croes Gérald
 * @copyright	2001-2006 CopixTeam
 * @link			http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Plugin smarty type fonction
 * Purpose:  automated error message.
 *
 * Input:    message = (required if you want to display a message) the error message
 *           class = (optional) class css to use for the paragraph
 *           assign = (optional) text to display, default is address
 *
 * Examples: {errormsg message="Please give an adress"}
 *           {errormsg message="Please give an adress" class="redText"}
 *           {errormsg message=$Message assign=$errorMessage}
 */
function smarty_function_errormsg($params, $me) {
	if (isset ($params['assign'])) {
		$me->assign($params['assign'], _tag ('errormsg', $params));
	} else {
		return _tag ('errormsg', $params);
	}
}
?>