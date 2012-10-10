<?php
/**
 * @package 	copix
 * @subpackage	smarty_plugins
* @author		Daclin Sylvain
* @copyright	2001-2006 CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Plugin smarty type fonction
 * Purpose:  generation of a CSV content file
*
* Input:    values           = (required) array of objects, or array of hash array
*           order            = (optional) if given, the resulting CSV file will be sorted by this fields.
*           separator        = (optional) the separator for the csv file. (default is "," [comma])
*           displayHeaders   = (optional) if we wants to output the headers
* Examples:
* {csv values=$arObjects displayHeaders=false order=$array displayHeaders=false}
*/
function smarty_function_csv($params, $me) {
   if (isset ($params['assign'])) {
      $me->assign($params['assign'], _tag ('csv', $params));
   }else {
      return _tag ('csv', $params);
   }
}