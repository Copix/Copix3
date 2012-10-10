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
 * Tests sur la classe CopixCookieObject
 * @package standard
 * @subpackage copixtest
 */
class CopixTest_CopixCookieObject extends CopixTest {
	public function setUp (){
		CopixContext::push ('copixtest');
	}
	public function tearDown (){
		CopixContext::pop ();
	}
	
	public function testCopixObject (){
		//on test que les objets autoloadés Copix ne soient pas pris en charge
		$element = new CopixCookieObject (CopixConfig::instance ());
		$serialized = serialize ($element);
		$elementBack = unserialize ($serialized);
		//si ça ne génère pas d'erreurs, c'est ok
	}
	
	public function testAutoloadedObject (){
		//Ici ce n'est pas vraiment un objet autoloadé, mais l'effet sera le même pour notre test.
		$element = new CopixCookieObject ($this, CopixCookieObject::AUTOLOADED);
		$serialized = serialize ($element);
		$elementBack = unserialize ($serialized);
	}
	
	public function testDAOAuto (){
		$element = new CopixCookieObject ($dao = _ioDAO ('copixtestautodao'), 'copixtestautodao');
		$element->findAll ();//appel d'une méthode pour vérifier le pont

		//On vérifie que l'élément est bien dedans et qu'il est identique
		$this->assertEquals ($element->getRemoteObject (), $dao);
		
		$serialized = serialize ($element);
		$elementBack = unserialize ($serialized);
		
		$elementBack->findAll ();//vérifie que le contexte est toujours ok et qu'on peut manipuler le dao
	}
	
	public function testDAOXML (){
		$element = new CopixCookieObject ($dao = _ioDAO ('copixtestmain'));
		$element->findAll ();//appel d'une méthode pour vérifier le pont

		//On vérifie que l'élément est bien dedans et qu'il est identique
		$this->assertEquals ($element->getRemoteObject (), $dao);
		
		$serialized = serialize ($element);
		$elementBack = unserialize ($serialized);
		
		$elementBack->findAll ();//vérifie que le contexte est toujours ok et qu'on peut manipuler le dao

		//-- Même chose en spécifiant le qualificateur
		$element = new CopixCookieObject ($dao = _ioDAO ('copixtestmain'), 'copixtest|copixtestmain');
		$element->findAll ();//appel d'une méthode pour vérifier le pont

		//On vérifie que l'élément est bien dedans et qu'il est identique
		$this->assertEquals ($element->getRemoteObject (), $dao);
		
		$serialized = serialize ($element);
		$elementBack = unserialize ($serialized);
		
		$elementBack->findAll ();//vérifie que le contexte est toujours ok et qu'on peut manipuler le dao

	}
	
	public function testRecordDAOAuto (){
		$element = new CopixCookieObject ($record = _record ('copixtestautodao'), 'copixtestautodao');
		
		//On vérifie que l'élément est bien dedans et qu'il est identique
		$this->assertEquals ($element->getRemoteObject (), $record);
		$serialized  = serialize ($element);
		$elementBack = unserialize ($serialized);
		
		$this->assertEquals (strtolower (get_class ($elementBack->getRemoteObject ())), 'compileddaorecordcopixtestautodao');
	}
	
	public function testRecordDAOXML (){
		$element = new CopixCookieObject ($record = _record ('copixtestmain'));
		
		//On vérifie que l'élément est bien dedans et qu'il est identique
		$this->assertEquals ($element->getRemoteObject (), $record);
		$serialized  = serialize ($element);
		$elementBack = unserialize ($serialized);
		
		$this->assertEquals (strtolower (get_class ($elementBack->getRemoteObject ())), 'compileddaorecordcopixtestmain');
		
		//--- Même chose en spécifiant le qualificateur
		$element = new CopixCookieObject ($record = _record ('copixtestmain'), 'copixtest|copixtestmain');
		
		//On vérifie que l'élément est bien dedans et qu'il est identique
		$this->assertEquals ($element->getRemoteObject (), $record);
		$serialized  = serialize ($element);
		$elementBack = unserialize ($serialized);
		
		$this->assertEquals (strtolower (get_class ($elementBack->getRemoteObject ())), 'compileddaorecordcopixtestmain');
	}

	public function testClass (){
		$element = new CopixCookieObject ($object = _ioClass ('fooclass'));
		
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
		$element = new CopixCookieObject ($object = _ioClass ('fooclass'), 'copixtest|fooclass');
		
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
		$element = new CopixCookieObject ($object = _ioClass ('fooclass'), CopixModule::getPath ('copixtest').'classes/fooclass.class.php');
		
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