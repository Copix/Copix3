<?php
/**
 * @package	copix
 * @subpackage	dao
 * @author	Salleyrond Julien
 * @copyright	CopixTeam
 * @link	http://copix.org
 * @license	http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Class de construction d'un objet CopixDAODefinitionBuilder à partir d'une base de données
 *
 * @package copix
 * @subpackage dao
 */
class CopixDAODefinitionDBBuilder extends CopixDAODefinitionBuilder {
    public function __construct ($pFullyQualifiedDAO, $pOptions = array ()) {
        parent::__construct ($pFullyQualifiedDAO, $pOptions);
        $pBase = $this->_options['connection'];
        $pTableName = $this->_options['tableName'];

        if ($pBase == null) {
            $pBase = CopixConfig::instance ()->copixdb_getDefaultProfileName ();
        }

        $listTable = CopixDB::getConnection ($pBase)->getTableList ();
        array_walk ($listTable, array ($this, '_toLower'));
        if (!in_array (strtolower ($pTableName), $listTable)) {
            throw new CopixDAODefinitionNoTableException ($this->_DAOId, _i18n ('copix:dao.error.tableMissing', array ($pTableName, implode (', ', $listTable))));
        }
    }

    public function _toLower (& $item, $key) {
        $item = strtolower ($item);
    }

    /**
     * Création de l'objet définition automatiquement à partir de la base
     *
     * @return CopixDAODefinition
     * @throws CopixException
     */
    public function getDefinition () {
        $definition = new CopixDAODefinition ();
        $definition->setDAOId ($this->_DAOId);

        $pBase = $this->_options['connection'];
        $pTableName = $this->_options['tableName'];

        if ($pBase == null) {
            $pBase = CopixConfig::instance ()->copixdb_getDefaultProfileName ();
        }

        $ct = CopixDB::getConnection ($pBase);
        $definition->setConnectionName ($pBase);

        $listTable = array ();
        $listTable = $ct->getTableList ();
        array_walk ($listTable, array ($this, '_toLower'));

        if (!in_array (strtolower ($pTableName), $listTable)) {
            throw new CopixDAODefinitionNoTableException ($this->_DAOId, _i18n ('copix:dao.error.tableMissing', array ($pTableName, implode (', ', $listTable))));
        }

        $fields = array ();
        $fields = $ct->getFieldList ($pTableName);
        if (count ($fields) < 1) {
            throw new CopixDAODefinitionException ($this->_DAOId, _i18n ('copix:dao.error.definitionfile.properties.missing', $pTableName));
        }

        $definition->addTable (array ('name' => $pTableName, 'tablename' => $pTableName, 'primary' => 'yes'));
        foreach ($fields as $field) {
            $definition->addProperty (new CopixDAOPropertyDefinition ($field, $definition));
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