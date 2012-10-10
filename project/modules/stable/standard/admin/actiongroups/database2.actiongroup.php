<?php
/**
 * @package		standard
 * @subpackage	admin 
 * @author		Steevan BARBOYON
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Gestion des bases de données
 * 
 * @package		standard
 * @subpackage	admin 
 */
class ActionGroupDatabase2 extends CopixActionGroup {
	/**
	 * Liste des tables requises pour qu'on valide une installation de Copix
	 *
	 * @var array
	 */
	private $_tablesForCopix = array ('copixconfig', 'copixlog', 'copixmodule');
	
	/**
	 * Profiles de connexion que l'ont peut modifier, car dans un fichier généré en PHP
	 * 
	 * @var array
	 */
	private $_profilesCanEdit = array ();
	
	/**
	 * Fil d'ariane
	 * 
	 * @var array
	 */
	private $_breadcrumb = array ();
	
	/**
	 * Executé avant toute action
	 *
	 * @param string $pActionName Nom de l'action
	 */
	public function beforeAction ($pActionName) {
		CopixAuth::getCurrentUser ()->assertCredential ('basic:admin');
		if (file_exists (CopixConfig::copixdb_getConfigFilePath ())) {
			require (CopixConfig::instance ()->copixdb_getConfigFilePath ());
			$this->_profilesCanEdit = array_keys ($_db_profiles);
		}
		$this->_breadcrumb = array (
			_url ('admin||') => _i18n ('breadcrumb.admin'),
			_url ('admin|database2|') => _i18n ('breadcrumb.database2.default')
		);
		CopixPage::add ()->setIsAdmin (true);
	}
	
	/**
	 * Executé après l'action
	 */
	public function afterAction ($pActionName, $pActionReturn) {
		parent::afterAction ($pActionName, $pActionReturn);
		_notify ('breadcrumb', array ('path' => $this->_breadcrumb));
	}
	
	/**
	 * Action par défaut : affichage des bases de données configurées
	 *
	 * @return CopixActionReturn
	 */
	public function processDefault () {
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = _i18n ('database2.default.title_page');
		
		$ppo->profiles = array ();		
		$ppo->defaultProfileName = CopixConfig::instance ()->copixdb_getDefaultProfileName ();
		$ppo->defaultProfile = null;
		$profiles = CopixConfig::instance ()->copixdb_getProfiles ();
		foreach ($profiles as $profileName) {
			$profile = CopixConfig::instance ()->copixdb_getProfile ($profileName);
			$parts = $profile->getConnectionStringParts ();
			if ($profileName == $ppo->defaultProfileName) {
				$profileToUse = &$ppo->defaultProfile;
			} else {
				$ppo->profiles[$profile->getName ()] = array ();
				$profileToUse = &$ppo->profiles[$profile->getName ()];
			}
			$profileToUse['dbname'] = (isset ($parts['dbname']) && strpos ($parts['dbname'], ' ') === false) ? $parts['dbname'] : null;
			$profileToUse['host'] = (isset ($parts['host'])) ? $parts['host'] : 'localhost';
			$profileToUse['user'] = $profile->getUser ();
			$profileToUse['driver'] = $profile->getDriverName ();
			$profileToUse['available'] = CopixDB::testConnection ($profile);
			$profileToUse['canEdit'] = in_array ($profileName, $this->_profilesCanEdit);
			$profileToUse['copixIsInstalled'] = $this->_copixIsInstalled ($profileName);
		}
		
		return _arPPO ($ppo, 'database.list.tpl');
	}
	
	/**
	 * Ajouter une nouvelle base de données
	 *
	 * @return CopixActionReturn
	 */
	public function processAdd () {
		$this->_breadcrumb['#'] = _i18n ('breadcrumb.database2.add');
		
		if (_request ('add') == 1) {
			require (CopixDB::getDBProfilesPath ());
			$profileName = _request ('profile');
			// bien laisser defaultProfile comme ça, car il renvoie 0 ou 1 mais on veut écrire true ou false
			$_db_profiles[$profileName]['default'] = (_request ('defaultProfile')) ? true : false;
			$_db_profiles[$profileName]['driver'] = _request ('driver');
			$_db_profiles[$profileName]['connectionString'] = _request ('connectionString');
			$_db_profiles[$profileName]['user'] = _request ('user');
			$_db_profiles[$profileName]['password'] = _request ('password');
			$_db_profiles[$profileName]['extra'] = array ();			
			$_db_profiles[$profileName]['available'] = true;
			$_db_profiles[$profileName]['errorNotAvailable'] = null;

			$generator = new CopixPHPGenerator ();
			$pDefault = (_request ('defaultProfile')) ? $_db_default_profile : $profileName;
			$str = $generator->getPHPTags (
				$generator->getVariableDeclaration ('$_db_profiles', $_db_profiles) . $generator->getEndLine () .
				$generator->getVariableDeclaration ('$_db_default_profile', $pDefault)
			);
			exit ($str);
			/*if ($toReturn = CopixFile::write ($this->getPath (), $str)) {
				CopixConfig::reload ();
			}
			return $toReturn;*/
			return _arNone ();
			
		} else {		
			$ppo = new CopixPPO ();
			$ppo->TITLE_PAGE = _i18n ('database2.add.title_page');
			$ppo->profilesExists = CopixConfig::instance ()->copixdb_getProfiles ();
			$ppo->dbtypes = array ();
			$dbtypes = array ('mysql' => 'MySQL', 'oci' => 'Oracle', 'mssql' => 'SQL Server', 'sqlite' => 'SQLite', 'pgsql' => 'PostgreSQL');
			$drivers = CopixDB::getAvailableDrivers ();
			foreach ($drivers as $driver) {
				$driverIsOther = true;
				foreach ($dbtypes as $type => $name) {
					if (strpos ($driver, $type) !== false) {
						$ppo->dbtypes[$name][] = $driver;
						$driverIsOther = false;
					}
				}
				if ($driverIsOther) {
					$ppo->dbtypes[_i18n ('database2.driver.other')][] = $driver;
				}
			}
			if (count ($ppo->dbtypes) == 0) {
				throw new CopixException (_i18n ('database2.error.noDriverAvailable'));
			}
			
			// par défaut on coche dans l'ordre :  pdo_mysql, mysql, aucun
			$ppo->defaultDriver = null;
			if (in_array ('pdo_mysql', $drivers)) {
				$ppo->defaultDriver = 'pdo_mysql';
			} else if (in_array ('mysql', $drivers)) {
				$ppo->defaultDriver = 'mysql';
			}
			
			// temporaire, pour tester les interfaces rapidement
			$ppo->defaultDriver = 'pdo_sqlite';
			
			$mysqlDefaultHost = (ini_get ('mysql.defaulthost') != '') ? ini_get ('mysql.defaulthost') : 'localhost:3306';
			if (strpos ($mysqlDefaultHost, ':') !== false) {
				$ppo->mysqlDefaultHost = substr ($mysqlDefaultHost, 0, strpos ($mysqlDefaultHost, ':'));
				$ppo->mysqlDefaultPort = substr ($mysqlDefaultHost, strpos ($mysqlDefaultHost, ':') + 1);
			} else {
				$ppo->mysqlDefaultHost = $mysqlDefaultHost;
				$ppo->mysqlDefaultPort = 3306;
			}
			$ppo->defaultModules = implode (', ', _class ('installservice')->getDefaultModules ());
			$ppo->isDefaultProfile = (count ($ppo->profilesExists) == 0);
			
			return _arPPO ($ppo, 'database.edit.tpl');
		}
	}
	
	/**
	 * Edite les informations d'une base de données
	 *
	 * @return CopixActionReturn
	 */
	public function processEdit () {
		CopixRequest::assert ('profile');
		$profile = _request ('profile');
		$this->_assertCanEdit ($profile);
		$this->_breadcrumb['#'] = _i18n ('breadcrumb.database2.edit', $profile);
		
		return _arNone ();
	}
	
	/**
	 * Supprime un pfoil de connexion
	 *
	 * @return CopixActionReturn
	 */
	public function processDelete () {
		CopixRequest::assert ('profile');
		$profile = _request ('profile');
		$this->_assertCanEdit ($profile);
		$this->_breadcrumb['#'] = _i18n ('breadcrumb.database2.delete');
		
		if (_request ('confirm') != 1) {
			return CopixActionGroup::process (
				'generictools|Messages::getConfirm',
				array (
					'message' => _i18n ('database2.confirmDelete', array ($profile)),
					'confirm' => _url ('database2|delete', array ('confirm' => 1, 'profile' => $profile)),
					'cancel' => _url ('database2|')
				)
			);
			
		} else {
			return _arRedirect (_url ('database2|'));
		}
	}
	
	/**
	 * Test une connexion passée en paramètre
	 *
	 * @return CopixActionReturn
	 */
	public function processTestConnection () {
		$ppo = new CopixPPO ();
		$profileName = _request ('profile');
		$profile = $this->_createProfile ();
		$ppo->testConnection = CopixDB::testConnection ($profile);
		CopixConfig::instance ()->copixdb_defineProfile ($profileName, _request ('driver') . ':' . _request ('connectionString'), _request ('user'), _request ('password'));
		$ppo->copixIsInstalled = $this->_copixIsInstalled ($profileName);
		// _dump (CopixDb::getConnection ($profileName)->getTableList ());
		
		return _arDirectPPO ($ppo, 'database.testconnection.tpl');
	}
	
	/**
	 * Créé un profil depuis les informations passées en paramètre
	 *
	 * @return CopixDBProfile
	 */
	private function _createProfile () {
		$toReturn = new CopixDBProfile (
			_request ('profile'),
			_request ('driver') . ':' . _request ('connectionString'),
			_request ('user'),
			_request ('password'),
			null
		);
		
		return $toReturn;
	}
	
	/**
	 * Lève une exception si on n'a pas le droit d'éditer le profil de connexion $pProfileName
	 *
	 * @param string $pProfileName
	 * @throws CopixException Si le profil n'existe pas
	 * @throws CopixException Si on n'a pas le droit d'éditer le profil de connexion
	 */
	private function _assertCanEdit ($pProfileName) {
		$this->_assertExists ($pProfileName);
		if (!in_array ($pProfileName, $this->_profilesCanEdit)) {
			throw new CopixException (_i18n ('database2.error.cantEditProfile', $pProfileName));
		}
	}
	
	/**
	 * Lève une exception si le profil de connexion $pProfileName n'existe pas
	 *
	 * @param string $pProfileName
	 * @throws CopixException Si le profil de connexion n'existe pas
	 */
	private function _assertExists ($pProfileName) {
		if (!in_array ($pProfileName, CopixConfig::instance ()->copixdb_getProfiles ())) {
			throw new CopixException (_i18n ('database2.error.unknowProfile', $pProfileName));
		}
	}
	
	/**
	 * Indique si Copix est installé sur ce profil de connexion
	 *
	 * @param string $pProfileName
	 * @return boolean
	 */
	private function _copixIsInstalled ($pProfileName) {
		try {
	    	$tables = CopixDb::getConnection ($pProfileName)->getTableList ();
	    	return (array_intersect ($this->_tablesForCopix, $tables) == $this->_tablesForCopix);
		} catch (Exception $e) {
			return false;
		}
	}
}