<?php
/** 
* @package		standard
* @subpackage	test
*/ 

/** 
* @package		standard
* @subpackage	test
*/ 
class Test_CopixHTTPHeaderTest extends CopixTest {
	function test404 (){
		$this->assertContains ('HTTP/1.1 404 Not found',array_values(CopixHTTPHeader::get404()));
	}
	
	function test403 (){
		$this->assertContains ('HTTP/1.1 403 Forbidden',array_values(CopixHTTPHeader::get403()));
	}
}