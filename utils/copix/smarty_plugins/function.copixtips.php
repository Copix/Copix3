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
 * Génération de liste de tips warning
 *
 * Input: tips liste des astuces
 * 		  warning liste des warning
 * 		  title titre a mettre pour les astuces
 * 		  titilei18n titre a mettre en i18n
 */
function smarty_function_copixtips($params, $me) {
    if (isset ($params['assign'])) {
        $me->assign($params['assign'], _tag ('copixtips', $params));
    }else {
        return _tag ('copixtips', $params);
    }
}