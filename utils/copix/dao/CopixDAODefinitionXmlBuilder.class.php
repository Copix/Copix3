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
 * Class de construction d'un objet CopixDAODefinitionBuilder à partir d'un fichier XML
 *
 * @package copix
 * @subpackage dao
 */
class CopixDAODefinitionXmlBuilder extends CopixDAODefinitionBuilder {
    /**
     * Création de l'objet définition à partir du XML
     *
     * @return CopixDAODefinition
     * @throws Exception
     */
    public function getDefinition () {
        $definition = new CopixDAODefinition ();
        $definition->setDAOId ($this->_DAOId);

        if (isset ($this->_options['xmlFilePath'])) {
            if (!($parsedFile = @simplexml_load_file ($this->_options['xmlFilePath']))) {
                throw new CopixDAODefinitionException ($this->_DAOId, _i18n ('copix:dao.error.badXMLFile', $this->_options['xmlFilePath']));
            }
        } else {
            throw new CopixDAODefinitionException ($this->_DAOId, _i18n ('copix:dao.error.xmlFileNotFound', array ($this->_options['xmlFilePath'], $this->_DAOId)));
        }

        if (isset ($parsedFile->datasource) && isset ($parsedFile->datasource->tables) && isset ($parsedFile->datasource->tables->table)) {
            foreach ($parsedFile->datasource->tables->table as $table) {
                $definition->addTable ($table->attributes ());
            }
        } else {
            throw new CopixDAODefinitionException ($this->_DAOId, _i18n ('copix:dao.error.definitionfile.table.missing'));
        }

        if ($definition->getPrimaryTableName () === null) {
            throw new CopixDAODefinitionException ($this->_DAOId, _i18n ('copix:dao.error.definitionfile.table.primary.missing'));
        }

        if (isset ($parsedFile->datasource->connection)) {
            $connection = $parsedFile->datasource->connection->attributes ();
            if (isset ($connection['name'])) {
                $definition->setConnectionName ((string) $connection['name']);
            }
        }

        //Ajout des propriétés
        if (isset ($parsedFile->properties) && isset ($parsedFile->properties->property)) {
            foreach ($parsedFile->properties->property as $field) {
                $definition->addProperty (new CopixDAOPropertyDefinition ($field->attributes(), $definition));
            }
        } else {
            throw new CopixDAODefinitionException ($this->_DAOId, _i18n ('copix:dao.error.definitionfile.properties.missing'));
        }

        //Ajout des méthodes
        if (isset ($parsedFile->methods) && isset ($parsedFile->methods->method)) {
            foreach ($parsedFile->methods->method as $method) {
                $definition->addMethod (new CopixDAOMethodDefinition ($method, $definition));
            }
        }

        //Assignation du fichier PHP s'il existe et est lisible
        if (isset ($this->_options['UserDAOFilePath']) && is_readable ($this->_options['UserDAOFilePath'])) {
            $definition->setUserDAOFilePath ($this->_options['UserDAOFilePath']);
        }
        if (isset ($this->_options['UserDAORecordFilePath']) && is_readable ($this->_options['UserDAORecordFilePath'])) {
            $definition->setUserDAORecordFilePath ($this->_options['UserDAORecordFilePath']);
        }
        return $definition;
    }
}