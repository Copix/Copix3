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
class Test_CopixErrorObjectTest extends CopixTest {
	function testConstruct (){
		$params = array ('name'=>'value', 'name2'=>'value2');
		$object = new CopixErrorObject ($params);
		
		$this->assertEquals (count ($params), $object->countErrors ());
		$this->assertEquals ($params, $object->asArray ());
		
		foreach ($params as $name=>$value){
			$this->assertTrue ($object->errorExists ($name));
			$this->assertEquals ($object->getError ($name), $value);
		}
		
		$this->assertTrue ($object->isError ());
		
		$asObject = $object->asObject ();
		foreach ($params as $name=>$value){
			$this->assertEquals ($asObject->$name, $value);
		}

	}

	function testEmpty (){
		$object = new CopixErrorObject ();
		$this->assertFalse ($object->isError ());
	}

	function testAsString (){
		$object = new CopixErrorObject ();
		$object->addErrors ('Test erreur');
		$this->assertEquals ('Test erreur', $object->asString ());
		
		$this->assertEquals ('Test erreur', _toString ($object));
	}
	
	function testAddErrors (){
		//ForceAppend === false
		$object = new CopixErrorObject (array (0=>'Erreur1', 1=>'Erreur2'));
		$object->addErrors (array (1=>'Erreur2'));
		$this->assertEquals (count ($object->asArray ()), 2);
		
		//ForceAppend = true
		$object->addErrors (array (1=>'Erreur3'), true);
		$this->assertEquals (count ($object->asArray ()), 3);
		
		//Ajout d'un errorObject (forceappend == false)
		$object2 = new CopixErrorObject (array (0=>'Erreur1', 1=>'Erreur2'));
		$object->addErrors ($object2);
		$this->assertEquals (count ($object->asArray ()), 3);
		
		//Ajout d'un errorObject (forceappend == true)
		$object2 = new CopixErrorObject (array (0=>'Erreur4', 1=>'Erreur5'));
		$object->addErrors ($object2, true);
		$this->assertEquals (count ($object->asArray ()), 5);
		
		//Ajout d'un StdClass (forceappend === false)
		$object = new CopixErrorObject (array ('e1'=>'Erreur1'));
		$errors = new StdClass ();
		$errors->e1 = 'Erreur 1';
		$object->addErrors ($errors);
		$this->assertEquals (count ($object->asArray ()), 1);

		//Ajout d'un StdClass (forceappend === true)
		$errors->e1 = 'Erreur 2';
		$object->addErrors ($errors, true);
		$this->assertEquals (count ($object->asArray ()), 2);
	}
	
	function testArrayAccess (){
		$object = new CopixErrorObject (array ('field'=>'erreur field', 'field 2'=>'erreur field 2'));
		$this->assertEquals ($object['field'], 'erreur field');
		$this->assertEquals ($object['field 2'], 'erreur field 2');
		
		$object['field'] = 'Erreur field modifiée';
		$this->assertEquals ($object['field'], 'Erreur field modifiée');
		
		$this->assertEquals (count ($object), 2);

		$this->assertFalse (isset ($object['newError']));
		$object['newError'] = 'Autre erreur';
		$this->assertEquals (count ($object), 3);
		$this->assertEquals ($object['newError'], 'Autre erreur');
		$this->assertTrue (isset ($object['newError']));

		unset ($object['newError']);
		$this->assertEquals ($object['newError'], null);
	}
	
	function testIterator (){
		$values = array (1=>'premier', 'champ'=>'valeur', 'autre'=>'troisième', 'dernier'=>4);

		//On vérifie que l'objet est égal au tableau
		$object = new CopixErrorObject (array (1=>'premier', 'champ'=>'valeur', 'autre'=>'troisième', 'dernier'=>4));
		$tested = array ();
		foreach ($object as $key=>$value){
			$this->assertEquals ($value, $values[$key]);//value est dans le tableau d'origine à la même clef
			$this->assertTrue (in_array ($key, array_keys ($values)));//la clef est dans le tableau d'origine
			$this->assertEquals ($value, $object[$key]);//ArrayAccess au passage

			$tested[] = $key;
		}
		$this->assertEquals (count ($tested), 4);
		$this->assertTrue (in_array (1, $tested));
		$this->assertTrue (in_array ('champ', $tested));
		$this->assertTrue (in_array ('autre', $tested));
		$this->assertTrue (in_array ('dernier', $tested));
		
		//une deuxème fois pour être sûr
		$tested = array ();
		foreach ($object as $key=>$value){
			$this->assertEquals ($value, $values[$key]);//value est dans le tableau d'origine à la même clef
			$this->assertTrue (in_array ($key, array_keys ($values)));//la clef est dans le tableau d'origine
			$this->assertEquals ($value, $object[$key]);//ArrayAccess au passage

			$tested[] = $key;
		}
		$this->assertEquals (count ($tested), 4);
		$this->assertTrue (in_array (1, $tested));
		$this->assertTrue (in_array ('champ', $tested));
		$this->assertTrue (in_array ('autre', $tested));
		$this->assertTrue (in_array ('dernier', $tested));

	}
}
?>