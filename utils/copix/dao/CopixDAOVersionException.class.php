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
 * Classe de base lorsqu'une mise à jour est demandée alors que le record a été modifié entre temps
 *
 * @package		copix
 * @subpackage	dao
 */
class CopixDAOVersionException extends CopixDAOException {
    /**
     * Elément qui n'est pas à jour
     *
     * @var CopixDAORecord
     */
    protected $_record = null;

    /**
     * Retourne l'enregistrement dont la mise à jour a échouée
     *
     * @return CopixDAORecord
     */
    public function getRecord () {
        return $this->_record;
    }

    /**
     * Constructeur
     *
     * @param CopixDAORecord $pRecord Enregistrement
     */
    public function __construct ($pRecord) {
        $this->_record = $pRecord;
        parent::__construct ('The record is not up to date');//FIXME I18N
    }
}