<?php
/**
* @package 	copix
* @subpackage	smarty_plugins
* @author		Favre Brice
* @copyright	2001-2007 CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
 
/**
 * Plugin smarty type modifier
 * Purpose:  encode a string (a href)
 * Input: caption
 * Output: utf8_encode(caption)
 * Example:  {$url|url}
 * @return string
 */
function smarty_modifier_utf ($string) {
   return utf8_encode($string);
}
?>