<?php
/**
* @package 		copix
* @subpackage	smarty_plugins
* @author		Croës Gérald
* @copyright	CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
 
/**
 * Transforme une chaine de caractère en tableau. 
 * @return string
 */
function smarty_modifier_toarray ($string) {
	$exploded = explode (';', $string);
	$array = array ();

	foreach ($exploded as $item){
		$item = explode ('=>', $item);
		if (count ($item) == 2){
			$array[$item[0]] = $item[1];
		}else{
			$array[] = $item[0];
		}
	}
	return $array;
}
?>