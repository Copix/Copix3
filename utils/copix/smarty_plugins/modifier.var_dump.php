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
 * Purpose: Format a var_dump   
 * Input: var
 * Output : <pre>var_dump(var)</pre> if xdebug is not present  
 */
function smarty_modifier_var_dump($string) {
    ob_start ();
    var_dump ($string);
    if (! extension_loaded('xdebug')){
        return '<pre>'.ob_get_clean ().'</pre>';
    }else{
        return ob_get_clean ();
    }

}

?>