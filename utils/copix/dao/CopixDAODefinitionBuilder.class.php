<?php
/**
 * @package		copix
 * @subpackage	dao
 * @author		Croës Gérald
 * @copyright	2001-2006 CopixTeam
 * @link			http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Classe capable de créer l'objet de définition de DAO à partir d'une source
 * de définition
 * @package copix
 * @subpackage dao
 */
abstract class CopixDAODefinitionBuilder {
	/**
	 * L'identifiant du DAO (il sera transmis à la définition)
	 * @var string
	 */
	protected $_DAOId = null;

	/**
	 * Le tableau d'options qui à été donné lors de la demande de construction
	 * @var array
	 */
	protected $_options = array ();

	/**
	 * Constructeur
	 * @param string $pFullyQualifiedDAO l'identifiant complètement qualifié  
	 * @param syring $pOptions Un tableau d'option
	 */
	public function __construct ($pFullyQualifiedDAO, $pOptions = array ()){
		$this->_DAOId   = $pFullyQualifiedDAO;
		$this->_options = $pOptions;
	}

	/**
	 * Récupération de la définition du DA
	 */
	abstract function getDefinition ();
}

/**
 * Capable de construire un objet Définition à partir d'une base de données
 * @author Salleyron Julien
 * @package copix
 * @subpackage dao
 */
class CopixDAODefinitionDBBuilder extends CopixDAODefinitionBuilder {
	/**
	 * Création de l'objet définition automatiquement à partir de la base
	 */
	function getDefinition () {
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

		if (!in_array ($pTableName, $listTable)) {
			throw new CopixException (_i18n ('copix:dao.error.tableMissing', array ($pTableName, implode (', ', $listTable))));
		}

		$fields = array ();
		$fields = $ct->getFieldList ($pTableName);
		if (count ($fields) <1) {
			throw new CopixException (_i18n ('copix:dao.error.definitionfile.properties.missing', $pTableName));
		}

		$definition->addTable (array('name'=>$pTableName, 'tablename'=>$pTableName, 'primary'=>'yes'));
		foreach ($fields as $field) {
			$definition->addProperty (new CopixPropertyForDAO ($field, $definition));
		}

		//Assignation du fichier PHP s'il existe et est lisible
		if (isset ($this->_options['phpClassFilePath']) && is_readable ($this->_options['phpClassFilePath'])){
			$definition->setPHPClassFilePath ($this->_options['phpClassFilePath']);
		}
		return $definition;
	}
}


class CopixDAODefinitionXmlAutoBuilder extends CopixDAODefinitionBuilder {
	/**
	 * Création de l'objet définition à partir du XM
	 */
	public function getDefinition (){
		$definition = new CopixDAODefinition ();
		$definition->setDAOId ($this->_DAOId);

		if (isset ($this->_options['xmlFilePath'])){
			if (! ($parsedFile = @simplexml_load_file ($this->_options['xmlFilePath']))){
				throw new Exception ('Impossible d\'analyser le fichier XML pour le DAO '.$this->_options['xmlFilePath']);
			}
		}else{
			throw new Exception ('Impossible de trouver le fichier XML '.$this->_options['xmlFilePath'].' pour le DAO '.$this->_DAOId);
		}
		
		$pBase = null;
		
		if (isset ($parsedFile->datasource->connection)){
			$connection = $parsedFile->datasource->connection->attributes ();
			if (isset ($connection['name'])){
				$definition->setConnectionName ((string) $connection['name']);
				$pBase = (string)$connection['name'];
			}
		}
		
		if ($pBase == null) {
			$pBase = CopixConfig::instance ()->copixdb_getDefaultProfileName ();
		}

		$ct = CopixDB::getConnection ($pBase);
		$listTable = array ();
		$listTable = $ct->getTableList ();

		if (isset ($parsedFile->datasource) && isset ($parsedFile->datasource->table) ){
			$pTableName = (string)$parsedFile->datasource->table['name'];
			if (!in_array ($pTableName, $listTable)) {
				throw new Exception (_i18n ('copix:dao.error.tableMissing ', $pTableName));
			}
			$definition->addTable (array('name'=>$pTableName, 'tablename'=>$pTableName, 'primary'=>'yes'));
		}else{
			throw new Exception (_i18n ('copix: dao.error.definitionfile.table.missing'));
		}

		if ($definition->getPrimaryTableName () === null){
			throw new Exception (_i18n ('copix:dao.error.definitionfile.table.primary.missing '));
		}

		$fields = $ct->getFieldList ($pTableName);
		$champAjoute = array ();
		//Ajout des propriétés
		if (isset($parsedFile->properties) && isset($parsedFile->properties->property)){
			foreach ($parsedFile->properties->property as $field){
				$definition->addProperty (new CopixPropertyForDAO ($field->attributes(), $definition));
				$champAjoute[] = isset($field['fieldName'])?$field['fieldName']:$field['name'];
			}
			foreach ($fields as $field){
				if(in_array($field->name, $champAjoute)){
					continue;
				}
				$definition->addProperty (new CopixPropertyForDAO ($field, $definition));
			}
		}else{
			throw new Exception (_i18n ('copix:dao.error.definitionfile.properties.missing'));
		}
		//Assignation du fichier PHP s'il existe et est lisible
		if (isset ($this->_options['phpClassFilePath']) && is_readable ($this->_options['phpClassFilePath'])){
			$definition->setPHPClassFilePath ($this->_options['phpClassFilePath']);
		}
		return $definition;
	}
}

/**
 * Capable de construire un objet Définition à partir d'un fichier XML
 * @package copix
 * @subpackage dao
 */
class CopixDAODefinitionXmlBuilder extends CopixDAODefinitionBuilder {
	/**
	 * Création de l'objet définition à partir du XM
	 */
	public function getDefinition (){
		$definition = new CopixDAODefinition ();
		$definition->setDAOId ($this->_DAOId);

		if (isset ($this->_options['xmlFilePath'])){
			if (! ($parsedFile = @simplexml_load_file ($this->_options['xmlFilePath']))){
				throw new Exception ('Impossible d\'analyser le fichier XML pour le DAO '.$this->_options['xmlFilePath']);
			}
		}else{
			throw new Exception ('Impossible de trouver le fichier XML '.$this->_options['xmlFilePath'].' pour le DAO '.$this->_DAOId);
		}

		if (isset ($parsedFile->datasource) && isset ($parsedFile->datasource->tables) && isset ($parsedFile->datasource->tables->table)){
			foreach ($parsedFile->datasource->tables->table as $table){
				$definition->addTable ($table->attributes ());
			}
		}else{
			throw new Exception (_i18n ('copix:dao.error.definitionfile.table.missing'));
		}

		if ($definition->getPrimaryTableName () === null){
			throw new Exception (_i18n ('copix:dao.error.definitionfile.table.primary.missing'));
		}

		if (isset ($parsedFile->datasource->connection)){
			$connection = $parsedFile->datasource->connection->attributes ();
			if (isset ($connection['name'])){
				$definition->setConnectionName ((string) $connection['name']);
			}
		}

		//Ajout des propriétés
		if (isset($parsedFile->properties) && isset($parsedFile->properties->property)){
			foreach ($parsedFile->properties->property as $field){
				$definition->addProperty (new CopixPropertyForDAO ($field->attributes(), $definition));
			}
		}else{
			throw new Exception (_i18n ('copix:dao.error.definitionfile.properties.missing'));
		}

		//Ajout des méthodes
		if (isset ($parsedFile->methods) && isset ($parsedFile->methods->method)){
			foreach ($parsedFile->methods->method as $method){
				$definition->addMethod (new CopixMethodForDAO ($method, $definition));
			}
		}

		//Assignation du fichier PHP s'il existe et est lisible
		if (isset ($this->_options['phpClassFilePath']) && is_readable ($this->_options['phpClassFilePath'])){
			$definition->setPHPClassFilePath ($this->_options['phpClassFilePath']);
		}
		return $definition;
	}
}
?>