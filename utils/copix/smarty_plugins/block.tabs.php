<?php
/**
 * @package 		copix
 * @subpackage 	smarty_plugins
 * @author			Steevan BARBOYON
 * @copyright		2001-2007 CopixTeam
 * @link				http://copix.org
 * @license			http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Smarty {tabs}{/tabs} block plugin
 *
 * Type:     block function
 * Name:     tabs
 * Purpose:  make tabs with ul / li and css styles
 * @param ul_class: string -> class du tag ul
 * @param li_class: string -> class du tag li, si non selectionne
 * @param li_class_selected: string -> class du tag li, si selectionne
 * @param values: string -> url*caption|url*caption, lien et texte de chaque onglet
 * @param selected: string -> url de l'onglet selectionne
 * @return string -> html du ul / li
 */
function smarty_block_tabs($params, $content, &$me) {
    if (is_null ($content)){
        return;
    }
    if (isset ($params['assign'])){
        $me->assign ($params['assign'], _tag ('tabs', $params, $content));
    }
    return _tag ('tabs', $params, $content);
}