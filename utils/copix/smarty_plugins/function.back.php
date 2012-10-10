<?php
/**
* @package		copix
* @subpackage	smarty_plugins
* @author		Croes Gérald
* @copyright	CopixTeam
* @link			http://www.copix.org
* @license  	http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/


/**
 * Lien retour
 */
function smarty_function_back ($pParams, &$smarty) {
	return _tag ('back', $pParams);
}