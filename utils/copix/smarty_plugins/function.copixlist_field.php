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
function smarty_function_copixlist_field($params, &$me) {
	$assign = '';
	if(isset($params['assign'])){
		$assign = $params['assign'];
		unset($params['assign']);
	}

	if (!isset($params['list'])) {
        $params['list'] = null;
	}
	$list = CopixListFactory::get ($params['list']);
	
	if (!isset($params['name'])) {
		throw new Exception(_i18n ('copix:copixlist.message.needType'));
	}
	$type = (isset ($params['type']) ? $params['type'] : 'varchar');
	$toReturn = $list->getField ($type, $params);

	
	if (strlen($assign) > 0){
		$me->assign($assign, $toReturn);
		return '';
	}else{
		return $toReturn;
	}
}