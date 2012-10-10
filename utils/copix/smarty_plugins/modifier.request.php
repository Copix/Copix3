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
 * Purpose: Format get a request data  
 * Input: var
 * Output : _request(var)  
 */
function smarty_modifier_request ($string) {
    return _request($string);
}