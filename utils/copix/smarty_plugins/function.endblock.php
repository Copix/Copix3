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
 * Fin d'un block
 */
function smarty_function_endblock ($pParams, &$pSmarty) {
    if (isset ($pParams['assign'])) {
        $pSmarty->assign ($pParams['assign'], _tag ('endblock', $pParams));
    } else {
        return _tag ('endblock', $pParams);
    }
}