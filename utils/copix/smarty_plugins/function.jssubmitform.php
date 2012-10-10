<?php
/**
 * @package 	copix
 * @subpackage	smarty_plugins
* @author		Bertrand Yan
* @copyright	2001-2006 CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/


/**
 * Plugin smarty type fonction
 * Purpose:  send form by javascript to given href.
 *
 * Input:    href     = (required)  where to send the form
 *           form     = (required) id of the form
 *           assign   = (optional) name of the template variable we'll assign
 *                      the output to instead of displaying it directly
 *
 * Examples:
 */
function smarty_function_jssubmitform($params, $me) {
   if (isset ($params['assign'])) {
      $me->assign($params['assign'], _tag ('jssubmitform', $params));
   }else {
      return _tag ('jssubmitform', $params);
   }
}
?>