<?php


class ActionGroupDefault extends CopixActionGroup{

	public function processDefault(){
		$ppo = new CopixPPO;
		
		
		return _arPPO($ppo,"editor.php");
	}
	
}



?>