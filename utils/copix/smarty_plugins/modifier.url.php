<?php
/**
* @package 	copix
* @subpackage	smarty_plugins
* @author		Croes Gérald
* @copyright	2001-2006 CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
 
/**
 * Plugin smarty type modifier
 * Purpose:  format an url (a href)
 * Input: caption|href caption is optionnal
 * Output: <a href="url">caption or url</a>
 * Example:  {$url|url}
 * @return string
 */
function smarty_modifier_url ($string) {
   $exploded = explode ('|', $string);
   if (count ($exploded) > 1){
      return '<a href="'.$exploded[1].'">'.$exploded[0].'</a>';
   }else{
      return '<a href="'.$string.'">'.$string.'</a>';
   }
}