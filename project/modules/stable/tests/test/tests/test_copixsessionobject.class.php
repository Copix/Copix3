<?php
/**
 * @package standard
 * @subpackage test
 * @author		Croës Gérald
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Tests sur la classe CopixSessionObject
 * @package standard
 * @subpackage test
 */
class Test_CopixSessionObject extends CopixTest {
	public function setUp (){
		CopixContext::push ('test');
	}
	public function tearDown (){
		CopixContext::pop ();
	}
	
	public function testCopixObject (){
		//on test que les objets autoloadés Copix ne soient pas pris en charge
		$element = new CopixSessionObject (CopixConfig::instance ());
		$serialized = serialize ($element);
		$elementBack = unserialize ($serialized);
		//si ça ne génère pas d'erreurs, c'est ok
	}
	
	public function testAutoloadedObject (){
		//Ici ce n'est pas vraiment un objet autoloadé, mais l'effet sera le même pour notre test.
		$element = new CopixSessionObject ($this);
		$serialized = serialize ($element);
		$elementBack = unserialize ($serialized);
	}
	
	public function testDAOAuto (){
		$element = new CopixSessionObject ($dao = _ioDAO ('testautodao'), 'testautodao');
		$element->findAll ();//appel d'une méthode pour vérifier le pont

		//On vérifie que l'élément est bien dedans et qu'il est identique
		$this->assertEquals ($element->getSessionObject (), $dao);
		
		$serialized = serialize ($element);
		$elementBack = unserialize ($serialized);
		
		$elementBack->findAll ();//vérifie que le contexte est toujours ok et qu'on peut manipuler le dao
	}
	
	public function testDAOXML (){
		$element = new CopixSessionObject ($dao = _ioDAO ('testmain'));
		$element->findAll ();//appel d'une méthode pour vérifier le pont

		//On vérifie que l'élément est bien dedans et qu'il est identique
		$this->assertEquals ($element->getSessionObject (), $dao);
		
		$serialized = serialize ($element);
		$elementBack = unserialize ($serialized);
		
		$elementBack->findAll ();//vérifie que le contexte est toujours ok et qu'on peut manipuler le dao

		//-- Même chose en spécifiant le qualificateur
		$element = new CopixSessionObject ($dao = _ioDAO ('testmain'), 'test|testmain');
		$element->findAll ();//appel d'une méthode pour vérifier le pont

		//On vérifie que l'élément est bien dedans et qu'il est identique
		$this->assertEquals ($element->getSessionObject (), $dao);
		
		$serialized = serialize ($element);
		$elementBack = unserialize ($serialized);
		
		$elementBack->findAll ();//vérifie que le contexte est toujours ok et qu'on peut manipuler le dao

	}
	
	public function testRecordDAOAuto (){
		$element = new CopixSessionObject ($record = _record ('testautodao'), 'testautodao');
		
		//On vérifie que l'élément est bien dedans et qu'il est identique
		$this->assertEquals ($element->getSessionObject (), $record);
		$serialized  = serialize ($element);
		$elementBack = unserialize ($serialized);
		
		$this->assertEquals (strtolower (get_class ($elementBack->getSessionObject ())), 'compileddaorecordtestautodao');
	}
	
	public function testRecordDAOXML (){
		$element = new CopixSessionObject ($record = _record ('testmain'));
		
		//On vérifie que l'élément est bien dedans et qu'il est identique
		$this->assertEquals ($element->getSessionObject (), $record);
		$serialized  = serialize ($element);
		$elementBack = unserialize ($serialized);
		
		$this->assertEquals (strtolower (get_class ($elementBack->getSessionObject ())), 'compileddaorecordtestmain');
		
		//--- Même chose en spécifiant le qualificateur
		$element = new CopixSessionObject ($record = _record ('testmain'), 'test|testmain');
		
		//On vérifie que l'élément est bien dedans et qu'il est identique
		$this->assertEquals ($element->getSessionObject (), $record);
		$serialized  = serialize ($element);
		$elementBack = unserialize ($serialized);
		
		$this->assertEquals (strtolower (get_class ($elementBack->getSessionObject ())), 'compileddaorecordtestmain');
	}

	public function testClass (){
		$element = new CopixSessionObject ($object = _ioClass ('fooclass'));
		
		$this->assertEquals ($element->getSessionObject (), $object);
		
		$serialized = serialize ($element);
		$elementBack = unserialize ($serialized);

		$this->assertEquals (strtolower (get_class ($elementBack->getSessionObject ())), 'fooclass');
		
		$this->assertEquals (1, $elementBack->getParam (1));
		$this->assertEquals (array (1, 2), $elementBack->getArrayWith (1, 2));
		
		$elementBack->test = 2;
		$this->assertEquals ($elementBack->test, 2);
		$this->assertEquals ($elementBack->getSessionObject ()->test, 2);
		
		$elementBack->notExists = 3;
		$this->assertEquals ($elementBack->notExists, 3);
		$this->assertEquals ($elementBack->getSessionObject ()->notExists, 3);
		
		$elementBack->setPublicPropertyTest (4);
		$this->assertEquals ($elementBack->test, 4);
		$this->assertEquals ($elementBack->getSessionObject ()->test, 4);
		$this->assertEquals ($elementBack->getPublicPropertyTest (), 4);
		$this->assertEquals ($elementBack->getSessionObject ()->getPublicPropertyTest (), 4);

		$elementBack->setPrivatePropertyTest (5);
		$this->assertEquals ($elementBack->getPrivatePropertyTest (), 5);
		$this->assertEquals ($elementBack->getSessionObject ()->getPrivatePropertyTest (), 5);
		
		$elementBack->setUnknownProperty ('notexists', 6);
		$this->assertEquals ($elementBack->getUnknownProperty ('notexists'), 6);
		$this->assertEquals ($elementBack->getSessionObject ()->getUnknownProperty ('notexists'), 6);
		$this->assertEquals ($elementBack->notexists, 6);
		$this->assertEquals ($elementBack->getSessionObject ()->notexists, 6);
	}

	public function testClassWithSelector (){
		$element = new CopixSessionObject ($object = _ioClass ('fooclass'), 'test|fooclass');
		
		$this->assertEquals ($element->getSessionObject (), $object);
		
		$serialized = serialize ($element);
		$elementBack = unserialize ($serialized);

		$this->assertEquals (strtolower (get_class ($elementBack->getSessionObject ())), 'fooclass');
		
		$this->assertEquals (1, $elementBack->getParam (1));
		$this->assertEquals (array (1, 2), $elementBack->getArrayWith (1, 2));
		
		$elementBack->test = 2;
		$this->assertEquals ($elementBack->test, 2);
		$this->assertEquals ($elementBack->getSessionObject ()->test, 2);
		
		$elementBack->notExists = 3;
		$this->assertEquals ($elementBack->notExists, 3);
		$this->assertEquals ($elementBack->getSessionObject ()->notExists, 3);
		
		$elementBack->setPublicPropertyTest (4);
		$this->assertEquals ($elementBack->test, 4);
		$this->assertEquals ($elementBack->getSessionObject ()->test, 4);
		$this->assertEquals ($elementBack->getPublicPropertyTest (), 4);
		$this->assertEquals ($elementBack->getSessionObject ()->getPublicPropertyTest (), 4);

		$elementBack->setPrivatePropertyTest (5);
		$this->assertEquals ($elementBack->getPrivatePropertyTest (), 5);
		$this->assertEquals ($elementBack->getSessionObject ()->getPrivatePropertyTest (), 5);
		
		$elementBack->setUnknownProperty ('notexists', 6);
		$this->assertEquals ($elementBack->getUnknownProperty ('notexists'), 6);
		$this->assertEquals ($elementBack->getSessionObject ()->getUnknownProperty ('notexists'), 6);
		$this->assertEquals ($elementBack->notexists, 6);
		$this->assertEquals ($elementBack->getSessionObject ()->notexists, 6);
	}
	
	public function testObject (){
		$element = new CopixSessionObject ($object = _ioClass ('fooclass'), CopixModule::getPath ('test').'classes/fooclass.class.php');
		
		$this->assertEquals ($element->getSessionObject (), $object);
		
		$serialized = serialize ($element);
		$elementBack = unserialize ($serialized);

		$this->assertEquals (strtolower (get_class ($elementBack->getSessionObject ())), 'fooclass');
		
		$this->assertEquals (1, $elementBack->getParam (1));
		$this->assertEquals (array (1, 2), $elementBack->getArrayWith (1, 2));
		
		$elementBack->test = 2;
		$this->assertEquals ($elementBack->test, 2);
		$this->assertEquals ($elementBack->getSessionObject ()->test, 2);
		
		$elementBack->notExists = 3;
		$this->assertEquals ($elementBack->notExists, 3);
		$this->assertEquals ($elementBack->getSessionObject ()->notExists, 3);
		
		$elementBack->setPublicPropertyTest (4);
		$this->assertEquals ($elementBack->test, 4);
		$this->assertEquals ($elementBack->getSessionObject ()->test, 4);
		$this->assertEquals ($elementBack->getPublicPropertyTest (), 4);
		$this->assertEquals ($elementBack->getSessionObject ()->getPublicPropertyTest (), 4);

		$elementBack->setPrivatePropertyTest (5);
		$this->assertEquals ($elementBack->getPrivatePropertyTest (), 5);
		$this->assertEquals ($elementBack->getSessionObject ()->getPrivatePropertyTest (), 5);
		
		$elementBack->setUnknownProperty ('notexists', 6);
		$this->assertEquals ($elementBack->getUnknownProperty ('notexists'), 6);
		$this->assertEquals ($elementBack->getSessionObject ()->getUnknownProperty ('notexists'), 6);
		$this->assertEquals ($elementBack->notexists, 6);
		$this->assertEquals ($elementBack->getSessionObject ()->notexists, 6);
	}	
}