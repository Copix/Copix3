<?php
/**
 * @package 	spinbutton
 * @author		Duboeuf Damien
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Plugin smarty type fonction
 * Purpose:  Javacript spinbutton.
 *
 * Input:    assign   = (optional) name of the template variable we'll assign
 *                      the output to instead of displaying it directly
 *           OTHERS : Go view a tag comment
 */
function smarty_function_spinbutton($params, & $me) {
	if (isset ($params['assign'])){
		$assignVar = $params['assign'];
		unset ($params['assign']);
		$me->assign ($assignVar, _tag ('spinbutton|spinbutton', $params));
		return;
	}else{
		return _tag ('spinbutton|spinbutton', $params);
	}
}
