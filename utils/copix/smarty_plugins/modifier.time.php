<?php
/**
* @package 	copix
* @subpackage	smarty_plugins
* @author		Croes Gérald see copix.org for other contributors.
* @copyright	2001-2006 CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Plugin smarty type modifier
 * Purpose:  format a time given by its timestamp (HHMMSS) to a time (HH:MM:SS)
  * if an incorrect date is given, returns the string without any modification
 * Input: HHMMSS
 * Output: HH:MM:SS
 * Example:  {$time|time}
 * @return string
 */
function smarty_modifier_time($string) {
    return (($time = CopixDateTime::hhmmssToTime ($string)) !== false) ? $time : $string;
}