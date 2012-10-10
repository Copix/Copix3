<?php
/**
 * @package 	mooxolorpicker
 * @author		Duboeuf Damien
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Plugin smarty type fonction
 * Purpose:  Javacript colorpicker.
 *
 * Input:    assign   = (optional) name of the template variable we'll assign
 *                      the output to instead of displaying it directly
 *           OTHERS : Go view a tag comment
 */
function smarty_function_moocolorpicker($params, & $me) {
	if (isset ($params['assign'])){
		$assignVar = $params['assign'];
		unset ($params['assign']);
		$me->assign ($assignVar, _tag ('moocolorpicker|moocolorpicker', $params));
		return;
	}else{
		return _tag ('moocolorpicker|moocolorpicker', $params);
	}
}