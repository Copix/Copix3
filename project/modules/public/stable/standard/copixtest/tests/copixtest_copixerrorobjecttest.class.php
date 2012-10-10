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
class CopixTest_CopixErrorObjectTest extends CopixTest {
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
}

?>