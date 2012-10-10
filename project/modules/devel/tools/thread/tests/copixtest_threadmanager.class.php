<?php
class CopixTest_ThreadManager extends CopixTest {

	private $manager;
	public function setUp (){
		
	}

	public function tearDown (){
		
	}
	
	
	//Exception test
	public function testCreateOneThread (){
		//$manager = _ioClass('thread|threadmanager');
		CopixContext::push("thread");
		$manager = _class('thread|threadmanager');
		$manager->emptyServersList();
		$url = _url('thread||');
		$url = str_replace('test.php','index.php',$url);
		$manager->addServer($url);
		$manager->add("thread|unitestthread",_ppo(array(
													'testbool'=>true,
													'teststring'=>"Foo Bar Baz",
												    'testnumeric'=>10.4
												)));
		//var_dump($manager);
		$response = $manager->execute();
		
		$response = $response[0];
		$this->assertEquals($response->testbool,true);
		$this->assertEquals($response->teststring,"Foo Bar Baz");
		$this->assertEquals($response->testnumeric,10.4);
		CopixContext::pop();
	}
	
	
	public function testCreateSeveralThreads (){
		//$manager = _ioClass('thread|threadmanager');
		CopixContext::push("thread");
		$manager = _class('thread|threadmanager');
		$manager->emptyServersList();
		$url = _url('thread||');
		$url = str_replace('test.php','index.php',$url);
		$manager->addServer($url);
		$manager->addServer("badsever");		
		
		$manager->add("thread|unitestthread",_ppo(array(
													'testbool'=>true,
													'teststring'=>"Foo Bar Baz",
												    'testnumeric'=>10.4
												)));
		
		//this thread have to crash
		$manager->add("thread|unitestthread",_ppo(array(
													'testbool'=>false,
													'teststring'=>"Aku dot",
												    'testnumeric'=>5
												)));
												
		$manager->add("thread|unitestthread",_ppo(array(
													'testbool'=>false,
													'teststring'=>"Aku dot",
												    'testnumeric'=>5
												)));
		
		$response = $manager->execute();

		//check reponses
		$responsesnumber = 0;
		foreach ($response as $res){
				if(isset($res->error)){
					$this->assertEquals($res->error,"Unable to connect to sever");
				}
				elseif($res->teststring=="Aku dot"){
					$this->assertEquals($res->testbool,false);
					$this->assertEquals($res->teststring,"Aku dot");
					$this->assertEquals($res->testnumeric,5);
				}
				else{	
					$this->assertEquals($res->testbool,true);
					$this->assertEquals($res->teststring,"Foo Bar Baz");
					$this->assertEquals($res->testnumeric,10.4);
				}			
				$responsesnumber++;
		}
		//check if number of reponse is 3
		$this->assertEquals($responsesnumber,3);
		
		CopixContext::pop();
	}
	

}
?>