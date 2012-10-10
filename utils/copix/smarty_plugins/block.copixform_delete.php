<?php
/**
 * @package 	copix
 * @subpackage 	smarty_plugins
* @author		Bertrand Yan
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
function smarty_block_copixform_delete($params, $content, &$me) {
    if (is_null ($content)) {
        return;
    }
    
    if (!isset($params['form'])) {
		$params['form'] = null;
	}
	if (isset ($params['assign']) && isset($content)){
		$me->assign ($params['assign'], CopixFormFactory::get($params['form'])->getButton ('delete', $content));
	}
	
	return CopixFormFactory::get($params['form'])->getButton ('delete', $content);
}