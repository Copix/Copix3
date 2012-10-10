<?php
/**
 * @package standard
 * @subpackage copixtest
 * @author		Guillaume Perréal, Gérald Croës
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

class CopixTest_CopixXMLSerializerTest extends CopixTest {
	
	public function setUp (){
		CopixContext::push ('copixtest');
		class_exists("CopixXMLSerializer", true); // Force le chargement de la classe (et donc du fichier)
		ob_start();
	}
	
	public function tearDown (){
		$c = trim(ob_get_contents());
		ob_end_clean();
		/*
		if($c) {
			$n = $this->getName();
			printf('<div style="background-color: white"><a name="%s" href="#%s"><h3>%s</h3></a><pre>%s</pre></div>', $n, $n, $n, $c);
		}
		*/
		CopixContext::pop ();
	}
	
	public function testMBStringOverloadSupport() {
		$overloaded = (extension_loaded("mbstring") && ((ini_get('mbstring.func_overload') & MB_OVERLOAD_STRING) == MB_OVERLOAD_STRING)) ? true : false;
		$i = CopixXMLSerializer::getInstance();
		
		if($overloaded) {
			$this->assertTrue($i instanceof CopixXMLSerializer_with_mb_overload);
		} else {
			$this->assertTrue($i instanceof CopixXMLSerializer_without_mb_overload);
		}
		
		$this->assertEquals(1, $i->strlen('a'), "CopixXMLSerializer::strlen('a') != 1");
		$this->assertEquals(1, $i->strlen("\xE9"), "CopixXMLSerializer::strlen('\xE9') != 1");
		$this->assertEquals(2, $i->strlen("\xC3\xA9"), "CopixXMLSerializer::strlen('\xC3\xA9') != 2");
		
		$this->assertEquals("b",   $i->substr("abcd", 1, 1));
		$this->assertEquals("bc",  $i->substr("abcd", 1, -1));
		$this->assertEquals("bcd", $i->substr("abcd", 1));

		$this->assertEquals("b",   $i->substr("\xE9bcd", 1, 1));
		$this->assertEquals("bc",  $i->substr("\xE9bcd", 1, -1));
		$this->assertEquals("bcd", $i->substr("\xE9bcd", 1));

		$this->assertEquals("\xA9",    $i->substr("\xC3\xA9bcd", 1, 1));
		$this->assertEquals("\xA9bc",  $i->substr("\xC3\xA9bcd", 1, -1));
		$this->assertEquals("\xA9bcd", $i->substr("\xC3\xA9bcd", 1));
		
		$this->assertEquals("\xA9",           $i->substr("\xC3\xA9b\xC3\xA9d", 1, 1));
		$this->assertEquals("\xA9b\xC3\xA9",  $i->substr("\xC3\xA9b\xC3\xA9d", 1, -1));
		$this->assertEquals("\xA9b\xC3\xA9d", $i->substr("\xC3\xA9b\xC3\xA9d", 1));		
		
		$this->assertEquals(0, $i->strpos("abcd", "a"));
		$this->assertEquals(1, $i->strpos("abcd", "b"));
		$this->assertEquals(1, $i->strpos("\xE9bcd", "b"));
		$this->assertEquals(1, $i->strpos("\xC3\xA9bcd", "\xA9"));
		$this->assertEquals(2, $i->strpos("\xC3\xA9bcd", "b"));
		
		$this->assertEquals(FALSE, $i->strpos("\xC3\xA9bcd", "\xA9", 3));
		$this->assertEquals(FALSE, $i->strpos("\xC3\xA9bcd", "b", 4));
		
		$this->assertEquals(FALSE, $i->strpos("\xE9bcd", "e"));
		
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
		$this->_doTest('cette chaine n\'existe pas');
		$this->_doTest('Cette proc&eacute;dure n\'existe pas');		
	}
	
	public function testStringZeroes() {
		$this->_doTest("\0\0");
		$this->_doTest("balbal\0truc");
	}

	public function testStringSpecials() {
		$this->_doTest("&amp;<class machin=\"trucmuche\"></class>");
	}
	
	public function testStringLATIN1() {
		$this->_doTest("eacute(LATIN1): \xE9"); // E9 = e accent aigu en ISO-8859-1
	}	

	public function testStringLATIN15() {
		$this->_doTest("euro(LATIN15): \xA4"); // A4 = euro en ISO-8859-15
	}	
	
	public function testStringCP1252() {
		$this->_doTest("euro(CP1252): \x80"); // 80 = euro en CP1252
	}	
	
	public function testStringUTF8() {
		$this->_doTest("eacute(UTF8): \xC3\xA9");  // C3A9 = e accent aigu en UTF8
		$this->_doTest("proc\xC3\xA9dure avec des \' et des &eacute;");
	}

	public function testStringUTF8Obj() {
		$obj = new StdClass ();
		$obj->testString = "ccedil(UTF8): \xC3\xA7"; // C3A7 = c cédille en UTF8
		$this->_doTest($obj);
	}

	public function testStringASCII02() {
		$this->_doTest("02 : \x02");
	}
	
	public function testVersion0() {
		// Encodage base64
		$xml = '<?xml version="1.0" encoding="UTF-8"?><data phpversion="5.2.4" type="string" encoding="base64">AAA=</data>';
		$this->assertEquals("\0\0", CopixXMLSerializer::unserialize($xml));
		
		// Double-encodage utf8
		$xml = '<?xml version="1.0" encoding="UTF-8"?><data phpversion="5.2.4" type="string">'.utf8_encode("\xC3\xA9").'</data>';
		$this->assertEquals("\xC3\xA9", CopixXMLSerializer::unserialize($xml));
				
		// Encodage UTF8
		$xml = '<?xml version="1.0" encoding="UTF-8"?><data phpversion="5.2.4" type="string">'.utf8_encode("\xE9").'</data>';
		$this->assertEquals("\xE9", CopixXMLSerializer::unserialize($xml));
	}
	
	public function testEncoding() {
		
		// Le XML lui-même est en ISO-8859-1, mais la chaîne doit sortir en UTF-8 car aucun sourceEncoding n'est précisé 
		$xml = '<?xml version="1.0" encoding="ISO-8859-1"?><data version="1" phpversion="5.2.4" type="string">'."\xE9".'</data>';
		$this->assertEquals("\xC3\xA9", CopixXMLSerializer::unserialize($xml));

		// Chaîne à l'origine en ISO-8859-1  
		$xml = '<?xml version="1.0" encoding="UTF-8"?><data version="1" phpversion="5.2.4" type="string" sourceEncoding="latin1">'."\xC3\xA9".'</data>';
		$this->assertEquals("\xE9", CopixXMLSerializer::unserialize($xml));

		if(extension_loaded("iconv") || extension_loaded("mbstring")) {
			// Chaîne à l'origine en CP1252 
			// \x80 = signe euro en CP1252
			// \xE2\x82\xAC  = signe euro en UTF-8
			$xml = '<?xml version="1.0" encoding="UTF-8"?><data version="1" phpversion="5.2.4" type="string" sourceEncoding="CP1252">'."\xE2\x82\xAC".'</data>';
			$this->assertEquals("\x80", CopixXMLSerializer::unserialize($xml));
		}
		
	}	
	public function testArrayEmpty() {
		$this->_doTest(array());
	}

	public function testArraySimple() {
		$this->_doTest(array(1, 5));
	}	

	public function testArrayEncodedKeys() {
		$this->_doTest(array(
			"a"        => "a",
			"\0"       => "\0",
			"\xE9"     => "\xE9",
			"\xC3\xA9" => "\xC3\xA9"
		));
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
		$_dump=true;
		try {
			$xml = CopixXMLSerializer::serialize($v);
			$v2 =& CopixXMLSerializer::unserialize($xml);
			if($_dump) {
				var_dump(serialize($v), $xml, serialize($v2));
			}
			$this->assertEquals(serialize($v), serialize($v2));
		} catch(Exception $e) {
			if($_dump) {
				if(isset($v)) var_dump($v, serialize($v));
				if(isset($xml)) var_dump($xml);
				if(isset($v2)) var_dump($v2, serialize($v2));
				var_dump($e);
			}
			$this->fail($e->getMessage());
		}
	}

		
}
?>