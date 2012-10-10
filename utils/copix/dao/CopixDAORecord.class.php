<?php
/**
 * @package copix
 * @subpackage dao
 * @author Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Classe de constantes pour les erreurs liées aux record dans la méthode check d'un DAO
 *
 * @package copix
 * @subpackage dao
 */
class CopixDAORecord {
    /**
     * Champs requis
     */
    const ERROR_REQUIRED = 1;

    /**
     * Format de la valeur invalide
     */
    const ERROR_FORMAT = 2;

    /**
     * Longueur de la valeur trop grande
     */
    const ERROR_SIZE_LIMIT = 3;

    /**
     * Valeur du champ non numérique
     */
    const ERROR_NUMERIC = 4;

    /**
     * Date invalide
     */
    const ERROR_DATE = 5;

    /**
     * Heure invalide
     */
    const ERROR_TIME = 6;

    /**
     * Format de la date invalide
     */
    const ERROR_DATE_FORMAT = 7;
}