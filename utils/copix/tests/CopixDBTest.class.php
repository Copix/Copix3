<?php
/**
* @package    copix
* @subpackage tests
* @author     Guillaume Perréal
* @copyright  2001-2008 CopixTeam
* @link       http://copix.org
* @license    http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Classe de base pour les tests Copix modifiant de façon définitive la base de données.
 * 
 * Force l'utilisation du profil "copixtest" par défaut. 
 *
 */
class CopixDBTest extends CopixTest {
	
	/**
	 * Nom du profile de la base de test.
	 *
	 */
	const TEST_PROFILE_NAME = 'copixtest';
	
	/**
	 * Sauvegarde du profil par défaut.
	 *
	 * @var string 
	 */
	private $_savedProfile;
	
	/**
	 * Sauvegarde le profil par défaut et le remplace par TEST_PROFILE_NAME. 
	 *
	 */
	public function setUp() {
		parent::setUp();
		
		$config = CopixConfig::instance();
		$profiles = $config->copixdb_getProfiles();
		if(!in_array(self::TEST_PROFILE_NAME, $profiles)) {
			throw new PHPUnit_Framework_SkippedTestError(_i18n("copix:copix.error.test.missingDBTestProfile", self::TEST_PROFILE_NAME));
		}
		
		$this->_savedProfile = $config->copixdb_getDefaultProfileName();
		$config->copixdb_defineDefaultProfileName(self::TEST_PROFILE_NAME);		
	}
	
	/**
	 * Remet en place le profil par défaut.
	 *
	 */
	public function tearDown() {		
		CopixConfig::instance()->copixdb_defineDefaultProfileName($this->_savedProfile);
		parent::tearDown();
	}
	
	/**
	 * Retourne une connexion à la base de test.
	 * 
	 * @return CopixDBConnection 
	 */
	protected function _getConnection() {
		return CopixDB::getConnection(self::TEST_PROFILE_NAME);
	}
	
	/**
	 * Execute un script SQL sur la base de test.
	 * 
	 * Dans le nom du chemin, la chaîne $drivername est remplacé par le nom du driver.
	 * 
	 * @param string $pScriptId Identifer du chemin du script (peut être de la forme "module|fichier").
	 */
	protected function _runSQLScript($pScriptId) {
		$selector = CopixSelectorFactory::create($pScriptId);
		$ct = $this->_getConnection();
		$filePath = str_replace('$drivername', $ct->getProfile()->getDriverName(), $selector->fileName);
		$ct->doSQLScript($selector->getPath($filePath), true);		
	}
	
	/**
	 * Supprime tous les enregistrements des tables indiquées dans la base de test.
	 *
	 * @param string ... Noms des tables.
	 */
	protected function _truncateTables() {
		$tables = func_get_args();
		$ct = $this->_getConnection();
		foreach($tables as $table) {
			$ct->doQuery("TRUNCATE TABLE ".$table);
		}
	}
}

?>