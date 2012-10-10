<?php
/**
 * @package		standard
 * @subpackage	test
 * @author		Croës Gérald, Steevan BARBOYON
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
class Test_CopixSession extends CopixTest {
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
		CopixSession::set ('test|key', 'value');
		$this->assertEquals (CopixSession::get ('test|key'), 'value');
		CopixSession::set ('test|key', 12);
		$this->assertEquals (CopixSession::get ('test|key'), 12);
		CopixSession::delete ('test|key');
		$this->assertEquals (CopixSession::get ('test|key'), null);
		$this->assertEquals (CopixSession::get ('test|key', 'default', 128), 128);
	}
	
	/**
	 * Test un set, get, set sur une variable existante, delete avec des valeurs de type TestObjectInSession
	 */
	public function testObjectCRUD () {
		// propriété statique pour montrer que le serialize ne prend pas en compte les propriétés statiques
		TestObjectInSession::$staticProperty = 'staticValueChanged';
		$object = new TestObjectInSession ('myNewValue');
		TestObjectInSession::$staticProperty = 'staticValueChangedSecondTime';
		$object2 = new TestObjectInSession ('myNewValue2');
		$arObjects = array ($object, $object2);
		CopixSession::set ('test|key', $object);
		$this->assertEquals (CopixSession::get ('test|key'), $object);
		CopixSession::setObject ('test|key', $object, null);
		$this->assertEquals (CopixSession::get ('test|key'), $object);
		CopixSession::set ('test|key', $arObjects);
		$this->assertEquals (CopixSession::get ('test|key'), $arObjects);
		CopixSession::delete ('test|key');
		$this->assertEquals (CopixSession::get ('test|key'), null);
	}
	
	/**
	 * Test un set, get, set sur une variable existante, delete avec des valeurs de type array
	 */
	public function testArrayCRUD () {
		CopixSession::set ('test|key', array ('yes 0'));
		CopixSession::push ('test|key', 'yes 1');
		CopixSession::push ('test|key', 'yes 2');
		$this->assertEquals (CopixSession::get ('test|key'), array ('yes 0', 'yes 1', 'yes 2'));
		CopixSession::delete ('test|key');
		$this->assertEquals (CopixSession::get ('test|key'), null);
		CopixSession::push ('test|key', 'yes 0');
		CopixSession::push ('test|key', 'yes 1');
		$this->assertEquals (CopixSession::get ('test|key'), array ('yes 0', 'yes 1'));
		CopixSession::delete ('test|key');
		$this->assertEquals (CopixSession::get ('test|key'), null);
	}
	
	/**
	 * Test l'appel à la méthode exists
	 */
	public function testExists () {
		CopixSession::set ('test|key', 'yes');
		$this->assertEquals (CopixSession::exists ('test|key'), true);
		CopixSession::delete ('test|key');
		$this->assertEquals (CopixSession::exists ('test|key'), false);
	}
	
	/**
	 * Test un set, get, set sur une variable existante, delete avec des valeurs de type chaines et int, dans un namespace 'test'
	 */
	public function testNamespaceCRUD () {
		CopixSession::set ('test|key', 'value', 'test');
		$this->assertEquals (CopixSession::get ('test|key', 'test'), 'value');
		CopixSession::set ('test|key', 12, 'test');
		$this->assertEquals (CopixSession::get ('test|key', 'test'), 12);
		CopixSession::delete ('test|key', 'test');
		$this->assertEquals (CopixSession::get ('test|key', 'test'), null);
	}
	
	/**
	 * Test la méthodes getNamespaces, namespaceExists, destroyNamespace et getVariables
	 */
	public function testNamespaces () {
		CopixSession::destroyNamespace ('test_addNamespace');
		$namespaces = CopixSession::getNamespaces ();
		$newNamespaces = $namespaces;
		CopixSession::set ('test|addNamespace', true, 'test_addNamespace');
		CopixSession::set ('test|addNamespace2', 'testValue', 'test_addNamespace');
		$this->assertEquals (CopixSession::namespaceExists ('test_addNamespace'), true);
		$this->assertEquals (CopixSession::getVariables ('test_addNamespace'), array (
			'test|addNamespace' => true,
			'test|addNamespace2' => 'testValue'
		));
		$newNamespaces[] = 'test_addNamespace';
		sort ($newNamespaces);
		$this->assertEquals (CopixSession::getNamespaces (), $newNamespaces);
		CopixSession::destroyNamespace ('test_addNamespace');
		$this->assertEquals (CopixSession::getNamespaces (), $namespaces);
		$this->assertEquals (CopixSession::namespaceExists ('test_addNamespace'), false);
	}
	
	/**
	 * Test les raccourcis _sessionSet et _sessionGet
	 */
	public function testShortcut () {
		_sessionSet ('test|key', 'value');
		$this->assertEquals (_sessionGet ('test|key'), 'value');
		_sessionSet ('test|key', null);
		$this->assertEquals (_sessionGet ('test|key'), null);
	}
	
	/**
	 * Test de modification indirecte
	 */
	public function testIndirectModification (){
		//Test avec des objets (ce test ne fonctionnerait PAS avec d'autres types)
		$object = new StdClass ();
		$object->property = 'new';
		CopixSession::set ('value', $object);
		
		$object = CopixSession::get ('value');
		$object->property = 'modifiée ailleur';
		$this->assertEquals ($object, CopixSession::get ('value'));
	}

	public function testDestroy (){
		CopixSession::set ('foo', 1);
		CopixSession::destroy ();
		$this->assertEquals (CopixSession::get ('foo'), null);
		
		CopixSession::start ();
		CopixSession::set ('foo', 1);
		$this->assertEquals (CopixSession::get ('foo'), 1);
	}
}

/**
 * Classe pour tester les objets en session
 *
 * @package		standard
 * @subpackage	test
 */
class TestObjectInSession {
	private $_privateProperty = null;
	public $property = 'testValue';
	public static $staticProperty = 'staticValue';
	public function __construct ($pPrivateValue) {
		$this->_privateProperty = $pPrivateValue;
	}
}