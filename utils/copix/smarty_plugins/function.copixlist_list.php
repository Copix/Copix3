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
	if (!isset($params['list'])) {
		$params['list'] = null;
	}
	$list = CopixListFactory::get ($params['list']);
	
	$assign = null;
	if (isset($params['assign'])){
		$assign = $params['assign'];
		unset($params['assign']);
	}

	if (isset ($params['template'])){
		$toReturn = $list->getHTML ($params['template']);
	}else{
		$toReturn = $list->getHTML ();
	}

	if ($assign){
		$me->assign ($assign, $toReturn);
		return '';
	}
	return $toReturn;
}