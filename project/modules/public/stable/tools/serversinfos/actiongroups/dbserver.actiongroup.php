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
 * Informations sur le serveur de base de données
 * @package devtools
 * @subpackage moduleeditor
 */
class ActionGroupDBServer extends CopixActionGroup {
	
	/**
	 * Seuls les administrateurs ont accès à cette section
	 */
	public function beforeAction ($pActionName) {
		_notify ('breadcrumb', array (
			'path' => array (_url ('admin||') => _i18n ('admin|breadcrumb.admin'))
		));
		CopixAuth::getCurrentUser ()->assertCredential ('basic:admin');
	}
	
	/**
	 * Affiche les informations sur le serveur de base de données
	 */
	public function processDefault () {
		$config = CopixConfig::instance ();
		$connection = _request ('connection', $config->copixdb_getDefaultProfileName ());
		$profile = CopixDb::getConnection ($connection)->getProfile ();
		if (!($profile instanceof CopixDBProfile)) {
			_classInclude ('serversinfos|moduleserversinfosexception');
			throw new ModuleServersInfosException (_i18n ('copix.error.invalidDBProfile', $profile), ModuleServersInfosException::INVALID_DB_PROFILE);
		}
		
		_notify ('breadcrumb', array (
			'path' => array (
				_url ('serversinfos|dbserver|') => _i18n ('module.breadcrumb.dbInfos', $connection),
			)
		));
		
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = _i18n ('dbserver.titlePage', $connection);
		$ppo->selectedConnection = $connection;
		
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
			_class ($profile->getDatabase () . 'infos')->fillInformations ($sections, $connection);
		} catch (Exception $e) {}
		$ppo->sections = $sections;
		$ppo->connections = CopixConfig::instance ()->copixdb_getProfiles ();
		return _arPPO ($ppo, 'dbserver.tpl');
	}
}