<?php
/**
 * @package 	copix
 * @subpackage	smarty_plugins
 * @author		Croës Gérald
 * @copyright	2001-2006 CopixTeam
 * @link			http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
* Plugin smarty type fonction
* Purpose:  generation of a copixed url
*
* Input:    dest=module|desc|action
*           complete syntax will be:
*           desc|action for current module, desc and action
*           [action or |action] default desc, action
*           [|desc|action] project, desc and action
*           [||action] action in the project
*           [module||action] action in the default desc for the module
*           [|||] the only syntax for the current page
*
*           * = any extra params will be used to generate the url
*
*/
function smarty_function_copixform_field($params, &$me) {

	$assign = '';
	if(isset($params['assign'])){
		$assign = $params['assign'];
		unset($params['assign']);
	}

	if (!isset($params['form'])) {
		$params['form'] = null;
	}
	
	if ($params['form'] instanceof  CopixForm) {
		$form = $params['form'];
	} else {
		$form = CopixFormFactory::get ($params['form']);
	}
	unset ($params['form']);
	
	
	if (!isset ($params['name'])) {
		throw new CopixTemplateTagException ("[plugin copixform_field] parameter 'name' cannot be empty");
	}
	$name = $params['name'];
	unset ($params['name']);
	
	$toReturn = $form->getRenderer ()->field ($name, $params);
	
	if (strlen($assign) > 0){
		$me->assign($assign, $toReturn);
		return '';
	}else{
		return $toReturn;
	}
}
?>