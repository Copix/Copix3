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
 * Purpose:  format a date given by its timestamp (YYYMMDD) to a date according
 *   to the current languages settings
 * if an incorrect date is given, returns the string without any modification
 * Input: YYYYMMDD
 * Output: (french) DD/MM/YYYY, (english) MM/DD/YYYY
 * Output plain text date if text is given as an extra parameter
 *
 * Example:  {$date|datei18n}
 *           {$date|datei18n:text}
 * 
 * @return string
 */
function smarty_modifier_datei18n($string, $format="") {
   if ($format == ""){
      return (($date = CopixDateTime::yyyymmddToDate ($string)) !== false) ? $date : $string;
   }elseif ($format == "text"){
      return (($date = CopixDateTime::yyyymmddToText ($string)) !== false) ? $date : $string;
   }
}
?>