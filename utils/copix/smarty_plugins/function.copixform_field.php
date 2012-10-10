<?php
/**
 * @package 	copix
 * @subpackage	smarty_plugins
 * @author		Croës Gérald
 * @copyright	2001-2006 CopixTeam
 * @link			http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
* Plugin smarty type fonction
* Purpose:  generation of a copixed url
*
* Input:    dest=module|desc|action
*           complete syntax will be:
*           desc|action for current module, desc and action
*           [action or |action] default desc, action
*           [|desc|action] project, desc and action
*           [||action] action in the project
*           [module||action] action in the default desc for the module
*           [|||] the only syntax for the current page
*
*           * = any extra params will be used to generate the url
*
*/
function smarty_function_copixform_field($params, &$me) {	
	
    if (isset ($params['assign'])){
        $me->assign ($params['assign'], _tag ('copixformfield', $params));
        return "";
    }
    return _tag ('copixformfield', $params);
}