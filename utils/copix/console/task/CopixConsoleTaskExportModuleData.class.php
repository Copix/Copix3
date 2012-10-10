<?php
/**
 * @package		copix
 * @subpackage	console
 * @author		Nicolas Bastien
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 * @experimental
 */

/**
 * Exporte les données d'un module (fichier et données en base)
 *
 * Cette tâche se base sur le module.xml pour retrouver les données à sauvegarder
 *
 * @package		copix
 * @subpackage	console
 */
class CopixConsoleTaskExportModuleData extends CopixConsoleAbstractTask {

	public $description = "Export des données d'un module dont le nom est fourni en parametre";
	public $requiredArguments = array('module_name' => 'Nom du module dont on veut exporter les donnees');

	protected $_arTables = array();
	protected $_arDirectories = array();

	protected $_nodeType = 'table';

	/**
	 * Chemin du répertoire d'export courant
	 * @var string
	 */
	private $_currentExportDirectory = null;

	/**
	 * Tableau faisant le lien entre le driver et la commande de dump à utiliser
	 * TODO ajouter d'autres commandes de dump que mysql
	 * @var array
	 */
	protected $_arSQLCommand = array (
		'mysql' => 'mysqldump --user=%%USER%% --password=%%PASSWORD%% --result-file="%%FILE%%" %%DATABASE%% %%TABLES%% 2>&1',
		
	);

	/**
	 * (non-PHPdoc)
	 * @see action/CopixConsoleAbstractTask#execute()
	 */
	public function execute() {
		echo "[export-module-data] Export du module : {$this->getArgument('module_name')}.\n";

		//Vérification que le module existe et récupération des dépendances
		$arDependencies = @CopixModule::getInformations ($this->getArgument('module_name'))->getDependencies ();
		$callbackFunc = create_function('$obj', 'if ($obj->getKind() === "module") {return $obj->getName();}return null;');
		$arDependencies = array_map($callbackFunc, $arDependencies);
		$arModules = array_merge($arDependencies, array($this->getArgument('module_name')));

		//Création du répertoire d'export
		$this->_currentExportDirectory = COPIX_PROJECT_DATA_PATH . 'export'. DIRECTORY_SEPARATOR . strtolower($this->getArgument('module_name') . '_' . date('Ymd_Gi')) . DIRECTORY_SEPARATOR;
		CopixFile::createDir($this->_currentExportDirectory);

		echo "\tListes des modules a exporter : \n  " . implode(' - ', $arModules) . "\n";

		//Récupération des données à sauvegarder
		$this->_loadTables($arModules);
		$this->_loadDirectories($arModules);

		//Dump des données
		$this->_dumpDBData();

		//Copie des fichiers
		$this->_copyFileData();

		//Création de l'archive
		$this->_packageAll();

		echo "[export-module-data] Termine\n";
		return ;
	}

	/**
	 * Chargement de la liste des tables à exporter
	 * @param array $arModules
	 */
	protected function _loadTables($arModules) {
		foreach ($arModules as $moduleName) {
			$this->_arTables = array_merge($this->_arTables, $this->_getModuleTables($moduleName));
		}
		$this->_arTables = array_unique($this->_arTables);
		sort($this->_arTables);
	}

	/**
	 * Chargement de la liste des répertoires à exporter
	 * @param array $arModules
	 */
	protected function _loadDirectories($arModules) {
		foreach ($arModules as $moduleName) {
			$this->_arDirectories = array_merge($this->_arDirectories, $this->_getModuleDirectories($moduleName));
		}
		$this->_arDirectories = array_unique($this->_arDirectories);
	}

	/**
	 * Listes les différentes tables du module
	 * @return array
	 */
	protected function _getModuleTables($pModuleName) {
		$xml = simplexml_load_file(CopixModule::getPath($pModuleName).'module.xml');

		// Extrait les infos
		$moduleNodes = $xml->xpath("/moduledefinition/registry/entry[@id='ModuleTables']/*");

		if ($moduleNodes === false) {
			return array();
		}

		$toReturn = array();
		foreach ($moduleNodes as $node) {
			$toReturn[] = _toString($node['name']);
		}
		return $toReturn;
	}

	/**
	 * Listes les différents répertoires de stockage du modules
	 * @return array
	 */
	protected function _getModuleDirectories($pModuleName) {
		$xml = simplexml_load_file(CopixModule::getPath($pModuleName).'module.xml');

		// Extrait les infos
		$moduleNodes = $xml->xpath("/moduledefinition/registry/entry[@id='ModuleDirectories']/*");

		if ($moduleNodes === false) {
			return array();
		}

		$toReturn = array();
		foreach ($moduleNodes as $node) {
			$toReturn[] = _toString($node['name']);
		}
		return $toReturn;
	}

	/**
	 * Export des tables
	 * @return void
	 */
	protected function _dumpDBData() {

		echo "\tArchivage des tables :\n";
		echo "\t\t" . implode("\n\t\t", $this->_arTables) . "\n";

		//Récupération du driver
		$driver = CopixConfig::instance()->copixdb_getProfile ();
		$arConnectionString = $driver->getConnectionStringParts();
		$database = $arConnectionString['dbname'];

		if (isset($this->_arSQLCommand[$driver->getDatabase()])){
			$sqlCmd = $this->_arSQLCommand[$driver->getDatabase()];
			
			$sqlCmd = str_replace('%%USER%%', $driver->getUser(), $sqlCmd);
			$sqlCmd = str_replace('%%PASSWORD%%', $driver->getPassword(), $sqlCmd);
			$sqlCmd = str_replace('%%FILE%%', $this->_currentExportDirectory.'data.sql', $sqlCmd);
			$sqlCmd = str_replace('%%DATABASE%%', $database, $sqlCmd); // $driver->getName() renvoie le nom du profil, pas celui de la base de données
			$sqlCmd = str_replace('%%TABLES%%', implode(' ', $this->_arTables), $sqlCmd);
	
			$output = null;
			$return = null;
			exec( $sqlCmd, $output, $return );
			if( $return == 0 ){
				echo "\tArchivage des tables termine.\n";
			} else {
				echo "\n";
				echo "\tErreur $return lors de l'archivage des tables.\n";
				echo "\n      ";
				echo implode("\n\t", $output);
				echo "\n";
				echo "\n";
			}
		} else {
			echo "\n";
			echo "Erreur: le driver pour les bases de donnees {$driver->getDatabase()} n'a pas ete cree.\n";
			echo "\tVous devrez exporter les donnees en utilisant un outil adapte.\n";
			echo "\tExporter les tables ".implode(', ', $this->_arTables)." de la base $database .\n\n";
		}
	}

	/**
	 * Export des fichiers
	 * @return void
	 */
	protected function _copyFileData() {
		echo "\tArchivage des fichiers\n";

		// TODO permettre de choisir la compression .tar.gz
		$zipper = new CopixZip($this->_currentExportDirectory . 'files.zip');

		foreach ($this->_arDirectories as $directoryName) {
			if (!is_dir(COPIX_VAR_PATH . $directoryName . DIRECTORY_SEPARATOR)) {continue;}
			echo "\t\t$directoryName ... ";
			$zipper->addDirectory(COPIX_VAR_PATH . $directoryName . DIRECTORY_SEPARATOR);
			//$zipper->zipDirectory($this->_currentExportDirectory . $directoryName .'.zip', COPIX_VAR_PATH . $directoryName . DIRECTORY_SEPARATOR);
			echo "termine.\n";
		}
		
		echo "\tArchivage des fichiers termine.\n";
		$zipper->printInfosForCmdLine();
		return;
	}

	protected function _packageAll() {
		echo "\tCreation du package ... \n";
		$zip = new CopixZip();
		$zip->zipDirectory(substr($this->_currentExportDirectory, 0, -1) . '.zip', $this->_currentExportDirectory);
		
		//Suppression des fichiers
		echo "\tSuppression des fichiers temporaires\n";
		CopixFile::removeDir($this->_currentExportDirectory);
		echo "\tCreation du package termine.\n";
	}

}

