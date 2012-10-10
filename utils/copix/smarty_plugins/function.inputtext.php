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
 * Plugin smarty type fonction
 * Génération d'input text
 *
 */
function smarty_function_inputtext($params, $me) {
    if (isset ($params['assign'])) {
        $me->assign($params['assign'], _tag ('inputtext', $params));
    }else {
        return _tag ('inputtext', $params);
    }
}