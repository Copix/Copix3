<?php
/**
 * @package 	copix
 * @subpackage	smarty_plugins
 * @author		Gérald Croës
 * @copyright	2001-2006 CopixTeam
 * @link			http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Permet l'édition d'un élément de type date avec un petit calendrier sympa
 * <code>
 *  {calendar name="test_date"}
 * </code>
 */
function smarty_function_calendar ($params, &$smarty) {
	return _tag ('calendar', $params);
}
?>