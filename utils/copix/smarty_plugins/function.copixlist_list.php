<?php
/**
 * @package 	copix
 * @subpackage	smarty_plugins
 * @author		Salleyron Julien
 * @copyright	2001-2008 CopixTeam
 * @link		http://www.copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Plugin smarty type fonction
 */
function smarty_function_copixlist_list($params, &$me) {
	$assign = '';
	if(isset($params['assign'])){
		$assign = $params['assign'];
		unset($params['assign']);
	}

	if (!isset($params['tplvars'])) {
		$params['tplvars'] = array ();
	}

	$params['tplvars'] = array_merge ($params['tplvars'],$me->_tpl_vars);

	if (!isset($params['list'])) {
		$params['list'] = null;
	}

	$list = CopixListFactory::get ($params['list']);

	if (!isset($params['datasource'])) {
		$params['datasource'] = 'dao';
	}

	$toReturn = $list->getList ($params['datasource'],$params);

	if (strlen($assign) > 0){
		$me->assign($assign, $toReturn);
		return '';
	}else{
		return $toReturn;
	}
}
?>