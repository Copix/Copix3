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
 * @param string contents of the block
 * @param Smarty clever simulation of a method
 * @return string string $content re-formatted
 */
function smarty_block_copixform_button($params, $content, &$me) {
	
    if (!isset($content)) {
        return null;
    }
    
    if (!isset($params['form'])) {
		$params['form'] = null;
	}
	if (!isset($params['type'])) {
	    throw new CopixException ( _i18n ('copix:copixform.button.typeundefined'));
	}
	if (isset ($params['assign'])){
		$me->assign ($params['assign'], CopixFormFactory::get ($params['form'])->getButton ($params['type'], $content));
	}
	return CopixFormFactory::get ($params['form'])->getButton ($params['type'], $content);
}