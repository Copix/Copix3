<?php
class CopixTest_CopixBayes extends CopixTest {
	
	public $bayes;
	public $bayesnodb;
	public function setUp (){
		_doQuery('delete from bayesian where dataset_bayes="CopixTests"');
		$this->bayes = _Class('bayes|bayes');
		$this->bayes->setDataMode("db","CopixTests");
		
		$this->bayesnodb = _Class('bayes|bayes');	
		
		//1er sac: 30 boules blanches, 10 boules noires
		$j=0;
		for ($i=0;$i<40;$i++){
			if($j<30){
				$this->bayes->train("sac1","blanche");
				$this->bayesnodb->train("sac1","blanche");
				$j++;
			}
			else{
				$this->bayes->train("sac1","noire");
				$this->bayesnodb->train("sac1","noire");
			}
		}

		//2eme sac: 20 boules blanches, 20 boules noires
		$j=0;
		for ($i=0;$i<40;$i++){
			if($j<20){
				$this->bayes->train("sac2","blanche");
				$this->bayesnodb->train("sac2","blanche");
				$j++;
			}
			else{
				$this->bayes->train("sac2","noire");
				$this->bayesnodb->train("sac2","noire");
			}
		}
	}
	
	public function tearDown (){
		_doQuery('delete from bayesian where dataset_bayes="CopixTests"');
	}

	//Exception test
	public function testValidationOnCreate (){
		try{
			$b = _Class('bayes|bayes');
			$b->setDataMode("db",""); //<-must raise exception
			$this->fail("No exception raised for empty dataset with db mode");
		}
		catch (Exception $expected){
			
		}
	}
	
	//With DB
	public function testSimpleProbaFromDB (){
		$this->assertEquals($this->bayes->getProba("blanche","sac1"),75);
		$this->assertEquals($this->bayes->getProba("blanche","sac2"),50);
	}
	
	public function testBayesianProbaFromDB (){
		$this->assertEquals($this->bayes->getBayes("sac1","blanche"),60);
		$this->assertEquals($this->bayes->getBayes("sac2","blanche"),40);
	}
	
	//Without DB
	public function testSimpleProbaWithoutDB (){
		$this->assertEquals($this->bayesnodb->getProba("blanche","sac1"),75);
		$this->assertEquals($this->bayesnodb->getProba("blanche","sac2"),50);
	}
	
	public function testBayesianProbaWithoutDB (){
		$this->assertEquals($this->bayesnodb->getBayes("sac1","blanche"),60);
		$this->assertEquals($this->bayesnodb->getBayes("sac2","blanche"),40);
	}
	
	//try zero
	public function testTryZero (){
		$bayeszero = _Class('bayes|bayes');
		$bayeszero->train('foo',"foo bar baz");
		$bayeszero->train('bar',"foo bar baz");
		$this->assertEquals($bayeszero->getBayes("bar","zero"),0);
	}

	public function testUnTrain (){
		//DB mode
		$bayeszero = _Class('bayes|bayes');
		$bayeszero->train('foo',"foo bar baz");
		$bayeszero->train('bar',"foo bar baz");
		$bayeszero->train('bar',"foo2 bar2 baz2");
		$bayeszero->untrain('bar',"foo2 bar2 baz2");
		$text = explode(" ","foo bar baz");
		
		$this->assertEquals(isset($bayeszero->categories['bar']->texts[1]),false);
		
		//check if we haven't erased the first data
		$this->assertEquals(isset($bayeszero->categories['bar']),true);
		$this->assertEquals($bayeszero->categories['bar']->texts[0],$text);
		
		//now, erasing every datas MUST erase category
		$bayeszero->untrain('bar',"foo bar baz");
		$this->assertEquals(isset($bayeszero->categories['bar']),false);
	}
	
	public function testUnTrainDB (){
		//DB mode
		$bayeszero = _Class('bayes|bayes');
		$bayeszero->setDataMode("db","CopixTests");
		$bayeszero->train('foo',"foo bar baz");
		$bayeszero->train('bar',"foo bar baz");
		$bayeszero->train('bar',"foo2 bar2 baz2");
		$find = ";foo2;bar2;baz2;";
		$bayeszero->untrain('bar',"foo2 bar2 baz2");
		$res = _ioDao('bayesian')->findBy(_daoSp()
								->addCondition('dataset_bayes','=','CopixTests')
								->addCondition('category_bayes','=','bar')
								->addCondition('datas_bayes','=',$find));
		$this->assertEquals(count($res),0);
		
		//check if we haven't erased the first data
		$find = ";foo;bar;baz;";
		$res = _ioDao('bayesian')->findBy(_daoSp()
								->addCondition('dataset_bayes','=','CopixTests')
								->addCondition('category_bayes','=','bar')
								->addCondition('datas_bayes','=',$find));
		$this->assertEquals(count($res),1);		

	}
	
	public function testSimpleMode (){
		$this->assertEquals($this->bayes->getBayes("sac1","blanche",true),60);
		$this->assertEquals($this->bayes->getBayes("sac2","blanche",true),40);
	}
	
}
?>