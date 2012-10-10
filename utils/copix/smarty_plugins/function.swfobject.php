<?php
/**
 * @package    copix
 * @subpackage smarty_plugins
 * @author     Guillaume Perréal
 * @copyright  2001-2008 CopixTeam
 * @link       http://copix.org
 * @license    http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* Plugin smarty type function
* Purpose: Génère une inclusion de Flash.
*
* Input:
*
* @param array $params les paramètres passés au tag
* @param Smarty $me l'objet Smarty en cours d'exécution
*/
function smarty_function_swfobject ($params, &$me) {
	
	if(isset($params['assign'])) {
		$name = $params['assign'];
		unset($params['assign']);
		$me->assign($name, _tag('swfobject', $params));
	} else {
		return _tag('swfobject', $params);
	}
}