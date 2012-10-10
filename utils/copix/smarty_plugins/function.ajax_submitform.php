<?php
/**
 * @package 	copix
 * @subpackage	smarty_plugins
 * @author		Steevan BARBOYON
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Vérifie les données saisies dans un formulaire, et le soumet si tout est ok
 */
function smarty_function_ajax_submitform ($params, &$smarty) {
	return _tag ('ajax_submitform', $params);
}
?>