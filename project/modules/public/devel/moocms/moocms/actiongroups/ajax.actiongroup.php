<?php

class ActionGroupAjax extends CopixActionGroup{
	public function processCodeEditor(){
		$language = CopixRequest::get('lang','html');
		
		$ppo = new CopixPPO();
		$tpl = new CopixTpl();
		$tpl->assign('lang',$language);
		$ppo->MAIN = $tpl->fetch('code.editor.php');
		return _arDirectPPO($ppo,'generictools|blank.tpl');
	}
}
?>