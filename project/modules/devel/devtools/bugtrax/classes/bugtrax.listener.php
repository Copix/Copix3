<?php
/**
 * 
 */

class ListenerBugtrax extends CopixListener {
	public function processContent ($pEvent, $pEventResponse){
		if($pEvent->getParam('kind')=="comment"){
			preg_match('/id_bug=(\d+)/',$pEvent->getParam('title'),$ids);
			$id = $ids[1];
			CopixLog::log("Ajout de commentaire sur le bug $id");		
		}
	} 
}
?>