<?php
class ChildTestParameterHandler extends CopixParameterHandler {
	public function __construct ($pParams){
		$this->setParams ($pParams);
	}
}

class Test_CopixParameterHandler extends CopixTest {
	public function testConstruct (){
		$parameters = array ('un'=>'valeur 1', 
		                     'deux'=>'valeur 2', 
		                     'trois'=>new StdClass (), 
		                     'quatre'=>null);

		$newClass = new ChildTestParameterHandler ($parameters);
		
		$this->assertEquals ($newClass->getParams (), $parameters);
		
		//on test tout un tas de chose pour voir si aucune exception n'est levée
		$newClass->assertParams ('un', 'deux', 'trois', 'quatre');
		$newClass->assertParams ('un');
		$newClass->assertParams ('deux');
		$newClass->assertParams ('trois');
		$newClass->assertParams ('quatre');
		
		//on test que l'on obtient bien une exception s'il manque un paramètre
		$this->setExpectedException ('CopixParameterHandlerMissingException');
		$newClass->assertParams ('un', 'deux', 'trois', 'quatre', 'cinq');
	}
	
	public function testRequire (){
		$parameters = array ('un'=>'valeur 1', 
		                     'deux'=>'valeur 2', 
		                     'trois'=>new StdClass (), 
		                     'quatre'=>null);

		$newClass = new ChildTestParameterHandler ($parameters);
		//récupération simple
		$this->assertEquals ($parameters['un'], $newClass->requireParam ('un'));
		$this->assertEquals ($parameters['deux'], $newClass->requireParam ('deux'));
		$this->assertEquals ($parameters['trois'], $newClass->requireParam ('trois'));
		$this->assertEquals ($parameters['quatre'], $newClass->requireParam ('quatre'));

		//récupération sous la forme de tableau
		$this->assertEquals (array ('un'=>$parameters['un'], 'trois'=>$parameters['trois']),
		                     $newClass->requireParam (array ('un', 'trois')));

		//exception en cas de paramètre non donné
		$this->setExpectedException ('CopixParameterHandlerMissingException');
		$newClass->requireParam ('cinq');
	}
	
	public function testRequire2 (){
		$parameters = array ('un'=>'valeur 1', 
		                     'deux'=>'valeur 2', 
		                     'trois'=>new StdClass (), 
		                     'quatre'=>null);

		$newClass = new ChildTestParameterHandler ($parameters);

		//exception en cas de paramètre en trop
		$this->setExpectedException ('CopixParameterHandlerMissingException');
		$newClass->requireParam (array ('quatre', 'cinq'));
	}
	
	public function testGet (){
		$parameters = array ('un'=>'valeur 1', 
		                     'deux'=>'valeur 2', 
		                     'trois'=>new StdClass (), 
		                     'quatre'=>null);

		$newClass = new ChildTestParameterHandler ($parameters);
		//récupération simple
		$this->assertEquals ($parameters['un'], $newClass->getParam ('un'));
		$this->assertEquals ($parameters['un'], $newClass->getParam ('un', null, _validator ('string')));
		$this->assertEquals ($parameters['deux'], $newClass->getParam ('deux', null, 'string'));
		$this->assertEquals ($parameters['trois'], $newClass->getParam ('trois', null, _validator ('object', array ('implements'=>'StdClass'))));
		$this->assertEquals ($parameters['quatre'], $newClass->getParam ('quatre'));

		//récupération sous la forme de tableau
		$this->assertEquals (array ('un'=>$parameters['un'], 'trois'=>$parameters['trois']),
		                     $newClass->requireParam (array ('un', 'trois')));

		//Vérifie que cela donne bien la valeur par défaut
		$this->assertNull ($newClass->getParam ('quatre', null, 'string', true));

		try {
			$newClass->getParam ('quatre', null, 'string');
			$this->assertTrue (false);//la ligne au dessus doit lever une exception
		}catch(CopixParameterHandlerValidationException $e){
		}

		//exception en cas de paramètre non donné
		$this->setExpectedException ('CopixParameterHandlerValidationException');
		$newClass->getParam ('quatre', null, _cValidator ()->attach (_validator ('string'))->attach (_validator ('notempty')));
	}
	
	public function testNotValidatorInThirdParameter (){
		$parameters = array ('un'=>'valeur 1', 
		                     'deux'=>'valeur 2', 
		                     'trois'=>new StdClass (), 
		                     'quatre'=>null);

		$newClass = new ChildTestParameterHandler ($parameters);

		$this->setExpectedException ('CopixException');
		$newClass->getParam ('un', null, new StdClass ());
	}
}