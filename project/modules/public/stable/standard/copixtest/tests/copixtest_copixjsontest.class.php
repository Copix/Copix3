<?php
/**
 * @package		standard
 * @subpackage	copixtest
 * @author		Guillaume Perréal
 * @copyright	2001-2008 CopixTeam
 * @link			http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * @package standard
 * @subpackage copixtest
 */
class CopixTest_CopixJSONTest extends CopixTest {

	 public function testEncoder() {

		$this->assertEquals('1', CopixJSONEncoder::encode(1));
		$this->assertEquals('-1', CopixJSONEncoder::encode(-1));
		$this->assertEquals('true', CopixJSONEncoder::encode(true));
		$this->assertEquals('false', CopixJSONEncoder::encode(false));
		$this->assertEquals('null', CopixJSONEncoder::encode(null));
		$this->assertEquals('1.0', CopixJSONEncoder::encode(1.0));
		$this->assertEquals('-1.0', CopixJSONEncoder::encode(-1.0));

		$this->assertEquals('"abc"', CopixJSONEncoder::encode('abc'));
		$this->assertEquals('"a\nbc"', CopixJSONEncoder::encode("a\nbc"));
		$this->assertEquals('"a\tbc"', CopixJSONEncoder::encode("a\tbc"));
		$this->assertEquals('"a\rbc"', CopixJSONEncoder::encode("a\rbc"));
		$this->assertEquals('"a\fbc"', CopixJSONEncoder::encode("a\x0Cbc"));
		$this->assertEquals('"a\bbc"', CopixJSONEncoder::encode("a\x08bc"));
		$this->assertEquals('"a\"bc"', CopixJSONEncoder::encode("a\"bc"));
		$this->assertEquals('"a\/bc"', CopixJSONEncoder::encode("a/bc"));
		$this->assertEquals('"a\\\\bc"', CopixJSONEncoder::encode('a\bc'));
		$this->assertEquals('"aéc"', CopixJSONEncoder::encode('aéc'));
		$this->assertEquals('"a\u00E9c"', CopixJSONEncoder::encode("a\xE9c"));

		$this->assertEquals('[]', CopixJSONEncoder::encode(array()));
		$this->assertEquals('{}', CopixJSONEncoder::encode(new stdClass()));

		$this->assertEquals('[4,"a",true]', CopixJSONEncoder::encode(array(4,"a",true)));
		$this->assertEquals('{"b":4,"5":"X","c":true}', CopixJSONEncoder::encode(array("b"=>4,5=>"X","c"=>true)));

		$o = new stdClass();
		$o->b = 4;
		$o->c = "X";
		$o->d = true;
		$this->assertEquals('{"b":4,"c":"X","d":true}', CopixJSONEncoder::encode($o));

		$this->assertEquals('[4,[5,6],7,false]', CopixJSONEncoder::encode(array(4,array(5,6),7,false)));

		$o = new stdClass();
		$o->o = new stdClass();
		$o->a = array(5,6);
		$this->assertEquals('{"o":{},"a":[5,6]}', CopixJSONEncoder::encode($o));

		// Récursion infinie
		$o = new stdClass();
		$o->o = $o;
		try {
		CopixJSONEncoder::encode($o);
		$this->fail("CopixJSONEncoderException expected");
		} catch(CopixJSONEncoderException $e) {
		; // OK
		}

		// Donnée impossible à encoder
		$f = fopen(__FILE__, "r");
		try {
		CopixJSONEncoder::encode($f);
		fclose($f);
		$this->fail("CopixJSONEncoderException expected");
		} catch(CopixJSONEncoderException $e) {
		fclose($f);
		; // OK
		}

		}

	public function testDecoder() {
		
		$this->assertEquals('abc', COPIXJSONDecoder::decode('"abc"'));
		$this->assertEquals("*\nn*", COPIXJSONDecoder::decode('"*\nn*"'));
		$this->assertEquals("*\tt*", COPIXJSONDecoder::decode('"*\tt*"'));
		$this->assertEquals("*\rr*", COPIXJSONDecoder::decode('"*\rr*"'));
		$this->assertEquals("*\x0Cf*", COPIXJSONDecoder::decode('"*\ff*"'));
		$this->assertEquals("*\x08b*", COPIXJSONDecoder::decode('"*\bb*"'));
		$this->assertEquals("*\"DQUOTE*", COPIXJSONDecoder::decode('"*\"DQUOTE*"'));
		$this->assertEquals("*/SLASH*", COPIXJSONDecoder::decode('"*\/SLASH*"'));
		$this->assertEquals("*\\BACKSLASH*", COPIXJSONDecoder::decode('"*\\\\BACKSLASH*"'));
		$this->assertEquals('aéc', COPIXJSONDecoder::decode('"aéc"'));
		$this->assertEquals("a\xE9c", COPIXJSONDecoder::decode('"a\u00E9c"'));
		
		$this->assertEquals(1, COPIXJSONDecoder::decode('1'));
		$this->assertEquals(-1, COPIXJSONDecoder::decode('-1'));
		$this->assertEquals(true, COPIXJSONDecoder::decode('true'));
		$this->assertEquals(false, COPIXJSONDecoder::decode('false'));
		$this->assertEquals(null, COPIXJSONDecoder::decode('null'));
		$this->assertEquals(1.0, COPIXJSONDecoder::decode('1.0'));
		$this->assertEquals(-1.0, COPIXJSONDecoder::decode('-1.0'));

		$this->assertEquals(array(), COPIXJSONDecoder::decode('[]'));
		$this->assertEquals(new stdClass(), COPIXJSONDecoder::decode('{}'));
		
		$this->assertEquals(array(4,"a",true), COPIXJSONDecoder::decode('[4,"a",true]'));
		$this->assertEquals(array("b"=>4,5=>"X","c"=>true), COPIXJSONDecoder::decode('{"b":4,"5":"X","c":true}', true));
		
		$o = new stdClass();
		$o->a = null;
		$o->b = 5;
		$this->assertEquals($o, COPIXJSONDecoder::decode('{"a":null,"b":5}'));

	}

}
?>