<?php
/**
 * This is an example of a thread class which can use PPO parameters
 * This Thread will wait for $pParams->wait seconds and return a message
 * 
 */
class ThreadTest extends Thread {
	public function process($pParams=null){
		sleep($pParams->wait);
		$pParams->resp = "Finish after ".$pParams->wait.' seconds';
		return $pParams;
	}	
}

?>