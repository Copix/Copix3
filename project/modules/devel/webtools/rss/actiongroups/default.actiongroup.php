<?php
class ActionGroupDefault extends CopixActionGroup{
	
	public function processDefault(){
		 $ppo = new CopixPPO();
		 $ppo->MAIN = CopixZone::process('RSS',array('category'=>CopixRequest::get('category'),'title'=>CopixRequest::get('title')));
		 return _arDirectPPO($ppo,'generictools|blank.tpl');
	}
	
}
?>