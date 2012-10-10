<?php
/**
 * @package     copix
 * @subpackage  smarty_plugins
 * @author       Duboeuf Damien
 * @copyright    2001-2007 CopixTeam
 * @link         http://copix.org
 * @license      http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Plugin smarty type fonction
 * Purpose:  generate a no-spam image.
 *
 * Input:    id = (required)  id of the image must be uniq
 *
 */
function smarty_function_imageprotect($params, & $me) {
    return _tag ('imageprotect', $params);
}
?>