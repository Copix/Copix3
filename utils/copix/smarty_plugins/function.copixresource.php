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
* Purpose:  génération du chemin d'une ressoruce pour les templates
*
* Input:    path=le/chemin/de/la/ressource.ext
*/
function smarty_function_copixresource($params, &$me) {
	$toReturn = _resource ($params['path']);
	
	$assign = isset ($params['assign']) ? $params['assign'] : null;
	if (strlen($assign) > 0){
		$me->assign($assign, $toReturn);
		return '';
	}else{
		return $toReturn;
	}
}
?>