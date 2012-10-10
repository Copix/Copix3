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
function smarty_function_autocomplete($params, $me) {
    
    if (!isset ($params['datasource'])) {
        $params['datasource'] = 'dao';
    }

    if (!isset ($params['field'])) {
        throw new Exception ('Vous devez remplir le champ "field"');
    }
    
    if (isset ($params['assign'])) {
        $me->assign($params['assign'], _tag ('autocomplete', $params));
    }else {
        return _tag ('autocomplete', $params);
    }
}