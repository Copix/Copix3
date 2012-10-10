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
 * Purpose:  generation of a radio buttons group
 *
 * Input:    name     = (required  name of the select box
 *           values   = (optional) values to display the values captions will be
 *                        html_escaped, not the ids
 *           selected = (optional) id of the selected element
 *           assign   = (optional) name of the template variable we'll assign
 *                      the output to instead of displaying it directly
 *           objectMap   = (optional) if given idProperty;captionProperty
 *           extra       = (optionnal) extra parameters we may give to the radio elements
 */
function smarty_function_radiobutton($params, &$me) {
   if (isset ($params['assign'])) {
      $me->assign($params['assign'], _tag ('radiobutton', $params));
   }else {
      return _tag ('radiobutton', $params);
   }
}