<?php
/**
 * @package copix
 * @subpackage dao
 * @author Croës Gérald
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Classe capable de créer l'objet de définition de DAO à partir d'une source de définition
 *
 * @package copix
 * @subpackage dao
 */
abstract class CopixDAODefinitionBuilder {
    /**
     * Identifiant du DAO (il sera transmis à la définition)
     *
     * @var string
     */
    protected $_DAOId = null;

    /**
     * Tableau d'options qui à été donné lors de la demande de construction
     *
     * @var array
     */
    protected $_options = array ();

    /**
     * Constructeur
     *
     * @param string $pFullyQualifiedDAO Identifiant complètement qualifié
     * @param string $pOptions Tableau d'option
     */
    public function __construct ($pFullyQualifiedDAO, $pOptions = array ()) {
        $this->_DAOId = $pFullyQualifiedDAO;
        $this->_options = $pOptions;
    }

    /**
     * Récupération de la définition du DAO
     *
     * @return CopixDAODefinition
     */
    abstract function getDefinition ();
}