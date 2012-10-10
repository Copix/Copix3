<?php
/**
 * @package	copix
 * @subpackage	dao
 * @author	Croës Gérald
 * @copyright	CopixTeam
 * @link	http://copix.org
 * @license	http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Classe de base pour les erreurs de vérification des données sur les DAO
 *
 * @package		copix
 * @subpackage	dao
 */
class CopixDAOCheckException extends CopixDAOException {
    /**
     * Tableau des erreurs de validation
     *
     * @var array
     */
    protected $_errors = array ();

    /**
     * Elément de données sur lequel l'erreur est survenue
     *
     * @var CopixDAORecord
     */
    protected $_record = null;

    /**
     * Constructeur
     *
     * @param array $pArrayOfErrors Tableau d'erreurs
     * @param CopixDAORecord $pRecord
     */
    public function __construct ($pArrayOfErrors = array (), $pRecord = null) {
        $this->_errors = $pArrayOfErrors;
        $this->_record = $pRecord;
        $extras = array ('errors' => $pArrayOfErrors, 'record' => $pRecord);
        parent::__construct ($this->getErrorMessage (), 0, $extras);
    }

    /**
     * Retourne les messages d'erreurs sous la forme d'une chaine de caractère, séparés par "\r\n *"
     *
     * @return string
     */
    public function getErrorMessage () {
        return implode ("\n\r *", $this->_errors);
    }

    /**
     * Retourne le tableau d'erreur utilisé lors de l'exception
     *
     * @return array
     */
    public function getErrors () {
        return $this->_errors;
    }

    /**
     * Retourne le record
     *
     * @return DAORecordAdapter
     */
    public function getRecord () {
        return $this->_record;
    }
}