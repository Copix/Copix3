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
 * Création de définition pour un DAO PHP
 * @package copix
 * @subpackage dao
 */
class CopixDAODefinitionPHPBuilder extends CopixDAODefinitionBuilder {
    /**
     * Création de l'objet définition automatiquement à partir de la base
     *
     * @return CopixDAODefinition
     * @throws CopixException
     */
    public function getDefinition () {
        $definition = new CopixDAODefinition ();
        $definition->setDAOId ($this->_DAOId);

        if (!isset ($this->_options['UserDAOFilePath']) ||
                !isset ($this->_options['UserDAORecordFilePath'])) {
            throw new CopixDAODefinitionException ($this->_DAOId, _i18n ('[CopixDAODefinitionPHPBuilder] You asked for a DAO, but Copix was not able to find its PHP files in your module. Be sure that your module is correctly installed and that both the '.$this->_DAOId.'dao.php and '.$this->_DAOId.'record.php are present in the classes directory.'));
        }

        // Assignation du fichier PHP s'il existe et est lisible
        if (! is_readable ($this->_options['UserDAOFilePath'])) {
            throw new CopixDAODefinitionException ($this->_DAOId, _i18n ('copix:dao.error.definitionfile.phpnotfound', array ($this->_options['UserDAOFilePath'], $this->_DAOId)));
        }
        if (! is_readable ($this->_options['UserDAORecordFilePath'])) {
            throw new CopixDAODefinitionException ($this->_DAOId, _i18n ('copix:dao.error.definitionfile.phpnotfound', array ($this->_options['UserDAORecordFilePath'], $this->_DAOId)));
        }

        $definition->setUserDAOFilePath ($this->_options['UserDAOFilePath']);
        $definition->setUserDAORecordFilePath ($this->_options['UserDAORecordFilePath']);
        return $definition;
    }
}