<?php

class ActionGroupCallByAjax extends CopixActionGroup{
	public function processGetZone(){
		CopixRequest::assert('zonename');
		$zone = CopixRequest::get('zonename');
		$params = CopixRequest::asArray();
		unset($params['zonename']);
		
		$ppo = new CopixPPO();
		$ppo->MAIN = CopixZone::process($zone,$params);
		return _arDirectPPO($ppo,'generictools|blank.tpl');		
	}	
}

?>