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
		CopixAuth::getCurrentUser ()->assertCredential ('basic:admin');
	}
	
	/**
	 * Affiche les informations sur le serveur de base de données
	 */
	public function processDefault () {
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = '';
		
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
		
		if ($profile->getDatabase () == 'mysql') {
			_class ('mysqlserverinfos')->fillInformations ($sections);
		}
		
		$ppo->sections = $sections;		
		return _arPPO ($ppo, 'infos.tpl');
	}
}