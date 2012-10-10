<?php
/**
 * @package 	copix
 * @subpackage	smarty_plugins
 * @author		Sabine CIMAPONTI
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */


/**
 * Tag permettant de générer rapidement une image et un lien sur une image dans img/tools/
 */
function smarty_function_copixicon ($params, $me) {
	if (isset ($params['assign'])) {
		$assignVar = $params['assign'];
		unset ($params['assign']);
        $me->assign ($assignVar, _tag ('copixicon', $params));
    }else {
        return _tag ('copixicon', $params);
    }
}
?>