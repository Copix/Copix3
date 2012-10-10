<?php
/**
 * @package copix
 * @subpackage dao
 * @author Gérald Croës
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Exception pour les DAO
 *
 * @package copix
 * @subpackage dao
 */
class CopixDAODefinitionNoTableException extends CopixDAOException {
    /**
     * Constructeur
     *
     * @param string $pID Identifiant du DAO
     * @param string $pMessage Message d'erreur
     * @param int $pCode Code d'erreur
     */
    public function __construct ($pID, $pMessage = '', $pCode = 0, $pExtras = array ()) {
        $message = ($pID === null) ? $pMessage : '[' . $pID . '] ' . $pMessage;
        $extras = array_merge (array ('dao' => $pID), $pExtras);
        parent::__construct ($message, $pCode, $extras);
    }
}