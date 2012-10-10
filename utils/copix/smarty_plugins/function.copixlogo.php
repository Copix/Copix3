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
 * Permet d'afficher un logo 'copix' dans les sources HTML sous la forme d'un commentaire
 * 
 * input: type : big   -> the big one
 *               small -> simply made with Copix, http://copix.org
 *        default is small
 * Examples: {copixlogo}
 * Simply output the made with Copix Logo
 * -------------------------------------------------------------
 */
function smarty_function_copixlogo($params, &$smarty) {
   if (isset ($params['assign'])) {
   	$smarty->assign($params['assign'], _tag ('copixlogo', $params));
   }else {
   	return _tag ('copixlogo', $params);
   }
}