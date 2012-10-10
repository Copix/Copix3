<?php
/**
* @package		standard
* @subpackage	test
* @author		Croës Gérald
* @copyright	2001-2006 CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @package		standard
 * @subpackage	test
 */
class Test_CopixDBProfileTest extends CopixTest {
	/**
	 * Test de connexion à toutes les connexions définies
	 * On dois faire ce test avant les autres car les autres tests peuvent créer des profils incorrects
	 */
	function testAllDefinedProfiles (){
		foreach (CopixConfig::instance ()->copixdb_getProfiles () as $name){
			try {
				CopixDB::getConnection ($name);
				$this->assertTrue (true);//ok
			}catch (Exception $exception){
				$this->assertTrue ($exception->getMessage ());//n'a pas fonctionné 
			}
		}
	}

	public function testMain (){
		//compte le nombre de profils au départ
		$nbStart = count (CopixConfig::instance ()->copixdb_getProfiles ());

		//vérifie que l'on arrive bien à définir un profil
		CopixConfig::instance ()->copixdb_defineProfile ('test', 'pdo_mysql:host=localhost;dbname=copix_3_beta', 'root', '');
		$this->assertNotNull (CopixConfig::instance ()->copixdb_getProfile ('test'));
		$this->assertEquals ($nbStart+1, count (CopixConfig::instance ()->copixdb_getProfiles ()));
		$this->assertEquals (CopixConfig::instance ()->copixdb_getProfile ('test')->getName (),
		'test');
		$parts = CopixConfig::instance ()->copixdb_getProfile ('test')->getConnectionStringParts ();
		$this->assertEquals ('localhost', $parts['host']);
		$this->assertEquals ('copix_3_beta', $parts['dbname']);
		$this->assertEquals ('pdo_mysql', CopixConfig::instance ()->copixdb_getProfile ('test')->getDriverName ());
		$this->assertEquals (array (), CopixConfig::instance ()->copixdb_getProfile ('test')->getOptions ());
		$this->assertEquals ('mysql', CopixConfig::instance ()->copixdb_getProfile ('test')->getDatabase ());
		
		CopixConfig::instance ()->copixdb_getProfile ('test')->setOptions (array ('FOO'=>'FOOVALUE'));
		$this->assertEquals (array ('FOO'=>'FOOVALUE'), CopixConfig::instance ()->copixdb_getProfile ('test')->getOptions ());
		$this->assertEquals ('FOOVALUE', CopixConfig::instance ()->copixdb_getProfile('test')->getOption ('FOO'));
		CopixConfig::instance ()->copixdb_getProfile ('test')->setOptions (array ('FOO2'=>'FOOVALUE2'));
		$this->assertEquals (array ('FOO'=>'FOOVALUE', 'FOO2'=>'FOOVALUE2'), CopixConfig::instance ()->copixdb_getProfile ('test')->getOptions ());
		CopixConfig::instance ()->copixdb_getProfile ('test')->clearOptions ();
		$this->assertEquals (array (), CopixConfig::instance ()->copixdb_getProfile ('test')->getOptions ());

		//vérifie que l'on arrive à ajouter encore un 2ème profil
		CopixConfig::instance ()->copixdb_defineProfile ('test2', 'pdo_mysql:host=localhost;dbname=copix_3_beta', 'root', '');
		$this->assertNotNull (CopixConfig::instance ()->copixdb_getProfile ('test2'));
		$this->assertEquals ($nbStart+2, count (CopixConfig::instance ()->copixdb_getProfiles ()));
		$this->assertEquals (CopixConfig::instance ()->copixdb_getProfile ('test2')->getName (),
		'test2');
	}
}
?>