<?php
/**
 * @package		standard
 * @subpackage	test
 * @author		Salleyron Julien
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Tests sur la classe CopixSession
 * 
 * @package		standard
 * @subpackage	test
 */
class Test_CopixCookie extends CopixTest {
	/**
	 * Appelé avant les tests
	 */
	public function setUp () {
		CopixContext::push ('test');
	}
	
	/**
	 * Appelé après les tests
	 */
	public function tearDown () {
		CopixContext::pop ();
	}
	
	/**
	 * Test un set, get, set sur une variable existante, delete avec des valeurs de type chaines et int
	 */
	public function testCRUD () {
		CopixCookie::set ('test|key', 'value');
		$this->assertEquals (CopixCookie::get ('test|key'), 'value');
		CopixCookie::set ('test|key', 12);
		$this->assertEquals (CopixCookie::get ('test|key'), 12);
		CopixCookie::delete ('test|key');
		$this->assertEquals (CopixCookie::get ('test|key'), null);
		$this->assertEquals (CopixCookie::get ('test|key', 'default', 128), 128);
	}
	
	/**
	 * Test un set, get, set sur une variable existante, delete avec des valeurs de type TestObjectInCookie
	 */
	public function testObjectCRUD () {
		// propriété statique pour montrer que le serialize ne prend pas en compte les propriétés statiques
		TestObjectInCookie::$staticProperty = 'staticValueChanged';
		$object = new TestObjectInCookie ('myNewValue');
		TestObjectInCookie::$staticProperty = 'staticValueChangedSecondTime';
		$object2 = new TestObjectInCookie ('myNewValue2');
		$arObjects = array ($object, $object2);
		CopixCookie::set ('test|key', $object);
		$this->assertEquals (CopixCookie::get ('test|key'), $object);
		CopixCookie::set ('test|key', $arObjects);
		$this->assertEquals (CopixCookie::get ('test|key'), $arObjects);
		CopixCookie::delete ('test|key');
		$this->assertEquals (CopixCookie::get ('test|key'), null);
	}
	
	/**
	 * Test un set, get, set sur une variable existante, delete avec des valeurs de type array
	 */
/*	public function testArrayCRUD () {
		CopixCookie::set ('test|key', array ('yes 0'));
		CopixCookie::push ('test|key', 'yes 1');
		CopixCookie::push ('test|key', 'yes 2');
		$this->assertEquals (CopixCookie::get ('test|key'), array ('yes 0', 'yes 1', 'yes 2'));
		CopixCookie::delete ('test|key');
		$this->assertEquals (CopixCookie::get ('test|key'), null);
		CopixCookie::push ('test|key', 'yes 0');
		CopixCookie::push ('test|key', 'yes 1');
		$this->assertEquals (CopixCookie::get ('test|key'), array ('yes 0', 'yes 1'));
		CopixCookie::delete ('test|key');
		$this->assertEquals (CopixCookie::get ('test|key'), null);
	}*/
	
	/**
	 * Test l'appel à la méthode exists
	 */
	public function testExists () {
		CopixCookie::set ('test|key', 'yes');
		$this->assertEquals (CopixCookie::exists ('test|key'), true);
		CopixCookie::delete ('test|key');
		$this->assertEquals (CopixCookie::exists ('test|key'), false);
	}
	
	/**
	 * Test un set, get, set sur une variable existante, delete avec des valeurs de type chaines et int, dans un namespace 'test'
	 */
	public function testNamespaceCRUD () {
		CopixCookie::set ('test|key', 'value', 'test');
		$this->assertEquals (CopixCookie::get ('test|key', 'test'), 'value');
		CopixCookie::set ('test|key', 12, 'test');
		$this->assertEquals (CopixCookie::get ('test|key', 'test'), 12);
		CopixCookie::delete ('test|key', 'test');
		$this->assertEquals (CopixCookie::get ('test|key', 'test'), null);
	}
	
	/**
	 * Test la méthodes getNamespaces, namespaceExists, destroyNamespace et getVariables
	 */
	public function testNamespaces () {
		CopixCookie::destroyNamespace ('test_addNamespace');
		$namespaces = CopixCookie::getNamespaces ();
		$newNamespaces = $namespaces;
		CopixCookie::set ('test|addNamespace', true, 'test_addNamespace');
		CopixCookie::set ('test|addNamespace2', 'testValue', 'test_addNamespace');
		$this->assertEquals (CopixCookie::namespaceExists ('test_addNamespace'), true);
		$this->assertEquals (CopixCookie::getVariables ('test_addNamespace'), array (
			'test|addNamespace' => true,
			'test|addNamespace2' => 'testValue'
		));
		$newNamespaces[] = 'test_addNamespace';
		sort ($newNamespaces);
		$this->assertEquals (CopixCookie::getNamespaces (), $newNamespaces);
		CopixCookie::destroyNamespace ('test_addNamespace');
		$this->assertEquals (CopixCookie::getNamespaces (), $namespaces);
		$this->assertEquals (CopixCookie::namespaceExists ('test_addNamespace'), false);
	}
	
	/**
	 * Test les raccourcis _CookieSet et _CookieGet
	 */
	/*public function testShortcut () {
		_CookieSet ('test|key', 'value');
		$this->assertEquals (_CookieGet ('test|key'), 'value');
		_CookieSet ('test|key', null);
		$this->assertEquals (_CookieGet ('test|key'), null);
	}*/
}

/**
 * Classe pour tester les objets en Cookie
 *
 * @package		standard
 * @subpackage	test
 */
class TestObjectInCookie {
	private $_privateProperty = null;
	public $property = 'testValue';
	public static $staticProperty = 'staticValue';
	public function __construct ($pPrivateValue) {
		$this->_privateProperty = $pPrivateValue;
	}
}