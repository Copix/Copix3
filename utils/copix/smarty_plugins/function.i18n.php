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
 * Purpose:  I18N interface for CopiX.
 *
 * Input:    key      = (required  name of the select box
 *           bundle   = (optional) values to display the values captions will be
 *                        html_escaped, not the ids
 *           lang      = (optional) id of the selected element
 *           assign   = (optional) name of the template variable we'll assign
 *                      the output to instead of displaying it directly
 *
 * Examples:
 */
function smarty_function_i18n($params, & $me) {
	if (isset ($params['assign'])){
		$assignVar = $params['assign'];
		unset ($params['assign']);
		$me->assign ($assignVar, _tag ('i18n', $params));
		return;
	}else{
		return _tag ('i18n', $params);
	}
}
?>