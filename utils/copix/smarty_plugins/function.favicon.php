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
* Plugin smarty type fonction
* Purpose: adds a favicon to the page (in the HTML Header)
*
* Input:    src = the image source
* Examples:
* {favicon src='./img/copix/favicon.ico' }
*/
function smarty_function_favicon ($params, &$me) {
    extract ($params);

    //are there any values given ?
    if (empty ($src)) {
        $me->_trigger_fatal_error("[plugin favicon] parameter 'src' cannot be empty");
        return;
    }
    CopixHTMLHeader::addFavIcon ($src);
}
?>