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
 * Just focus the form element of the given id
 * 
 * <code>
 *   {formfocus id="field_id"}
 * </code>
 */
function smarty_function_formfocus($params, $smarty) {
   if (isset ($params['assign'])) {
      $smarty->assign($params['assign'], _tag ('formfocus', $params));
   }else {
      return _tag ('formfocus', $params);
   }
}