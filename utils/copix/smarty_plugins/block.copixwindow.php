<?php
/**
 * @package 	copix
 * @subpackage 	smarty_plugins
 * @author		Salleyron Julien
 * @copyright	2000-2006 CopixTeam
 * @link			http://www.copix.org
 * @license 		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 */
function smarty_block_copixwindow ($pParams, $pContent, &$me, $first) {
    if (is_null ($pContent) && $first === true) {
		return ;
	}
	if (isset ($pParams['assign'])){
		$me->assign ($pParams['assign'], _tag ('copixwindow', $pParams, $pContent));
	}
	return _tag ('copixwindow', $pParams, $pContent);
}