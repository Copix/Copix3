<?php
/**
 * @package 	copix
 * @subpackage	smarty_plugins
 * @author		Croës Gérald
 * @copyright	2001-2006 CopixTeam
 * @link		http://www.copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Tag permettant de générer rapidement la partie en tête d'un tableau HTML
 *
 * @param array  $params tableau de paramètres
 * @param Smarty $smarty l'objet smarty qui utilise le tag en question
 * @return string
 */
function smarty_function_table_head ($params, &$smarty){
    if (isset ($params['assign'])) {
        $me->assign ($params['assign'], _tag ('table_head', $params));
    }else {
        return _tag ('table_head', $params);
    }
}