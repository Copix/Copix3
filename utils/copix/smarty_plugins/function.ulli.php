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
 * Purpose:  generation of a combo box
 *
 * Input: values   = (required) values to display
 * Examples:
 * $tpl->assign ('values', array ('first', 'second', 'third'));
 * {ulli values=$values}
 * will output
 * <ul>
 *  <li>first</li>
 *  <li>second</li>
 *  <li>third</li>
 * </ul>
 */
function smarty_function_ulli($params, $me) {
   if (isset ($params['assign'])) {
      $me->assign($params['assign'], _tag ('ulli', $params));
   }else {
      return _tag ('ulli', $params);
   }
}

?>