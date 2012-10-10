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
 * Test des classes de DAO
 * @package standard
 * @subpackage copixtest
 */
class CopixTest_CopixPPOTest extends CopixTest {
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
		$ppo = new CopixPpo ();
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
		
		$array = get_object_vars ($ppo);
		$this->assertEquals ($array['p1'], 'V1');
		$this->assertEquals ($array['p2'], 'V2');
		$this->assertEquals ($array['p3'], 'V3');
	}
}
?>