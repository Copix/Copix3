<?php
/**
 * @package 	copix
 * @subpackage	smarty_plugins
* @author		Daclin Sylvain
* @copyright	2001-2006 CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Smarty plugin
 * -------------------------------------------------------------
* Type:     function
* input: type :
* Examples: {htmleditor name="text_content" content="Default XHTML content"}
*
* includes the required library for the js library fckeditor
* you can find this library at http://www.fckeditor.net
* -------------------------------------------------------------
*/
function smarty_function_htmleditor($params, $smarty) {
	if (isset ($params['assign'])) {
    	$smarty->assign($params['assign'], _tag ('htmleditor', $params));
	}else {
		return _tag ('htmleditor', $params);
	}
}