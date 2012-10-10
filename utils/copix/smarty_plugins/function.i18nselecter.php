<?php
/**
 * @package      copix
 * @subpackage   smarty_plugins
 * @author       Duboeuf Damien
 * @copyright    2001-2007 CopixTeam
 * @link         http://copix.org
 * @license      http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Plugin smarty type fonction
 * Purpose:  generate a i18n menu.
 *
 * Input:    name    = (required)  name of the image must be uniq
 * Input:    id      = (required)  id of the image must be uniq
 * Input:    caption = caption of input text
 * 
 */
function smarty_function_i18nselecter($params, & $me) {
    return _tag ('i18nselecter', $params);
}
?>