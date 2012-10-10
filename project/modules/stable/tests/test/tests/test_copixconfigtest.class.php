<?php
/**
 * @package		standard
 * @subpackage	test
 * @author		Croës Gérald
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * @package		standard
 * @subpackage	test
 */
class Test_CopixConfigTest extends CopixTest {
	public function setUp (){
		static $deleted = false;
		if ($deleted === false){
			CopixFile::removeFileFromPath (COPIX_CACHE_PATH . 'php/config/', false);
			$deleted = true;
		}
	}

	/**
	 * Test de lecture d'un paramètre existant
	 */
	function testRead (){
		CopixConfig::get ('default|mailEnabled');
		CopixConfig::get ('|mailEnabled');
		$this->assertEquals (CopixConfig::get ('|mailEnabled'), CopixConfig::get ('default|mailEnabled'));
		CopixConfig::get ('test|test');
		try {
			CopixConfig::get ('test|parametreBidon');
			$this->fail ('Aucune exception de générée pour un paramètre inexistant');
		}catch (CopixException $e){
		}
		$this->assertFalse (CopixConfig::exists ('|parametreBidon'));
		$this->assertTrue (CopixConfig::exists ('default|mailEnabled'));

	}

	/**
	 * Test d'écriture
	 */
	function testWrite (){
		CopixConfig::set ('test|test', 'fooValue');
		$this->assertEquals (CopixConfig::get ('test|test'), 'fooValue');
	}

	/**
	 * Tests de get/set avec des valeurs spéciales
	 */
	function testSpecialValues (){
		CopixConfig::set ('test|test', 'fooValue avec des \' " / / \\ ');
		$this->assertEquals (CopixConfig::get ('test|test'), 'fooValue avec des \' " / / \\ ');
		CopixConfig::get ('test|test');
		CopixConfig::set ('test|test', 'foo');
		$this->assertEquals (CopixConfig::get ('test|testSpecial'), "mon\\chemin\\et\\sous\\chemin");
	}

	/**
	 * Test getRealPath
	 */
	function testRealPath() {

		if(@realpath (".") === false) {
			$this->skip ("realpath disabled");
		}

		$config = CopixConfig::instance ();
		$wasDisabled = $config->realPathDisabled;
		$config->realPathDisabled = true;

		try {
			$this->assertEquals (realpath ("."), CopixConfig::getRealPath ("."));
			$this->assertEquals (realpath ("./"), CopixConfig::getRealPath ("./"));
			$this->assertEquals (realpath ("./"), CopixConfig::getRealPath (".\\"));
			$this->assertEquals (realpath (".."), CopixConfig::getRealPath (".."));
			$this->assertEquals (realpath ("../"), CopixConfig::getRealPath ("../"));
			if($config->osIsWindows ()) {
				$this->assertEquals (realpath("C:\\"), CopixConfig::getRealPath("C:\\"));
			} else {
				$this->assertEquals (realpath ("/"), CopixConfig::getRealPath ("/"));
			}
			$this->assertEquals (realpath (COPIX_TEMP_PATH), CopixConfig::getRealPath (COPIX_TEMP_PATH));
			$this->assertEquals (realpath (COPIX_PATH), CopixConfig::getRealPath (COPIX_PATH));
		} catch (Exception $e) {
			$config->realPathDisabled = $wasDisabled;
			throw $e;
		}
	}
}