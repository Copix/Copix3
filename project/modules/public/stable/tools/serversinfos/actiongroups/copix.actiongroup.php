<?php 
/**
 * @package tools
 * @subpackage serversinfos
 * @copyright CopixTeam
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 * @author Steevan BARBOYON
 * @link http://www.copix.org
 */

/**
 * Informations sur Copix
 * 
 * @package tools
 * @subpackage serversinfos
 */
class ActionGroupCopix extends CopixActionGroup {
	/**
	 * Formatte le retour de _getConfig en boolean
	 */
	const FORMAT_BOOL = 1;
	
	/**
	 * Instance de CopixConfig, pour éviter de trop appeler le singleton
	 *
	 * @var CopixConfig
	 */
	private $_config = null;
	
	/**
	 * Actions limitées aux administrateurs
	 * 
	 * @param string $pActionName Nom de l'action
	 */
	public function beforeAction ($pActionName) {
		CopixAuth::getCurrentUser ()->assertCredential ('basic:admin');
	}
	
	/**
	 * Affichage des infos sur copix
	 * 
	 * @return CopixActionReturn
	 */
	public function processDefault () {
		_notify ('breadcrumb', array ('path' => array (
			_url ('admin||') => _i18n ('admin|breadcrumb.admin'),
			_url ('serversinfos|copix|') => _i18n ('module.breadcrumb.copixInfos'),
		)));
		
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = _i18n ('copix.titlepage');
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
			'COPIX_TEMP_PATH', 'COPIX_CACHE_PATH', 'COPIX_VAR_PATH'  
		));
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

		// fichiers de base
		$files = $this->_getConstants (array (
			'COPIX_CLASSPATHS_FILE', 'COPIX_CONFIG_FILE', 'COPIX_INC_FILE', 'COPIX_PROJECTCONTROLLER_FILE', 'COPIX_TESTCONTROLLER_FILE'
		));
		if (count ($files) > 0) {
			$sections[_i18n ('copix.section.files')] = $files;
		}
		
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
		$sections[$section] = array_merge ($sections[$section], $this->_getConfigs ($configs, ActionGroupCopix::FORMAT_BOOL));
		ksort ($sections[$section]);
		
		// configuration i18n
		$section = _i18n ('copix.section.i18n');
		$sections[$section] = $this->_getConfigs (array ('default_language', 'default_country', 'default_timezone'));
		$configs = array ('i18n_path_enabled', 'i18n_missingKeyLaunchException');
		$sections[$section] = array_merge ($sections[$section], $this->_getConfigs ($configs, ActionGroupCopix::FORMAT_BOOL));
		
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
		return _arPPO ($ppo, 'copix.tpl');
	}
	
	/**
	 * Retourne un tableau avec les constantes qui sont définies, et leur valeurs
	 *
	 * @param array $pConstants Constantes que l'on veut, clef = index, valeur = nom de la constante
	 * @param boolean $pSort Indique si on veut trier le retour
	 * @return array
	 */
	private function _getConstants ($pConstants, $pSort = true) {
		$toReturn = array ();
		foreach ($pConstants as $constant) {
			if (defined ($constant)) {
				$toReturn[$constant] = constant ($constant);
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
	 * @param int $pFormat Indique comment on veut formatter le retour, utiliser les constantes ActionGroupCopix::FORMAT_X
	 * @return mixed
	 */
	private function _getConfigs ($pConfigs, $pFormat = null) {
		$toReturn = array ();
		foreach ($pConfigs as $config) {
			if (isset ($this->_config->$config)) {
				switch ($pFormat) {
					case ActionGroupCopix::FORMAT_BOOL : $value = CopixFormatter::getBool ($this->_config->$config); break;
					default : $value = $this->_config->$config;
				}
				$toReturn[$config] = $value;
			}
		}
		return $toReturn;
	}
}