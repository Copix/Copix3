<?php
/**
 * @package 	copix
 * @subpackage 	smarty_plugins
* @author		Salleyron Julien
* @copyright	2001-2006 CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Smarty {popupinformation}{/popupinformation} block plugin
 *
 * Type:     block function<br>
 * Name:     popupinformation<br>
 * Purpose:  Add div wich display when mouse is over img<br>
 * @param array
 * 
 * <pre>
 * Params:   img: string
 * Params:   text: string
 * Params:   divclass: (optional)string, css class
 * Params:   displayimg: (optional, default true)boolean, display img ?
 * Params:   displaytext: (optional, default false) boolean , displaty text after img ?
 * Params:   assign :(optional) name of the template variable we'll assign
 *                      the output to instead of displaying it directly
 * </pre>
 * 
 * @param string contents of the block
 * @param Smarty clever simulation of a method
 * @return string string $content re-formatted
 */
function smarty_block_copixlist_button($params, $content, &$me) {
    if (!isset($content)) {
        return null;
    }
    if (!isset($params['list'])) {
		$params['list'] = null;
	}
	if (!isset($params['type'])) {
	    throw new CopixException ( _i18n ('copix:copixlist.button.typeundefined'));
	}
	if (isset ($params['assign'])){
		$me->assign ($params['assign'], CopixListFactory::get ($params['list'])->getButton ($params['type'], $content));
	}
	return CopixListFactory::get ($params['list'])->getButton ($params['type'], $content);
}
?>