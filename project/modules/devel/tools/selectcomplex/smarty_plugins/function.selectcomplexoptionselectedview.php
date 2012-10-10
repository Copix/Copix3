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
function smarty_function_selectcomplexoptionselectedview ($params, $me) {
	if (isset ($pParams['assign'])){
		$me->assign ($pParams['assign'], '{selectcomplexselectedview}');
	}
	return '{selectcomplexoptionselectedview}';
}