<?php
/**
 * @package		copix
 * @subpackage	smarty_plugins
 * @author		Duboeuf
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Voir copixlanguagechooser.templatetag.php
 */
function smarty_function_copixlanguagechooser ($params, $me) {
	if (isset ($params['assign'])) {
		$me->assign ($params['assign'], _tag ('error', $params));
	} else {
		return _tag ('selectcomplexe|copixlanguagechooser', $params);
	}
}