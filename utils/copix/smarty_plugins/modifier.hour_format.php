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
 * Purpose:  format an hour (HHMMSS) string
 * Input:
 * Example:  {$hour|hour_format:"%H:%i:%s"}
 * @param string  $hour  string to convert
 * @param string  $format the format we wants to display the hour with
 * @return string
 */
function smarty_modifier_hour_format($string, $format="%H:%i:%s") {
   $hour    = substr ($string, 0, 2);
   $minute  = substr ($string, 2, 2);
   $seconds = strlen ($string) == 6 ? substr ($string, 4, 2) : 0;
   return str_replace (array ('%H', '%i', '%s'), array ($hour, $minute, $seconds), $format);
}