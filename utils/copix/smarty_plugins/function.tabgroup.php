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
* Purpose: appelle d'une zone Copix directement depuis le template
*
* Input:    id=module|desc|action
*           assign=variable ou la sortie sera assignée
*           * = tout autre paramètre sera passé à la zone
*
* @param array $params les paramètres passés au tag
* @param Smarty $me l'objet Smarty en cours d'exécution
*/
function smarty_function_tabgroup ($params, &$me) {
	//On regarde si l'on assigne la sortie à un élément
	if (isset ($params['assign'])){
		$assign = $params['assign'];
		unset ($params['assign']);
	}else{
		$assign = null;
	}

	$toReturn = _tag ('tabgroup', $params);
	if (strlen ($assign) > 0) {
		$me->assign ($assign, $toReturn);
		return '';
	}else{
		return $toReturn;
	}
}