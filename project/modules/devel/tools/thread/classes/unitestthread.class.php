<?php
/**
 * Thread used for unit tests, return params given
 *
 */
class UnitestThread extends Thread {
	public function process($pParams=null){
		return $pParams;
	}
}
?>