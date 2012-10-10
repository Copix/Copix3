<?php
/** 
* @package		standard
* @subpackage	copixtest
*/ 

/** 
* @package		standard
* @subpackage	copixtest
*/ 
class CopixTest_CopixFormsTest extends CopixTest {
	function testForms (){
		$form = CopixFormFactory::get(null);
		$this->assertNotNull($form->getId());
		
		$form = CopixFormFactory::get('test');
		$this->assertEquals('test', $form->getId());
		
		$form = CopixFormFactory::get('test');
		$this->assertEquals('test', $form->getId());
		
		$form->setAction('test');
		$this->assertEquals('test', $form->getAction());
	}
	
	function testDAOForm() {
		$form = CopixFormFactory::get(null);
		$form->setDAOId('test');
		$this->assertEquals('test',$form->getDAOId());
		
		$form->setDAOId('test',array('testField'));
		$this->assertEquals('test',$form->getDAOId('testField'));
	}
	
	function testRecord() {
		$form = CopixFormFactory::get(null);
		$form->setRecord('test');
		$this->assertEquals('test',$form->getRecord());
		
		$form->setRecord('test',array('testField'));
		$this->assertEquals('test',$form->getRecord('testField'));
	}
	
	function testValidator() {
		CopixForm::addValidator('test',array('testField','testField'));
	}
	
	
	
}
?>