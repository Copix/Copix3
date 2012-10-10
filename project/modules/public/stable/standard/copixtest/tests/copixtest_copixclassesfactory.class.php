<?php
/**
 * @package standard
 * @subpackage copixtest
 * @author		Croës Gérald
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file 
 */

/**
 * @package	standard
 * @subpackage copixtest
 * 
 * Test de CopixClassesFactory
 */
class CopixTest_CopixClassesFactory extends CopixTest {
	public function setUp (){
		CopixContext::push ('copixtest');
	}

	/**
	 * Test de création simple
	 */
	public function testCreate (){
		$foo = _class ('fooclass');
		$foo2 = _class ('copixtest|fooclass');
		
		$this->assertEquals (strtolower (get_class ($foo)), 'fooclass');
		$this->assertEquals (strtolower (get_class ($foo2)), 'fooclass');

		$this->assertTrue ($foo !== $foo2);
	}
	
	public function testFileInclude (){
		$array = _classInclude ('copixtest|');
		$array2 = array ('cachetestadaptator', 'fooclass', 
						'fooconstructedclass', 'testuserhandler',
						'validatorconstructnointerface', 'validatormod', 
						'validatormod2', 'validatornointerface');
		sort ($array);
		sort ($array2);
		$this->assertEquals ($array, $array2);
	}

	/**
	 * Test d'instance unique
	 */
	public function testInstanceof (){
		$foo = _ioClass ('fooclass');
		$foo2 = _ioClass ('fooclass');
		$this->assertTrue ($foo === $foo2);
		
		//instance unique avec un autre id
		$foo3 = _ioClass ('fooclass', 'id3');
		$this->assertTrue ($foo !== $foo3);
		$foo4 = _ioClass ('fooclass', 'id3');
		$this->assertTrue ($foo4 === $foo3);
	}
	
	/**
	 * Test de création avec des arguments
	 */
	public function testCreateParams (){
		$foo = _class ('fooconstructedclass');
		$this->assertEquals ('default', $foo->getVar ());

		$foo = _class ('fooconstructedclass', array ('var1'));
		$this->assertEquals ('var1', $foo->getVar ());

		$foo = _class ('fooconstructedclass', array ('var1', 'var2'));
		$this->assertEquals ('var1', $foo->getVar ());
		$this->assertEquals ('var2', $foo->getVar2 ());
	}
}
?>