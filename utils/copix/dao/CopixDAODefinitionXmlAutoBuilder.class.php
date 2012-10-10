<?php
/**
 * @package	copix
 * @subpackage	dao
 * @author	Benguigui Landry
 * @copyright	CopixTeam
 * @link	http://copix.org
 * @license	http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Class de construction d'un objet CopixDAODefinitionBuilder à partir d'un fichier XML, qui va "ajouter" des propriétés à une DAO automatique
 *
 * @package copix
 * @subpackage dao
 */
class CopixDAODefinitionXmlAutoBuilder extends CopixDAODefinitionBuilder {
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

        $pBase = null;

        if (isset ($parsedFile->datasource->connection)) {
            $connection = $parsedFile->datasource->connection->attributes ();
            if (isset ($connection['name'])) {
                $definition->setConnectionName ((string) $connection['name']);
                $pBase = (string) $connection['name'];
            }
        }

        if ($pBase == null) {
            $pBase = CopixConfig::instance ()->copixdb_getDefaultProfileName ();
        }

        $ct = CopixDB::getConnection ($pBase);
        $listTable = array ();
        $listTable = $ct->getTableList ();
        if (isset ($parsedFile->datasource) && isset ($parsedFile->datasource->table)) {
            $pTableName = (string) $parsedFile->datasource->table['name'];
            if (!in_array ($pTableName, $listTable)) {
                throw new CopixDAODefinitionException ($this->_DAOId, _i18n ('copix:dao.error.tableMissing ', $pTableName));
            }
            $definition->addTable (array ('name' => $pTableName, 'tablename' => $pTableName, 'primary' => 'yes'));
        } else {
            throw new CopixDAODefinitionException ($this->_DAOId, _i18n ('copix:dao.error.definitionfile.table.missing'));
        }

        if ($definition->getPrimaryTableName () === null) {
            throw new CopixDAODefinitionException ($this->_DAOId, _i18n ('copix:dao.error.definitionfile.table.primary.missing '));
        }

        $fields = $ct->getFieldList ($pTableName);
        $champAjoute = array ();
        //Ajout des propriétés
        if (isset ($parsedFile->properties) && isset ($parsedFile->properties->property)) {
            foreach ($parsedFile->properties->property as $field) {
                $definition->addProperty (new CopixDAOPropertyDefinition ($field->attributes (), $definition));
                $champAjoute[] = (isset ($field['fieldName'])) ? $field['fieldName'] : $field['name'];
            }
        }
        foreach ($fields as $field) {
            if (in_array ($field->name, $champAjoute)) {
                continue;
            }
            $definition->addProperty (new CopixDAOPropertyDefinition ($field, $definition));
        }

        //Ajout des méthodes
        if (isset ($parsedFile->methods) && isset ($parsedFile->methods->method)) {
            foreach ($parsedFile->methods->method as $method) {
                $definition->addMethod (new CopixDAOMethodDefinition ($method, $definition));
            }
        }

        // Assignation du fichier PHP s'il existe et est lisible
        if (isset ($this->_options['UserDAOFilePath']) && is_readable ($this->_options['UserDAOFilePath'])) {
            $definition->setUserDAOFilePath ($this->_options['UserDAOFilePath']);
        }
        if (isset ($this->_options['UserDAORecordFilePath']) && is_readable ($this->_options['UserDAORecordFilePath'])) {
            $definition->setUserDAORecordFilePath ($this->_options['UserDAORecordFilePath']);
        }

        return $definition;
    }
}