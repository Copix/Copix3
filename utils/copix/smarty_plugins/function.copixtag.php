<?php
/**
 * @package 	copix
 * @subpackage	smarty_plugins
* @author		Salleyron Julien
* @copyright	2001-2007 CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* Plugin smarty type fonction pour encapsuler tous les appels aux tag copix
* 
* <code>
*  {copixtag type="select" selected=$value values=$values}
* </code>
* 
* @see CopixTpl::tag
*/
function smarty_function_copixtag($params, &$me) {
	$toReturn = '';
	if (file_exists (COPIX_PATH.'taglib/'.strtolower ($params['type']).'.templatetag.php')) {	
	    $toReturn = _tag ( $params['type'], $params);
	}
	return $toReturn;
}
?>