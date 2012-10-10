<?php
/**
* @package      copix
* @subpackage	utils
* @author       Croës Gérald
* @copyright    CopixTeam
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Classe de représentation des constantes pour les code retours HTTP
 * @package copix
 * @subpackage utils
 */
class CopixHTTPHeader {
	/**
	 * Retourne les informations pour le messgae not found
	 * @return array of strings
	 */
   public static function get404 (){
      return array ("HTTP/1.1 404 Not found", "Status: 404 Not found");
   }
   
   /**
    * Retourne les informations pour le message 'accès interdit'
    * @return array of strings
    */
   public static function get403 (){
      return ('HTTP/1.1 403 Forbidden');
   }
}
?>