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
 * Tests sur la classe CopixSessionObject
 * @package standard
 * @subpackage copixtest
 */
class CopixTest_CopixXMLSerializerTest extends CopixTest {
	
	public function setUp (){
		CopixContext::push ('copixtest');
	}
	public function tearDown (){
		CopixContext::pop ();
	}
	
	public function testNULL() {
		$this->_doTest(NULL);
	}
	
	public function testTRUE() {
		$this->_doTest(TRUE);
	}
	
	public function testFALSE() {
		$this->_doTest(FALSE);
	}
	
	public function testInteger() {
		$this->_doTest(0);
		$this->_doTest(1);
		$this->_doTest(-1);
	}

	public function testDouble() {
		$this->_doTest(0.0);
		$this->_doTest(1.0);
		$this->_doTest(-1.0);
		$this->_doTest(1.5);
		$this->_doTest(-1.5);
	}

	public function testDoubleRationale() {
		$this->_doTest(1.0/3.0);
		$this->_doTest(-1.0/3.0);
	}

	public function testDoubleBig() {
		$this->_doTest(0.33E+15);
		$this->_doTest(0.55E-15);
	}
	
	public function testStringASCII() {
		$this->_doTest('str');
		$this->_doTest(' a ');
		$this->_doTest(' b c ');
		$this->_doTest(":");
		$this->_doTest(";");
		$this->_doTest("\n");
		$this->_doTest("\r");
	}
	
	public function testStringZeroes() {
		$this->_doTest("\0\0");
		$this->_doTest("balbal\0truc");
	}

	public function testStringSpecials() {
		$this->_doTest("&amp;<class machin=\"trucmuche\"></class>");
	}
	
	public function testStringLATIN1() {
		$this->_doTest(utf8_decode('zéé'));
	}	
	
	public function testStringUTF8() {
		$this->_doTest('zéé');
	}
	
	public function testArrayEmpty() {
		$this->_doTest(array());
	}

	public function testArraySimple() {
		$this->_doTest(array(1, 5));
	}	

	public function testArrayNested() {
		$this->_doTest(array(1, array(4,5)));
	}	

	public function testArrayNestedWithRefs() {
		$a = array(8);
		$this->_doTest(array(1, &$a, 5, &$a));
	}	

	public function testArrayNestedWithCircularRefs() {
		$a1 = array(4);
		$a2 = array(5, &$a1);
		$a1[] =& $a2;
		$this->_doTest($a1);
	}

	public function testObjectSimple() {
		$o = new stdClass();
		$o->prop1 = 1;
		$o->prop2 = 2;
		$o->prop3 = array(5,6);
		$this->_doTest($o);
	}

	public function testObjectComposed() {
		$o = new stdClass();
		$o->prop1 = 1;
		$o->prop2 = new stdClass();
		$o->prop2->subProp1 = false;
		$this->_doTest($o);
	}

	public function testObjectRecursive() {
		$o1 = new stdClass();
		$o2 = new StdClass();
		$o1->ref = $o2;
		$o2->ref = $o1;
		$this->_doTest($o1);
	}
	
	/**
	 * Teste la linéarisation puis la délinéarisation d'une valeur.
	 *
	 * @param mixed $v Valeur à tester.
	 */
	private function _doTest($v, $_dump=false) {
		$xml = CopixXMLSerializer::serialize($v);
		$v2 =& CopixXMLSerializer::unserialize($xml);
		if($_dump) {
			var_dump(array_merge(compact('v', 'xml', 'v2'), array('ser(v)'=>serialize($v), 'ser(v2)'=>serialize($v2))));
		}
		$this->assertEquals(serialize($v), serialize($v2));
	}

		
}
?>