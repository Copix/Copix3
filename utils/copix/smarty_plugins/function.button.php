<?php
/**
 * @package copix
 * @subpackage smarty_plugins
 * @author Stevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Bouton d'action
 */
function smarty_function_button ($pParams, &$pSmarty) {
    if (isset ($pParams['assign'])) {
        $pSmarty->assign ($pParams['assign'], _tag ('button', $pParams));
    } else {
        return _tag ('button', $pParams);
    }
}