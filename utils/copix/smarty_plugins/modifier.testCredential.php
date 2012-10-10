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
 * Plugin smarty type modifier
 * Purpose: test credential   
 * Input: var
 * Output : CopixAuth::getCurrentUser()->testCredential (var)  
 */
function smarty_modifier_testCredential ($string) {
    return CopixAuth::getCurrentUser()->testCredential ($string);
}
?>