<?php

class ActionGroupDefault extends CopixActionGroup{
	
	public function processShowPage(){
		
		$main = new CopixTpl();
		$main->assign('TITLE_PAGE',CopixRequest::get('title'));
		$main->assign('MAIN',_ioClass('moopage')->getPage(CopixRequest::get('title')));
		return _arDisplay($main);
		
		
	}
	
}

?>