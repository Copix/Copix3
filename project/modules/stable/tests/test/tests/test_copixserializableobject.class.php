<?php
/**
 * @package    standard
 * @subpackage test
 * @author     Guillaume Perréal
 * @copyright  CopixTeam
 * @link       http://copix.org
 * @license    http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Tests sur la classe CopixSerializableObject
 * @package standard
 * @subpackage test
 */
class Test_CopixSerializableObject extends CopixTest {
	public function setUp (){
		CopixContext::push ('test');
	}
	public function tearDown (){
		CopixContext::pop ();
	}
	
	public function testString (){
		$string = 'éàu';
		$object = new CopixSerializableObject ($string);
		
		$serialized = serialize ($object);
		$unserialized = unserialize ($serialized);

		$this->assertEquals ($unserialized->getRemoteObject (), $string);
	}

	public function testCopixObject (){
		//on test que les objets autoloadés Copix ne soient pas pris en charge
		$element = new CopixSerializableObject (CopixConfig::instance ());
		$serialized = serialize ($element);
		$elementBack = unserialize ($serialized);
		//si ça ne génère pas d'erreurs, c'est ok
	}

	public function testAutoloadedObject (){
		//Ici ce n'est pas vraiment un objet autoloadé, mais l'effet sera le même pour notre test.
		$element = new CopixSerializableObject ($this);
		$serialized = serialize ($element);
		$elementBack = unserialize ($serialized);
	}

	public function testDAOAuto (){
		$element = new CopixSerializableObject ($dao = _ioDAO ('testautodao'), 'testautodao');
		$element->findAll ();//appel d'une méthode pour vérifier le pont

		//On vérifie que l'élément est bien dedans et qu'il est identique
		$this->assertEquals ($element->getRemoteObject (), $dao);

		$serialized = serialize ($element);
		$elementBack = unserialize ($serialized);

		$elementBack-> findAll ();//vérifie que le contexte est toujours ok et qu'on peut manipuler le dao
	}

	public function testDAOXML (){
		$element = new CopixSerializableObject ($dao = _ioDAO ('testmain'));
		$element->findAll ();//appel d'une méthode pour vérifier le pont

		//On vérifie que l'élément est bien dedans et qu'il est identique
		$this->assertEquals ($element->getRemoteObject (), $dao);

		$serialized = serialize ($element);
		$elementBack = unserialize ($serialized);

		$elementBack->findAll ();//vérifie que le contexte est toujours ok et qu'on peut manipuler le dao

		//-- Même chose en spécifiant le qualificateur
		$element = new CopixSerializableObject ($dao = _ioDAO ('testmain'), 'test|testmain');
		$element->findAll ();//appel d'une méthode pour vérifier le pont

		//On vérifie que l'élément est bien dedans et qu'il est identique
		$this->assertEquals ($element->getRemoteObject (), $dao);

		$serialized = serialize ($element);
		$elementBack = unserialize ($serialized);

		$elementBack->findAll ();//vérifie que le contexte est toujours ok et qu'on peut manipuler le dao

	}

	public function testRecordDAOAuto (){
		$element = new CopixSerializableObject ($record = _record ('testautodao'), 'testautodao');

		//On vérifie que l'élément est bien dedans et qu'il est identique
		$this->assertEquals ($element->getRemoteObject (), $record);
		$serialized  = serialize ($element);
		$elementBack = unserialize ($serialized);

		$this->assertEquals (strtolower (get_class ($elementBack->getRemoteObject ())), 'compileddaorecordtestautodao');
	}

	public function testRecordDAOXML (){
		$element = new CopixSerializableObject ($record = _record ('testmain'));

		//On vérifie que l'élément est bien dedans et qu'il est identique
		$this->assertEquals ($element->getRemoteObject (), $record);
		$serialized  = serialize ($element);
		$elementBack = unserialize ($serialized);

		$this->assertEquals (strtolower (get_class ($elementBack->getRemoteObject ())), 'compileddaorecordtestmain');

		//--- Même chose en spécifiant le qualificateur
		$element = new CopixSerializableObject ($record = _record ('testmain'), 'test|testmain');

		//On vérifie que l'élément est bien dedans et qu'il est identique
		$this->assertEquals ($element->getRemoteObject (), $record);
		$serialized  = serialize ($element);
		$elementBack = unserialize ($serialized);

		$this->assertEquals (strtolower (get_class ($elementBack->getRemoteObject ())), 'compileddaorecordtestmain');
	}

	public function testClass (){
		$element = new CopixSerializableObject ($object = _ioClass ('fooclass'));

		$this->assertEquals ($element->getRemoteObject (), $object);

		$serialized = serialize ($element);
		$elementBack = unserialize ($serialized);

		$this->assertEquals (strtolower (get_class ($elementBack->getRemoteObject ())), 'fooclass');

		$this->assertEquals (1, $elementBack->getParam (1));
		$this->assertEquals (array (1, 2), $elementBack->getArrayWith (1, 2));

		$elementBack->test = 2;
		$this->assertEquals ($elementBack->test, 2);
		$this->assertEquals ($elementBack->getRemoteObject ()->test, 2);

		$elementBack->notExists = 3;
		$this->assertEquals ($elementBack->notExists, 3);
		$this->assertEquals ($elementBack->getRemoteObject ()->notExists, 3);

		$elementBack->setPublicPropertyTest (4);
		$this->assertEquals ($elementBack->test, 4);
		$this->assertEquals ($elementBack->getRemoteObject ()->test, 4);
		$this->assertEquals ($elementBack->getPublicPropertyTest (), 4);
		$this->assertEquals ($elementBack->getRemoteObject ()->getPublicPropertyTest (), 4);

		$elementBack->setPrivatePropertyTest (5);
		$this->assertEquals ($elementBack->getPrivatePropertyTest (), 5);
		$this->assertEquals ($elementBack->getRemoteObject ()->getPrivatePropertyTest (), 5);

		$elementBack->setUnknownProperty ('notexists', 6);
		$this->assertEquals ($elementBack->getUnknownProperty ('notexists'), 6);
		$this->assertEquals ($elementBack->getRemoteObject ()->getUnknownProperty ('notexists'), 6);
		$this->assertEquals ($elementBack->notexists, 6);
		$this->assertEquals ($elementBack->getRemoteObject ()->notexists, 6);
	}

	public function testClassWithSelector (){
		$element = new CopixSerializableObject ($object = _ioClass ('fooclass'), 'test|fooclass');

		$this->assertEquals ($element->getRemoteObject (), $object);

		$serialized = serialize ($element);
		$elementBack = unserialize ($serialized);

		$this->assertEquals (strtolower (get_class ($elementBack->getRemoteObject ())), 'fooclass');

		$this->assertEquals (1, $elementBack->getParam (1));
		$this->assertEquals (array (1, 2), $elementBack->getArrayWith (1, 2));

		$elementBack->test = 2;
		$this->assertEquals ($elementBack->test, 2);
		$this->assertEquals ($elementBack->getRemoteObject ()->test, 2);

		$elementBack->notExists = 3;
		$this->assertEquals ($elementBack->notExists, 3);
		$this->assertEquals ($elementBack->getRemoteObject ()->notExists, 3);

		$elementBack->setPublicPropertyTest (4);
		$this->assertEquals ($elementBack->test, 4);
		$this->assertEquals ($elementBack->getRemoteObject ()->test, 4);
		$this->assertEquals ($elementBack->getPublicPropertyTest (), 4);
		$this->assertEquals ($elementBack->getRemoteObject ()->getPublicPropertyTest (), 4);

		$elementBack->setPrivatePropertyTest (5);
		$this->assertEquals ($elementBack->getPrivatePropertyTest (), 5);
		$this->assertEquals ($elementBack->getRemoteObject ()->getPrivatePropertyTest (), 5);

		$elementBack->setUnknownProperty ('notexists', 6);
		$this->assertEquals ($elementBack->getUnknownProperty ('notexists'), 6);
		$this->assertEquals ($elementBack->getRemoteObject ()->getUnknownProperty ('notexists'), 6);
		$this->assertEquals ($elementBack->notexists, 6);
		$this->assertEquals ($elementBack->getRemoteObject ()->notexists, 6);
	}

	public function testObject (){
		$element = new CopixSerializableObject ($object = _ioClass ('fooclass'), CopixModule::getPath ('test').'classes/fooclass.class.php');

		$this->assertEquals ($element->getRemoteObject (), $object);

		$serialized = serialize ($element);
		$elementBack = unserialize ($serialized);

		$this->assertEquals (strtolower (get_class ($elementBack->getRemoteObject ())), 'fooclass');

		$this->assertEquals (1, $elementBack->getParam (1));
		$this->assertEquals (array (1, 2), $elementBack->getArrayWith (1, 2));

		$elementBack->test = 2;
		$this->assertEquals ($elementBack->test, 2);
		$this->assertEquals ($elementBack->getRemoteObject ()->test, 2);

		$elementBack->notExists = 3;
		$this->assertEquals ($elementBack->notExists, 3);
		$this->assertEquals ($elementBack->getRemoteObject ()->notExists, 3);

		$elementBack->setPublicPropertyTest (4);
		$this->assertEquals ($elementBack->test, 4);
		$this->assertEquals ($elementBack->getRemoteObject ()->test, 4);
		$this->assertEquals ($elementBack->getPublicPropertyTest (), 4);
		$this->assertEquals ($elementBack->getRemoteObject ()->getPublicPropertyTest (), 4);

		$elementBack->setPrivatePropertyTest (5);
		$this->assertEquals ($elementBack->getPrivatePropertyTest (), 5);
		$this->assertEquals ($elementBack->getRemoteObject ()->getPrivatePropertyTest (), 5);

		$elementBack->setUnknownProperty ('notexists', 6);
		$this->assertEquals ($elementBack->getUnknownProperty ('notexists'), 6);
		$this->assertEquals ($elementBack->getRemoteObject ()->getUnknownProperty ('notexists'), 6);
		$this->assertEquals ($elementBack->notexists, 6);
		$this->assertEquals ($elementBack->getRemoteObject ()->notexists, 6);
	}
}
?>