<?php
/**
 * @package copix
 * @subpackage smarty_plugins
 * @author Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license	http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Début d'un block
 */
function smarty_function_beginblock ($pParams, &$pSmarty) {
    if (isset ($pParams['assign'])) {
        $pSmarty->assign ($pParams['assign'], _tag ('beginblock', $pParams));
    } else {
        return _tag ('beginblock', $pParams);
    }
}