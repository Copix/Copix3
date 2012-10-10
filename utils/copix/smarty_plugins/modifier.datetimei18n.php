<?php
/**
* @package 		copix
* @subpackage	smarty_plugins
* @author		Croes Gérald
* @copyright	CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Plugin smarty type modifier
 * Purpose:  format a date given by its timestamp (YYYMMDD) to a date according
 *   to the current languages settings
 * if an incorrect date is given, returns the string without any modification
 * Input: YYYYMMDDHHIISS
 * Output: (french) DD/MM/YYYY HH:II:SS, (english) MM/DD/YYYY HH:II:SS
 * Example:  {$date|datetimei18n}
 * @return string
 */
function smarty_modifier_datetimei18n ($string, $format="") {
   if ($format == ""){
      return (($date = CopixDateTime::yyyymmddhhiissToDateTime ($string)) !== false) ? $date : $string;
   }
   return (($date = CopixDateTime::yyyymmddhhiissToText ($string)) !== false) ? $date : $string;
}
?>