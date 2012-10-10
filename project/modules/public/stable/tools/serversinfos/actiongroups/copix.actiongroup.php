<?php 
/**
 * @package devtools
 * @subpackage moduleeditor
 * @copyright CopixTeam
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 * @author Steevan BARBOYON
 * @link http://www.copix.org
 */

/**
 * Informations sur Copix
 * @package devtools
 * @subpackage moduleeditor
 */
class ActionGroupCopix extends CopixActionGroup {
	
	/**
	 * Actions limitées aux administrateurs
	 */
	public function beforeAction ($pActionName) {
		CopixAuth::getCurrentUser ()->assertCredential ('basic:admin');
	}
	
	/**
	 * Affichage des infos sur copix.
	 */
	public function processDefault () {
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = _i18n ('copix.titlepage');
		
		$sections = array ();
		
		// infos de version
		$section = _i18n ('copix.section.version');
		$sections[$section]['COPIX_VERSION'] = COPIX_VERSION;
		$sections[$section]['COPIX_VERSION_MAJOR'] = COPIX_VERSION_MAJOR;
		$sections[$section]['COPIX_VERSION_MINOR'] = COPIX_VERSION_MINOR;
		$sections[$section]['COPIX_VERSION_FIX'] = 	COPIX_VERSION_FIX;
		$sections[$section]['COPIX_VERSION_RC'] = COPIX_VERSION_RC;
		$sections[$section]['COPIX_VERSION_BETA'] = COPIX_VERSION_BETA;
		$sections[$section]['COPIX_VERSION_DEV'] = COPIX_VERSION_DEV;
		
		// infos sur les répertoires
		$section = _i18n ('copix.section.path');
		$sections[$section]['COPIX_PATH'] = COPIX_PATH;
		$sections[$section]['COPIX_CORE_PATH'] = COPIX_CORE_PATH;
		$sections[$section]['COPIX_UTILS_PATH'] = COPIX_UTILS_PATH;
		$sections[$section]['COPIX_PROJECT_PATH'] = COPIX_PROJECT_PATH;
		$sections[$section]['COPIX_TEMP_PATH'] = COPIX_TEMP_PATH;
		$sections[$section]['COPIX_CACHE_PATH'] = COPIX_CACHE_PATH;
		$sections[$section]['COPIX_LOG_PATH'] = COPIX_LOG_PATH;
		$sections[$section]['COPIX_VAR_PATH'] = COPIX_VAR_PATH;
		$sections[$section]['COPIX_SMARTY_PATH'] = COPIX_SMARTY_PATH;
		$sections[$section]['COPIX_ACTIONGROUP_DIR'] = COPIX_ACTIONGROUP_DIR;
		$sections[$section]['COPIX_DESC_DIR'] = COPIX_DESC_DIR;
		$sections[$section]['COPIX_ZONES_DIR'] = COPIX_ZONES_DIR;
		$sections[$section]['COPIX_TEMPLATES_DIR'] = COPIX_TEMPLATES_DIR;
		$sections[$section]['COPIX_STATIC_DIR'] = COPIX_STATIC_DIR;
		$sections[$section]['COPIX_CLASSES_DIR'] = COPIX_CLASSES_DIR;
		$sections[$section]['COPIX_RESOURCES_DIR'] = COPIX_RESOURCES_DIR;
		$sections[$section]['COPIX_PLUGINS_DIR'] = COPIX_PLUGINS_DIR;
		$sections[$section]['COPIX_INSTALL_DIR'] = COPIX_INSTALL_DIR;		
		$sections[$section]['arModulesPath'] = CopixConfig::instance ()->arModulesPath;
		$sections[$section]['arPluginsPath'] = CopixConfig::instance ()->arPluginsPath;
		
		// configuration générale
		$section = _i18n ('copix.section.config');
		switch (CopixConfig::instance ()->getMode ()) {
			case CopixConfig::DEVEL : $sections[$section]['mode'] = 'DEVEL'; break;
			case CopixConfig::PRODUCTION : $sections[$section]['mode'] = 'PRODUCTION'; break;
			case CopixConfig::FORCE_INITIALISATION : $sections[$section]['mode'] = 'FORCE_INITIALISATION'; break;
			default : $sections[$section]['mode'] = 'UNKNOW'; break;
		}
		$sections[$section]['checkTrustedModules'] = CopixFormatter::getBool (CopixConfig::instance ()->checkTrustedModules);
		$sections[$section]['sessionName'] = CopixConfig::instance ()->sessionName;
		$sections[$section]['apcEnabled'] = CopixFormatter::getBool (CopixConfig::instance ()->apcEnabled);
		$sections[$section]['default_language'] = CopixConfig::instance ()->default_language;
		$sections[$section]['default_country'] = CopixConfig::instance ()->default_country;
		$sections[$section]['default_charset'] = CopixConfig::instance ()->default_charset;
		$sections[$section]['i18n_path_enabled'] = CopixFormatter::getBool (CopixConfig::instance ()->i18n_path_enabled);
		$sections[$section]['missingKeyTriggerErrorLevel'] = (CopixConfig::instance ()->missingKeyTriggerErrorLevel == E_USER_ERROR) ? 'E_USER_ERROR' : CopixConfig::instance ()->missingKeyTriggerErrorLevel;
		$sections[$section]['compile_check'] = CopixFormatter::getBool (CopixConfig::instance ()->compile_check);
		$sections[$section]['force_compile'] = CopixFormatter::getBool (CopixConfig::instance ()->force_compile);
		$sections[$section]['template_caching'] = CopixFormatter::getBool (CopixConfig::instance ()->template_caching);
		$sections[$section]['template_use_sub_dirs'] = CopixFormatter::getBool (CopixConfig::instance ()->template_use_sub_dirs);
		$sections[$section]['mainTemplate'] = CopixConfig::instance ()->mainTemplate;
		$sections[$section]['invalidActionTriggersError'] = CopixFormatter::getBool (CopixConfig::instance ()->invalidActionTriggersError);
		$sections[$section]['notFoundDefaultRedirectTo'] = CopixFormatter::getBool (CopixConfig::instance ()->notFoundDefaultRedirectTo);
		$sections[$section]['overrideUnserializeCallbackEnabled'] = CopixFormatter::getBool (CopixConfig::instance ()->overrideUnserializeCallbackEnabled);
		
		// configuration des url
		$section = _i18n ('copix.section.configUrl');
		$sections[$section]['significant_url_mode'] = CopixConfig::instance ()->significant_url_mode;
		$sections[$section]['significant_url_prependIIS_path_key'] = CopixConfig::instance ()->significant_url_prependIIS_path_key;
		$sections[$section]['stripslashes_prependIIS_path_key'] = CopixFormatter::getBool (CopixConfig::instance ()->stripslashes_prependIIS_path_key);
		$sections[$section]['url_requestedscript_variable'] = CopixConfig::instance ()->url_requestedscript_variable;
		
		// configuration des bases de données
		$section = _i18n ('copix.section.configDb');
		$sections[$section][_i18n ('copix.configDb.givenDrivers')] = CopixDB::getAllDrivers ();
		$sections[$section][_i18n ('copix.configDb.availableDrivers')] = CopixDB::getAvailableDrivers ();
		$sections[$section][_i18n ('copix.configDb.profils')] = CopixConfig::instance ()->copixdb_getProfiles ();
		$sections[$section][_i18n ('copix.configDb.defaultProfil')] = CopixConfig::instance ()->copixdb_getDefaultProfileName ();
		
		// profil de connexion utilisé actuellement
		$profile = CopixDb::getConnection ()->getProfile ();
		$parts = $profile->getConnectionStringParts ();
		$section = _i18n ('copix.section.dbProfile', array ($profile->getName ()));
		$sections[$section][_i18n ('copix.dbProfile.connexionString')] = $profile->getConnectionString ();
		$sections[$section][_i18n ('copix.dbProfile.driverName')] = $profile->getDriverName ();
		$sections[$section][_i18n ('copix.dbProfile.databaseType')] = $profile->getDatabase ();
		$sections[$section][_i18n ('copix.dbProfile.user')] = $profile->getUser ();
		$sections[$section][_i18n ('copix.dbProfile.database')] = $parts['dbname'];
		$sections[$section][_i18n ('copix.dbProfile.serverName')] = (isset ($parts['host'])) ? $parts['host'] : 'localhost';
		$sections[$section][_i18n ('copix.dbProfile.options')] = $profile->getOptions ();

		$section = _i18n ('copix.section.auth');
		$sections[$section]['copixauth_cache'] = CopixFormatter::getBool (CopixConfig::instance ()->copixauth_cache);
		
		$userHandlers = CopixConfig::instance ()->copixauth_getRegisteredUserHandlers ();
		//echo '<pre><div align="left">';
		foreach ($userHandlers as $key => $item) {
			$userHandlers[$key]['required'] = CopixFormatter::getBool ($userHandlers[$key]['required']);
		}
		$sections[$section]['userHandlers'] = $userHandlers;
		
		$groupHandlers = CopixConfig::instance ()->copixauth_getRegisteredGroupHandlers ();
		foreach ($groupHandlers as $key => $item) {
			$groupHandlers[$key]['required'] = CopixFormatter::getBool ($groupHandlers[$key]['required']);
		}
		$sections[$section]['groupHandlers'] = $groupHandlers;
		
		$credentialHandlers = CopixConfig::instance ()->copixauth_getRegisteredCredentialHandlers ();
		foreach ($credentialHandlers as $key => $item) {
			$credentialHandlers[$key]['stopOnSuccess'] = CopixFormatter::getBool ($credentialHandlers[$key]['stopOnSuccess']);
			$credentialHandlers[$key]['stopOnFailure'] = CopixFormatter::getBool ($credentialHandlers[$key]['stopOnFailure']);
		}
		$sections[$section]['credentialHandlers'] = $credentialHandlers;
		
		//$sections[$section][''] = ;
		
		$ppo->sections = $sections;		
		return _arPPO ($ppo, 'infos.tpl');
	}
}
 ?>