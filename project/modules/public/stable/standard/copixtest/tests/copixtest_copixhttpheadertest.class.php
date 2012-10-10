<?php
/** 
* @package		standard
* @subpackage	copixtest
*/ 

/** 
* @package		standard
* @subpackage	copixtest
*/ 
class CopixTest_CopixHTTPHeaderTest extends CopixTest {
	function test404 (){
		$this->assertContains ('HTTP/1.1 404 Not found',array_values(CopixHTTPHeader::get404()));
	}
	
	function test403 (){
		$this->assertEquals ('HTTP/1.1 403 Forbidden',CopixHTTPHeader::get403());
	}
}
?>