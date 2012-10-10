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
 * Purpose:  get the current url.
 *
 * Input:   assign   = (optional) name of the template variable we'll assign
 *                      the output to instead of displaying it directly
 *
 * Examples:
 */
function smarty_function_currenturl($params, &$me) {
	if (isset($params['notxml'])){
		$isxml = ($params['notxml'] == 'true' ? false : true);
	}else{
		$isxml = true;
	}

	$assign = CopixUrl::getCurrentUrl ($isxml);
	if (isset ($params['assign'])){
		$me->assign($params['assign'], $assign);
		return '';
	}else{
		return $assign;
	}
}
?>