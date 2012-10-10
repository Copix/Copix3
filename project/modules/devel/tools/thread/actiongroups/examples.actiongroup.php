<?php
/**
 * Tests for thread system
 *
 */
class ActionGroupExamples extends CopixActionGroup {
	
	/**
	 * A simple test for threads, this send 3 threads with a random value to wait
	 * Threads response are displayed afeter processes
	 * 
	 * @return _arNone()
	 */
	function processDefault (){
		$manager = _ioClass('ThreadManager');
		
		
		for ($i=0;$i<3; $i++){
			$rands[] = $r = rand(1,5);
			$manager->add('threadtest',_ppo(array('wait'=>$r)));
		}
		$total1 = array_sum($rands);
		$t1 = time();
		$manager->execute();
		$t2 = time();
		$total2 = $t2 -$t1; 
		$ppo = new CopixPPO();
		
		$ppo->MAIN = nl2br(str_replace(" ","&nbsp;",var_export($manager->responses,true)));
		$ppo->MAIN .= "<br /><strong>Total time (sum of times)</strong>: $total1 seconds";
		$ppo->MAIN .= "<br /><strong>Threaded time (real spent time)</strong>: $total2 seconds";
		$ppo->MAIN .= "<br /><strong>Time diff</strong>: ".($total1 - $total2)." seconds";
		
		
		return _arPPO($ppo,"generictools|blank.tpl");		
	}
	
	/**
	 * Echo thread system, if thread work, the response 
	 * values are the same as sended values
	 *
	 * @return _arNone()
	 */
	function processTest2(){
		$manager = _ioClass('thread|threadmanager');
		
		$manager->add("thread|unitestthread",_ppo(array(
													'testbool'=>true,
													'teststring'=>"Foo Bar Baz",
												    'testnumeric'=>10.4
												)));
		$response = $manager->execute();
		$ppo->MAIN = nl2br(str_replace(" ","&nbsp;",var_export($response,true)));		
		return _arPPO($ppo,"generictools|blank.tpl");
	}
	
}
?>