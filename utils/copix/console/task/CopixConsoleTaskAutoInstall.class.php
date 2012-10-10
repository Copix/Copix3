<?php
/**
 *
 * @package		copix
 * @subpackage	console
 * @author		Nicolas Bastien
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 * @experimental
 */

/**
 * Installation et paramétrage d'un projet
 *
 * @package		copix
 * @subpackage	console
 */
class CopixConsoleTaskAutoInstall extends CopixConsoleAbstractTask {

	public $description = "Installation et initialisation du projet. Configuration du thème, des bases, plugins.....";
	public $requiredArguments = array('env_name' => "Nom de l'environnement de deploiement : dev, recette, prod");

	protected $_db_default_profile = null;

	protected $_dbProfilesFileName      = 'db_profiles.conf.php';
	protected $_cacheProfilesFileName   = 'cache_profiles.conf.php';
	protected $_logProfilesFileName     = 'log_profiles.conf.php';
	protected $_pluginsFileName         = 'plugins.conf.php';
	protected $_modulesListFileName     = 'modules_to_install.conf.php';

	/**
	 * (non-PHPdoc)
	 * @see action/CopixConsoleAbstractTask#validate()
	 */
	public function validate () {
		if (!is_file(COPIX_PROJECT_PATH . 'config/' . $this->getArgument('env_name') . '/' . $this->_dbProfilesFileName)
		||  !is_file(COPIX_PROJECT_PATH . 'config/' . $this->getArgument('env_name') . '/' . $this->_cacheProfilesFileName)
		||  !is_file(COPIX_PROJECT_PATH . 'config/' . $this->getArgument('env_name') . '/' . $this->_logProfilesFileName)
		||  !is_file(COPIX_PROJECT_PATH . 'config/' . $this->getArgument('env_name') . '/' . $this->_pluginsFileName)
		||  !is_file(COPIX_PROJECT_PATH . 'config/' . $this->getArgument('env_name') . '/' . $this->_modulesListFileName)
		) {
			throw new CopixException("Le repertoire config/$this->getArgument('env_name') ne contient pas l'ensemble de fichiers necessaire.");
		}
		return parent::validate();
	}

	/**
	 * (non-PHPdoc)
	 * @see action/CopixConsoleAbstractTask#execute()
	 */
	public function execute() {
		echo "[auto-install] Installation pour l'environnement : {$this->getArgument('env_name')}.\n";

		if ($this->_initDBProfiles()
			&& $this->_initCacheProfiles()
			&& $this->_initLogProfiles()
			&& $this->_initPlugins()
			&& $this->_installModules()
			&& $this->_configure()
		) {
			echo "[auto-install] Success\n";
		} else {
			echo "[auto-install] Failed\n";
		}

		return ;
	}

	/**
	 * Configuration des bases de données
	 * @return boolean
	 */
	protected function _initDBProfiles() {

		echo "Configuration BDD\n";

		//Récupéraiton de la configuration
		require(COPIX_PROJECT_PATH . 'config/' . $this->getArgument('env_name') . '/' . $this->_dbProfilesFileName);

		if (!_ioClass ('DatabaseConfigurationFile')->isWritable ()) {
			echo "[Error] {$this->_dbProfilesFileName} n'est pas accessible.";
			return false;
		}

		//Ecriture du fichier
		$isOk = _ioClass ('DatabaseConfigurationFile')->write ($_db_profiles, $_db_default_profile);

		if (!is_null($_db_default_profile)) {
			$this->_db_default_profile = substr($_db_default_profile,7);
		}

		//Vérification de l'initialisation de la base
		//Dans le cas d'une première installation on doit configurer l'admin
		$this->_checkInstallFramework($this->_db_default_profile);

		return $isOk;
	}

	/**
	 * Vérification de l'installation des tables de base de copix
	 * Si besoin installation
	 *
	 * @param string $pConnection nom de la table par défaut
	 * @return
	 */
	protected function _checkInstallFramework($pConnection = null) {
		$ct     = CopixDb::getConnection($pConnection);
		$tables = $ct->getTableList();
		if (!in_array ('copixmodule', $tables) || !in_array ('copixconfig', $tables) || !in_array ('copixlog', $tables)) {
			//Initialisation de la base
			// find the current connection type (defined in /plugins/copixDB/profils.definition.xml)
			echo "Creation des tables pour Copix\n";
			$config = CopixConfig::instance ();
			$driver = $config->copixdb_getProfile ();
			$typeDB = $driver->getDriverName ();

			// Search each module install file
			$scriptName = 'prepareinstall.'.$typeDB.'.sql';
			$file = CopixModule::getPath ('admin') . COPIX_INSTALL_DIR . 'scripts/' . $scriptName;
			CopixDB::getConnection ()->doSQLScript ($file);
			//make sure that copixmodule is reset
			CopixModule::reset();
			echo "Creation des tables pour Copix terminee\n";
		}
		return;
	}

	/**
	 * Configuration du cache
	 * @return boolean
	 */
	protected function _initCacheProfiles() {
		echo "Configuration du cache\n";

		//Récupéraiton de la configuration
		require(COPIX_PROJECT_PATH . 'config/' . $this->getArgument('env_name') . '/' . $this->_cacheProfilesFileName);

		if (!_ioClass ('cacheConfigurationFile')->isWritable ()) {
			echo "[Error] {$this->_cacheProfilesFileName} n'est pas accessible.";
			return false;
		}

		//Ecriture du fichier
		return _ioClass ('cacheConfigurationFile')->write ($_cache_types);
	}

	/**
	 * Configuration des logs
	 * @return boolean
	 */
	protected function _initLogProfiles() {
		echo "Configuration des logs\n";

		//Récupéraiton de la configuration
		require(COPIX_PROJECT_PATH . 'config/' . $this->getArgument('env_name') . '/' . $this->_logProfilesFileName);

		foreach($_log_profiles as $profile) {
			CopixLogConfigFile::add ($profile);
		}

		return true;
	}

	/**
	 * Configuration des plugins
	 * @return boolean
	 */
	protected function _initPlugins() {
		echo "Configuration des plugins\n";

		//Récupéraiton de la configuration
		require(COPIX_PROJECT_PATH . 'config/' . $this->getArgument('env_name') . '/' . $this->_pluginsFileName);

		if (!is_writable (CopixPluginConfigFile::getPath ())) {
			echo "[Error] " . CopixPluginConfigFile::getPath () . " n'est pas accessible.";
			return false;
		}
		//Ecriture du fichier
		return CopixPluginConfigFile::set ($_plugins);

		return true;
	}

	/**
	 * Installation des modules
	 * @return boolean
	 */
	protected function _installModules() {
		echo "Installation des modules\n";

		//Récupéraiton de la configuration
		require(COPIX_PROJECT_PATH . 'config/' . $this->getArgument('env_name') . '/' . $this->_modulesListFileName);

		foreach ($_arModulesToInstall as $moduleName) {
			//            var_dump(CopixConfig::instance()->arModulesPath);
			//            $installer = new CopixConsoleTaskInstallModule();
			//            $installer->addArgument("module_name", $moduleName);
			//            $installer->execute();
			//Ici je rappelle la console car la récupération des dépendences de modules ne marche pas en boucle
			echo `php copix im $moduleName`;
		}
		return true;
	}

	/**
	 * Configuration de base
	 * (module, utilisateur, mot de passe.....)
	 */
	protected function _configure() {

		if (is_null($this->_db_default_profile)) {
			return true;
		}

		$config = CopixConfig::instance ();

		$driver = $config->copixdb_getProfile ($this->_db_default_profile);
		$typeDB = $driver->getDriverName ();

		// Search each module install file
		$scriptName = 'configuration.'.$typeDB.'.sql';
		$file = COPIX_PROJECT_PATH . 'config/' . $this->getArgument('env_name') . '/' . $scriptName;
		CopixDB::getConnection ()->doSQLScript ($file);
		return true;
	}
}

