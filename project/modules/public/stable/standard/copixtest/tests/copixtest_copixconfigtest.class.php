<?php
/**
* @package		standard
* @subpackage	copixtest
* @author		Croës Gérald
* @copyright	2001-2006 CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @package		standard
 * @subpackage	copixtest
 */
class CopixTest_CopixConfigTest extends CopixTest {
	/**
	 * Test de lecture d'un paramètre existant
	 */
	function testRead (){
		CopixConfig::get ('default|mailEnabled');
		CopixConfig::get ('|mailEnabled');
		$this->assertEquals (CopixConfig::get ('|mailEnabled'), CopixConfig::get ('default|mailEnabled'));
		CopixConfig::get ('copixtest|test');
		try {
			CopixConfig::get ('copixtest|parametreBidon');
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
		CopixConfig::set ('copixtest|test', 'fooValue');
		$this->assertEquals (CopixConfig::get ('copixtest|test'), 'fooValue');
	}
	 
	/**
	 * Tests de get/set avec des valeurs spéciales
	 */
	function testSpecialValues (){
		CopixConfig::set ('copixtest|test', 'fooValue avec des \' " / / \\ ');
		$this->assertEquals (CopixConfig::get ('copixtest|test'), 'fooValue avec des \' " / / \\ ');
		CopixConfig::get ('copixtest|test');
		CopixConfig::set ('copixtest|test', 'foo');
		$this->assertEquals (CopixConfig::get ('copixtest|testSpecial'), "mon\\chemin\\et\\sous\\chemin");
	}
}
?>