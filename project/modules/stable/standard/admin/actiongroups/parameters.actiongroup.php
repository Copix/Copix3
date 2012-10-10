<?php
/**
 * @package standard
 * @subpackage admin
 * @author Bertrand Yan, Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Administration des paramètres des modules
 *
 * @package standard
 * @subpackage admin
 */
class ActionGroupParameters extends CopixActionGroup {
	/**
	 * Instance de CopixConfig, pour éviter de trop appeler le singleton
	 *
	 * @var CopixConfig
	 */
	private $_config = null;

	/**
	 * Executé avant toute action
	 *
	 * @param string $pActionName Nom de l'action
	 */
	protected function _beforeAction ($pActionName) {
		_currentUser ()->assertCredential ('basic:admin');
	}

	/**
	 * Choix du module
	 *
	 * @return CopixActionReturn
	 */
	public function processDefault () {
		$ppo = ToolsAdmin::setPage (ToolsAdmin::PAGE_PARAMETERS_LIST);
		$ppo->highlight = _request ('highlight');
		
		$ppo->modules = array ();
		foreach (CopixModule::getList () as $moduleName) {
			if (count (CopixConfig::getParams ($moduleName)) > 0) {
				$informations = CopixModule::getInformations ($moduleName);
				$groupId = $informations->getGroup ()->getId ();
				$ppo->modules[$groupId]['caption'] = $informations->getGroup ()->getCaption ();
				$ppo->modules[$groupId]['modules'][] = $informations;
			}
		}
		ksort ($ppo->modules);

		return _arPPO ($ppo, 'parameters/list.php');
	}

	/**
	 * Paramètres du module demandé
	 *
	 * @return CopixActionReturn
	 */
	public function processEdit () {
		$module = _request ('choiceModule');
		if (!CopixModule::isEnabled ($module)) {
			return _arRedirect (_url ('admin|parameters|'));
		}

		$ppo = ToolsAdmin::setPage (ToolsAdmin::PAGE_PARAMETERS_EDIT, array ('module' => $module));
		$ppo->choiceModule = $module;
		$ppo->params = CopixConfig::getParams ($module);
		if (CopixRequest::exists ('errorsId')) {
			$errorsId = _request ('errorsId');
			$ppo->errorsId = $errorsId;
			$ppo->errors = CopixSession::get ('parameters|errors|' . $errorsId, 'admin|parameters');
			$newValues = CopixSession::get ('parameters|params|' . $errorsId, 'admin|parameters');
			foreach ($newValues as $name => $value) {
				if (array_key_exists ($name, $ppo->params)) {
					$ppo->params[$name]['Value'] = $value;
				}
			}
		}
		return _arPPO ($ppo, 'parameters/edit.php');
	}

	/**
	 * Sauvegarde la configuration
	 *
	 * @return CopixActionReturn
	 */
	public function processSave () {
		$module = _request ('choiceModule');
		$ppo = ToolsAdmin::setPage (ToolsAdmin::PAGE_PARAMETERS_DO_EDIT, array ('module' => $module));
		if (!CopixModule::isEnabled ($module)) {
			throw new ModuleAdminException (_i18n ('params.error.moduleNotEnabled', $module), ModuleAdminException::MODULE_NOT_ENABLED);
		}

		$errors = array ();
		$params = array ();
		foreach (CopixRequest::asArray () as $name => $value) {
			if (substr ($name, 0, 6) == 'param_') {
				$param = substr ($name, 6);
				$params[$param] = $value;
				$result = _validator ('CopixConfigModuleValidator', array ('name' => $param))->check ($value);
				if ($result instanceof CopixErrorObject) {
					$errors = array_merge ($errors, $result->asArray ());
				}
			}
		}

		// erreurs dans les valeurs saisies
		if (count ($errors) > 0) {
			$errorsId = (_request ('errorsId') != null) ? _request ('errorsId') : uniqid ();
			CopixSession::set ('parameters|errors|' . $errorsId, $errors, 'admin|parameters');
			CopixSession::set ('parameters|params|' . $errorsId, $params, 'admin|parameters');
			return _arRedirect (_url ('admin|parameters|edit', array ('choiceModule' => $module, 'errorsId' => $errorsId)));
		}

		// pas d'erreur, définition des nouveaux paramètres
		foreach ($params as $name => $value) {
			CopixConfig::set ($name, $value);
		}

		$params = array (
			'title' => _i18n ('params.title.confirmSaved'),
			'redirect_url' => _url ('admin|parameters|', array ('highlight' => $module)),
			'message' => _i18n ('params.confirmSaved', $module),
			'links' => array (
				_url ('admin|parameters|', array ('highlight' => $module)) => _i18n ('params.action.backToList'),
				_url ('admin|parameters|edit', array ('choiceModule' => $module))=> _i18n ('params.action.backToEdit')
			)
		);
		return CopixActionGroup::process ('generictools|messages::getInformation', $params);
	}

	/**
	 * Affichage de la config de Copix
	 *
	 * @return CopixActionReturn
	 */
	public function processCopix () {
		$ppo = ToolsAdmin::setPage (ToolsAdmin::PAGE_PARAMETERS_COPIX);
		$sections = array ();
		$this->_config = CopixConfig::instance ();

		// version
		$sections[_i18n ('copix.section.version')] = $this->_getConstants (array (
			'COPIX_VERSION', 'COPIX_VERSION_MAJOR', 'COPIX_VERSION_MINOR', 'COPIX_VERSION_FIX',
			'COPIX_VERSION_RC', 'COPIX_VERSION_BETA', 'COPIX_VERSION_DEV'
		));

		// chemins
		$section = _i18n ('copix.section.paths');
		
		$sections[$section] = $this->_getConstants (array (
			'COPIX_CORE_PATH', 'COPIX_PATH', 'COPIX_UTILS_PATH', 'COPIX_SMARTY_PATH', 'COPIX_PROJECT_PATH',
			'COPIX_TEMP_PATH', 'COPIX_CACHE_PATH', 'COPIX_VAR_PATH', 'COPIX_LOG_PATH',
			'COPIX_CONFIG_FILE', 'COPIX_PROJECTCONTROLLER_FILE', 'COPIX_TESTCONTROLLER_FILE', 'COPIX_INC_FILE',
			'COPIX_CLASSPATHS_FILE'
		), true, true);
		$sections[$section] = array_merge ($sections[$section], array ('arModulesPath' => $this->_config->arModulesPath));
		if (method_exists ($this->_config, 'copixtheme_addPath')) {
			$sections[$section] = array_merge ($sections[$section], array (_i18n ('copix.paths.themes') => $this->_config->copixtheme_getPaths ()));
		}
		if (method_exists ($this->_config, 'copixtpl_addPath')) {
			$sections[$section] = array_merge ($sections[$section], array (_i18n ('copix.paths.templates') => $this->_config->copixtpl_getPaths ()));
		}
		/* TODO: arPluginsPath désactivé jusqu'à ce qu'on l'implémente vraiment, cf #151.
		$sections[$section]['arPluginsPath'] = $this->_config->arPluginsPath;
		*/

		// répertoires
		$sections[_i18n ('copix.section.dirs')] = $this->_getConstants (array (
			'COPIX_ACTIONGROUP_DIR', 'COPIX_CLASSES_DIR', 'COPIX_DESC_DIR', 'COPIX_INSTALL_DIR', 'COPIX_PLUGINS_DIR',
			'COPIX_RESOURCES_DIR', 'COPIX_TEMPLATES_DIR', 'COPIX_WWW_DIR', 'COPIX_ZONES_DIR'
		));

		// configuration générale
		$section = _i18n ('copix.section.config');
		switch ($this->_config->getMode ()) {
			case CopixConfig::DEVEL : $sections[$section]['mode'] = 'DEVEL'; break;
			case CopixConfig::PRODUCTION : $sections[$section]['mode'] = 'PRODUCTION'; break;
			case CopixConfig::FORCE_INITIALISATION : $sections[$section]['mode'] = 'FORCE_INITIALISATION'; break;
			default : $sections[$section]['mode'] = 'UNKNOW'; break;
		}
		$configs = array ('trustedModules', 'sessionName', 'copixsession_key', 'default_charset', 'mainTemplate', 'notFoundDefaultRedirectTo');
		$sections[$section] = array_merge ($sections[$section], $this->_getConfigs ($configs));
		$configs = array (
			'checkTrustedModules', 'apcEnabled', 'copixresource_gzipCompress', 'compile_check', 'force_compile', 'template_caching',
			'realPathDisabled', 'template_use_sub_dirs', 'invalidActionTriggersError', 'overrideUnserializeCallbackEnabled'
		);
		$sections[$section] = array_merge ($sections[$section], $this->_getConfigs ($configs, true));
		ksort ($sections[$section]);

		// configuration i18n
		$section = _i18n ('copix.section.i18n');
		$sections[$section] = $this->_getConfigs (array ('default_language', 'default_country', 'default_timezone'));
		$configs = array ('i18n_path_enabled', 'i18n_missingKeyLaunchException');
		$sections[$section] = array_merge ($sections[$section], $this->_getConfigs ($configs, true));

		// configuration des url
		$section = _i18n ('copix.section.configUrl');
		$sections[$section]['significant_url_mode'] = $this->_config->significant_url_mode;
		$sections[$section]['significant_url_prependIIS_path_key'] = $this->_config->significant_url_prependIIS_path_key;
		$sections[$section]['stripslashes_prependIIS_path_key'] = CopixFormatter::getBool ($this->_config->stripslashes_prependIIS_path_key);
		$sections[$section]['url_requestedscript_variable'] = $this->_config->url_requestedscript_variable;

		// configuration des bases de données
		$section = _i18n ('copix.section.configDb');
		$sections[$section][_i18n ('copix.configDb.givenDrivers')] = CopixDB::getAllDrivers ();
		$sections[$section][_i18n ('copix.configDb.availableDrivers')] = CopixDB::getAvailableDrivers ();
		$sections[$section][_i18n ('copix.configDb.profils')] = $this->_config->copixdb_getProfiles ();
		$sections[$section][_i18n ('copix.configDb.defaultProfil')] = $this->_config->copixdb_getDefaultProfileName ();

		// profil de connexion utilisé actuellement
		$profile = CopixDb::getConnection ()->getProfile ();
		$parts = $profile->getConnectionStringParts ();
		$section = _i18n ('copix.section.dbProfile', array ($profile->getName ()));
		$sections[$section][_i18n ('copix.dbProfile.connexionString')] = $profile->getConnectionString ();
		$sections[$section][_i18n ('copix.dbProfile.driverName')] = $profile->getDriverName ();
		$sections[$section][_i18n ('copix.dbProfile.databaseType')] = $profile->getDatabase ();
		$sections[$section][_i18n ('copix.dbProfile.user')] = $profile->getUser ();
		$sections[$section][_i18n ('copix.dbProfile.database')] = (isset ($parts['dbname'])) ? $parts['dbname'] : null;
		$sections[$section][_i18n ('copix.dbProfile.serverName')] = (isset ($parts['host'])) ? $parts['host'] : 'localhost';
		$sections[$section][_i18n ('copix.dbProfile.options')] = $profile->getOptions ();

		$section = _i18n ('copix.section.auth');
		$sections[$section]['copixauth_cache'] = CopixFormatter::getBool ($this->_config->copixauth_cache);

		$userHandlers = $this->_config->copixauth_getRegisteredUserHandlers ();
		//echo '<pre><div align="left">';
		foreach ($userHandlers as $key => $item) {
			$userHandlers[$key]['required'] = CopixFormatter::getBool ($userHandlers[$key]['required']);
		}
		$sections[$section]['userHandlers'] = $userHandlers;

		$groupHandlers = $this->_config->copixauth_getRegisteredGroupHandlers ();
		foreach ($groupHandlers as $key => $item) {
			$groupHandlers[$key]['required'] = CopixFormatter::getBool ($groupHandlers[$key]['required']);
		}
		$sections[$section]['groupHandlers'] = $groupHandlers;

		$credentialHandlers = $this->_config->copixauth_getRegisteredCredentialHandlers ();
		foreach ($credentialHandlers as $key => $item) {
			$credentialHandlers[$key]['stopOnSuccess'] = CopixFormatter::getBool ($credentialHandlers[$key]['stopOnSuccess']);
			$credentialHandlers[$key]['stopOnFailure'] = CopixFormatter::getBool ($credentialHandlers[$key]['stopOnFailure']);
		}
		$sections[$section]['credentialHandlers'] = $credentialHandlers;

		$ppo->sections = $sections;
		return _arPPO ($ppo, 'parameters/copix.php');
	}

	/**
	 * Retourne un tableau avec les constantes qui sont définies, et leur valeurs
	 *
	 * @param array $pConstants Constantes que l'on veut, clef = index, valeur = nom de la constante
	 * @param boolean $pSort Indique si on veut trier le retour
	 * @param boolean $pIsDir Indique si la valeur est un répertoire, et qu'on doit le passer dans CopixFile::getRealPath
	 * @return array
	 */
	private function _getConstants ($pConstants, $pSort = true, $pIsDir = false) {
		$toReturn = array ();
		foreach ($pConstants as $constant) {
			if (defined ($constant)) {
				if ($pIsDir) {
					$toReturn[$constant] = (file_exists (constant ($constant))) ? CopixFile::getRealPath (constant ($constant)) : constant ($constant);
				} else {
					$toReturn[$constant] = constant ($constant);
				}
			}
		}

		if ($pSort) {
			ksort ($toReturn);
		}
		return $toReturn;
	}

	/**
	 * Retourne la valeur d'une config dans CopixConfig
	 *
	 * @param array $pName Propriétés de CopixConfig, clef = index, valeur = nom de la propriété
	 * @param boolean $pFormat Indique comment on veut formatter le retour d'un boolean
	 * @return mixed
	 */
	private function _getConfigs ($pConfigs, $pFormatBool = false) {
		$toReturn = array ();
		foreach ($pConfigs as $config) {
			if (isset ($this->_config->$config)) {
				$toReturn[$config] = ($pFormatBool) ? CopixFormatter::getBool ($this->_config->$config) : $this->_config->$config;
			}
		}
		return $toReturn;
	}

	/**
	 * Affichage du PHPInfo dans la charte courante
	 *
	 * @return CopixActionReturn
	 */
	public function processWebServer () {
		$ppo = ToolsAdmin::setPage (ToolsAdmin::PAGE_PARAMETERS_WEBSERVER);

		ob_start ();
		phpinfo ();
		$info = ob_get_contents ();
		ob_end_clean ();
		$body = preg_replace ('%^.*<body>(.*)</body>.*$%ms', '$1', $info);
		$body = str_replace ('<table ', '<table class="CopixTable" ', $body);
		$body = str_replace ('<td class="e"', '<td', $body);
		$body = preg_replace_callback ('(<tr>)', array ($this, '_replace_callback'), $body);
		$ppo->phpinfo = $body;

		return _arPpo ($ppo, 'parameters/webserver.php');
	}

	/**
	 * Callback pour preg_replace_callback sur les <tr>
	 *
	 * @return string
	 */
	private function _replace_callback () {
		static $_alternate = '<tr>';
		$_alternate = ($_alternate == '<tr>') ? '<tr class="alternate">' : '<tr>';
		return $_alternate;
	}

	/**
	 * Affiche les informations sur le serveur de base de données
	 *
	 * @return CopixActionReturn
	 */
	public function processDBServer () {
		$profileName = _request ('profile', CopixConfig::instance ()->copixdb_getDefaultProfileName ());
		$ppo = ToolsAdmin::setPage (ToolsAdmin::PAGE_PARAMETERS_DBSERVER, array ('profile' => $profileName));

		$profile = CopixDb::getConnection ($profileName)->getProfile ();
		if (!($profile instanceof CopixDBProfile)) {
			throw new ModuleServersInfosException (_i18n ('copix.error.invalidDBProfile', $profileName), ModuleServersInfosException::INVALID_DB_PROFILE);
		}

		$ppo->selectedProfile = $profileName;

		// profil de connexion utilisé actuellement
		$parts = $profile->getConnectionStringParts ();
		$section = _i18n ('dbserver.section.profile', array ($profile->getName ()));
		$sections[$section][_i18n ('copix.dbProfile.connexionString')] = $profile->getConnectionString ();
		$sections[$section][_i18n ('copix.dbProfile.driverName')] = $profile->getDriverName ();
		$sections[$section][_i18n ('copix.dbProfile.databaseType')] = $profile->getDatabase ();
		$sections[$section][_i18n ('copix.dbProfile.user')] = $profile->getUser ();
		$sections[$section][_i18n ('copix.dbProfile.database')] = (isset ($parts['dbname'])) ? $parts['dbname'] : null;
		$sections[$section][_i18n ('copix.dbProfile.serverName')] = (isset ($parts['host'])) ? $parts['host'] : 'localhost';
		$sections[$section][_i18n ('copix.dbProfile.options')] = $profile->getOptions ();
		try {
			if (class_exists ($profile->getDatabase () . 'infos')) {
				_class ($profile->getDatabase () . 'infos')->fillInformations ($sections, $profileName);
			}
		} catch (Exception $e) {}
		$ppo->sections = $sections;
		$ppo->profiles = CopixConfig::instance ()->copixdb_getProfiles ();
		return _arPPO ($ppo, 'parameters/dbserver.php');
	}
}