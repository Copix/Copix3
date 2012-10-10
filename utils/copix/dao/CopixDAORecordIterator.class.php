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
 * Classe qui permet de parcourir un ensemble de résultat "standards" sous la forme d'un tableau de DAORecord
 */
class CopixDAORecordIterator implements Iterator, ArrayAccess, Countable {
    /**
     * Type de record que l'on décide de parcourir
     *
     * @var string
     */
    private $_recordId = null;

    /**
     * Résultats de la requête
     *
     * @var CopixDBResultSet
     */
    private $_resultSet;

    /**
     * Offset courant
     *
     * @var int
     */
    private $_currentOffset = 0;

    /**
     * Le nom de la connexion à utiliser
     *
     * @var string
     */
    private $_connectionName = null;

    /**
     * Construction en indiquant le type de DAO en paramètre
     *
     * @param array $pArray Résultats de la requête
     * @param string $pRecordId Type de record que l'on décide de parcourir
     */
    public function __construct ($pArray, $pRecordId, $pConnectionName = null) {
        $this->_resultSet = $pArray;
        $this->_recordId = $pRecordId;
        $this->_connectionName = $pConnectionName;

        $this->_sampleRecord = _record ($pRecordId, $this->_connectionName);
    }

    /**
     * Retourne l'élément d'indice donné
     *
     * @param string $pOffset
     * @return object
     */
    public function offsetGet ($pOffset) {
        return $this->_makeRecordIfNot ($pOffset);
    }

    /**
     * Retourne l'élément courant
     *
     * @return object
     */
    public function current () {
        return $this->_makeRecordIfNot ($this->_currentOffset);
    }

    /**
     * Création d'un enregistrement a partir d'un élément
     *
     * @param	mixed	offset (integer)
     * @return ICopixDAORecord
     */
    private function _makeRecordIfNot ($pOffset) {
        if (isset ($this->_resultSet[$pOffset])) {
            if ($this->_resultSet[$pOffset] instanceof ICopixDAORecord) {
                return $this->_resultSet[$pOffset];
            }else {
                $record = clone ($this->_sampleRecord);
                return $this->_resultSet[$pOffset] = $record->initFromDBObject ($this->_resultSet[$pOffset]);
            }
        }else {
            return null;
        }
    }

    /**
     * Passe à l'enregistrement suivant
     */
    public function next () {
        $this->_currentOffset++;
    }

    /**
     * Retourne la clef courante
     *
     * @return int
     */
    public function key () {
        return $this->_currentOffset;
    }

    /**
     * Indique si l'élément courant est valide
     *
     * @return boolean
     */
    public function valid () {
        return isset ($this->_resultSet[$this->_currentOffset]);
    }

    /**
     * Réinitialisation du parcours des éléments au premier indice
     */
    public function rewind () {
        $this->_currentOffset = 0;
    }

    /**
     * Blocage de la possibilité de définir un enregistrement. Déclenche une exception.
     *
     * @param mixed $pKey Clef à modifier, type string ou int
     * @param mixed $pValue Nouvelle valeur pour la clef $pKey
     * @throws CopixDAOResultSetException
     */
    public function offsetSet ($pKey, $pValue) {
        throw new CopixDAOResultSetException (_i18n ('copix:dao.error.offsetSet'));
    }

    /**
     * Blocage de la possibilité de supprimer un enregistrement. Déclenche une exception.
     *
     * @param mixed $pKey Clef à supprimer, type string ou int
     * @throws CopixDAOResultSetException
     */
    public function offsetUnset ($pKey) {
        throw new CopixDAOResultSetException (_i18n ('copix:dao.error.offsetUnset'));
    }

    /**
     * Indique si $pOffset existe
     *
     * @param mixed $pOffset Clef dont on veut vérifier l'existance
     * @return boolean
     */
    public function offsetExists ($pOffset) {
        return isset ($this->_resultSet[$pOffset]);
    }

    /**
     * Retourne le nombre d'éléments qui existent dans le résulat
     *
     * @return int
     */
    public function count () {
        return count ($this->_resultSet);
    }

    /**
     * Récupère l'ensemble des enregistrements dans un tableau
     *
     * @return array
     */
    public function fetchAll () {
        $results = array ();
        foreach ($this->_resultSet as $key => $element) {
            $results[$key] = clone ($this->_sampleRecord);
            $results[$key]->initFromDBObject ($element);
        }
        return $results;
    }
}