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
 * Test des classes de DAO
 * @package standard
 * @subpackage test
 */
class Test_CopixPPOTest extends CopixTest {
	public function testSetAndGet (){
		$ppo = new CopixPPO ();
		$ppo->value = '1';
		$this->assertEquals ($ppo->value, '1');
		$this->assertEquals (isset ($ppo->value), true);
		$this->assertEquals (isset ($ppo->notExists), false);
		$this->assertEquals ($ppo->notExists.'more', 'more');
	}

	public function testConstruct (){
		$ppo = new CopixPpo (array ('value'=>'test', 'value2'=>'test2'));
		$this->assertEquals ($ppo->value, 'test');
		$this->assertEquals ($ppo->value2, 'test2');
	}
	
	public function testArray (){
		$ppo = new CopixPpo ();
		$ppo->myArray['notExists'] = 'value';
		$this->assertEquals ($ppo->myArray['notExists'], 'value');

		$ppo = new CopixPpo ();
		$ppo->myArray[] = 'value';
		$ppo->myArray[] = 'value2';
		
		$this->assertEquals ($ppo->myArray[0], 'value');
		$this->assertEquals ($ppo->myArray[1], 'value2');
	}
	
	public function testObject (){
		$ppo = new CopixRPpo ();
		$ppo->notExists->element = 'test';
		$this->assertEquals ($ppo->notExists->element, 'test');
		
		$ppo->notExists->notExistsAgain->notExist = 'Wouhou';
		$this->assertEquals ($ppo->notExists->notExistsAgain->notExist, 'Wouhou');

		$this->assertEquals (isset ($ppo->foo), false);
		$this->assertEquals (isset ($ppo->foo->foo->foo), false);
	}

	public function testMix (){
		$ppo = new CopixRPpo ();
		$this->assertEquals (isset ($ppo->foo['test']->foo->foo['testing']->card), false);
		$this->assertTrue ($ppo->foo['test']->foo->foo['testing']->card instanceof CopixPpo);
		
		$ppo = new CopixRPpo ();
		$this->assertEquals (isset ($ppo['test']->foo->foo['testing']), false);
		$this->assertTrue ($ppo['test']->foo->foo['testing'] instanceof CopixPpo);
		
		$ppo = new CopixRPpo ();
		$this->assertEquals (isset ($ppo['test']->foo->foo['testing']->card), false);
		$this->assertTrue ($ppo['test']->foo->foo['testing']->card instanceof CopixPpo);
		
		$ppo = new CopixRPpo ();
		$ppo->foo->foo['testing'] = 1;
		$this->assertEquals ($ppo->foo->foo['testing'], 1);

		$ppo = new CopixRPpo ();
		$ppo['test']->foo->foo['testing'] = 1;
		$this->assertEquals ($ppo['test']->foo->foo['testing'], 1);

		$ppo = new CopixRPpo ();
		$ppo->foo->foo[] = 1;
		$this->assertEquals ($ppo->foo->foo[0], 1);

		$ppo = new CopixRPpo ();
		$ppo['test']->foo->foo['test'][] = 1;
		$this->assertEquals ($ppo['test']->foo->foo['test'][0], 1);
	}
	
	public function testUnset (){
		$ppo = new CopixPpo (array ('p3'=>'V3'));
		$ppo->p1 = 'V1';
		$ppo['p2'] = 'V2';
		
		$this->assertEquals ($ppo['p2'], 'V2');
		unset ($ppo['p2']);
		$this->assertNull ($ppo['p2']);
		
	}
	
	public function testIf (){
		$ppo = new CopixPpo ();
		$ppo->value = true;
		$this->assertTrue ($ppo->value);
		$this->assertNull ($ppo->noValue);
	}
	
	public function testGetObjectVars (){
		$ppo = new CopixPpo (array ('p3'=>'V3'));
		$ppo->p1 = 'V1';
		$ppo['p2'] = 'V2';
		$ppo->ar[] = 'value';
		
		$array = get_object_vars ($ppo);
		$this->assertEquals ($array['p1'], 'V1');
		$this->assertEquals ($array['p2'], 'V2');
		$this->assertEquals ($array['p3'], 'V3');
		$this->assertEquals ($array['ar'][0], 'value');
	}
	
	public function testRPPO (){
		$array = array ('p1'=>1, 'p2'=>2);
		$array2 = array ('p1'=>1, 'p2'=>2);
		$array['a1'] = $array2;
		
		$ppo = _rPPO ($array);
		$this->assertEquals ($array['p1'], $ppo->p1);
		$this->assertEquals ($array['p1'], $ppo['p1']);
		$this->assertEquals ($array['p2'], $ppo->p2);
		$this->assertEquals ($array['p2'], $ppo['p2']);
		$this->assertEquals ($array['a1']['p1'], $ppo->a1['p1']);
		$this->assertEquals ($array['a1']['p1'], $ppo['a1']['p1']);
	}
	
	public function testSaveIn (){
		//Avec des objets
		$class = new StdClass ();
		$class->p1 = 1;
		$class->p2 = 2;
		$class2 = clone ($class);
		
		$ppo = _ppo ($class);
		$ppo->p2 = 4;
		$ppo->p3 = 3;
		
		$ppo->saveIn ($class, false);
		
		$this->assertFalse (isset ($class->p3));
		$this->assertEquals ($class->p2, $ppo->p2);
		$this->assertEquals (strtolower (get_class ($class)), 'stdclass');
		
		$ppo->saveIn ($class2);
		$this->assertTrue (isset ($class2->p3));
		$this->assertEquals ($class2->p2, $ppo->p2);
		$this->assertEquals ($class2->p3, $ppo->p3);
		$this->assertEquals (strtolower (get_class ($class2)), 'stdclass');
		
		//Avec des tableaux		
		$array = array ('p1'=>1, 'p2'=>2);
		$array2 = $array;

		$ppo = _ppo ($array);
		$ppo->p2 = 4;
		$ppo->p3 = 3;

		$ppo->saveIn ($array, false);
		$this->assertTrue (is_array ($array));
		$this->assertFalse (isset ($array['p3']));
		$this->assertEquals ($array['p2'], $ppo->p2);
		
		$ppo->saveIn ($array2);
		$this->assertTrue (is_array ($array2));
		$this->assertTrue (isset ($array2['p3']));
		$this->assertEquals ($array2['p2'], $ppo->p2);
		$this->assertEquals ($array2['p3'], $ppo->p3);
	}
	
	public function _testSaveInNestedLevels (){
		//Avec des objets
		$class = new StdClass ();
		$class->p1 = 1;
		$class->p2 = 2;
		$class2 = clone ($class);
		$class->c1 = $class2;
		
		$ppo = _rPPO ();
		$ppo->p1 = 1;
		$ppo->p2 = 22;
		$ppo->p3 = 3;
		$ppo->c1->p1 = 11;
		$ppo->c1->p2 = 2;
		$ppo->c1->p3 = 33;

		$ppo->saveIn ($class, false);
		$this->assertFalse (isset ($class->p3));
		$this->assertEquals ($class->p1, $ppo->p1);
		$this->assertEquals ($class->p2, $ppo->p2);
		$this->assertEquals (strtolower (get_class ($class)), 'stdclass');
		$this->assertEquals (strtolower (get_class ($class->c1)), 'stdclass');
		$this->assertEquals ($class->c1->p1, 11);
		$this->assertEquals ($class->c1->p2, 2);
		$this->assertFalse (isset ($class->c1->p3));

		$ppo->saveIn ($class);
		$this->assertTrue (isset ($class->p3));
		$this->assertEquals ($class->p3, 3);
		$this->assertEquals ($class->p1, $ppo->p1);
		$this->assertEquals ($class->p2, $ppo->p2);
		$this->assertEquals (strtolower (get_class ($class)), 'stdclass');
		$this->assertEquals (strtolower (get_class ($class->c1)), 'stdclass');
		$this->assertEquals ($class->c1->p1, 11);
		$this->assertEquals ($class->c1->p2, 2);
		$this->assertTrue (isset ($class->c1->p3));
		$this->assertEquals ($class->c1->p3, 33);
		
		//Avec des tableaux		
		$array = array ('p1'=>1, 'p2'=>2);
		$array2 = array ('p1'=>1, 'p2'=>2);
		$array['a1'] = $array2;

		$ppo = _rPPO ($array);
		$ppo['p1']= 1;
		$ppo['p2'] = 2;
		$ppo['p3'] = 3;
		$ppo['a1']['p1'] = 11;
		$ppo['a1']['p2'] = 22;
		$ppo['a1']['p3'] = 33;
				
		$ppo->saveIn ($class, false);
		$this->assertFalse (isset ($array['p3']));
		$this->assertEquals ($array['p1'], 1);
		$this->assertEquals ($array['p2'], 2);
		$this->assertTrue (is_array ($array));
		$this->assertTrue (is_array ($array['a1']));
		$this->assertEquals ($array['a1']['p1'], 11);
		$this->assertEquals ($array['a1']['p2'], 22);
		$this->assertFalse (isset ($array['a1']['p3']));

		$ppo->saveIn ($class);
		$this->assertFalse (isset ($array['p3']));
		$this->assertEquals ($array['p1'], $ppo->p1);
		$this->assertEquals ($array['p2'], $ppo->p2);
		$this->assertEquals ($array['p3'], $ppo->p3);
		$this->assertEquals (is_array ($array));
		$this->assertEquals (is_array ($array['a1']));
		$this->assertEquals ($array['a1']['p1'], 11);
		$this->assertEquals ($array['a1']['p2'], 22);
		$this->assertTrue (isset ($array['a1']['p3']));
		$this->assertEquals ($array['a1']['p3'], 33);		
	}
}