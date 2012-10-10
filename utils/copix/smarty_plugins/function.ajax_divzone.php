<?php
/**
 * @package 	copix
 * @subpackage	smarty_plugins
 * @author		Salleyron Julien
 * @copyright	2001-2006 CopixTeam
 * @link			http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Div d'une zone chargé en ajax

 */
function smarty_function_ajax_divzone ($params, &$smarty) {
    if (isset ($params['assign'])) {
        $me->assign($params['assign'], _tag ('ajax_divzone', $params));
    }else {
        return _tag ('ajax_divzone', $params);
    }
}
?>