<?php
/**
 * @package 	tools
 * @subpackage 	antispam
 * @author		Duboeuf Damien
 * @copyright	2001-2010, CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Plugin smarty type fonction
 * Purpose:  captcha
 *
 * Input:    assign   = (optional) name of the template variable we'll assign
 *                      the output to instead of displaying it directly
 *           OTHERS : Go view a tag comment
 */
function smarty_function_captcha_response($params, & $me) {
	if (isset ($params['assign'])){
		$assignVar = $params['assign'];
		unset ($params['assign']);
		$me->assign ($assignVar, _tag ('spinbutton|captcha_response', $params));
		return;
	}else{
		return _tag ('spinbutton|captcha_response', $params);
	}
}
