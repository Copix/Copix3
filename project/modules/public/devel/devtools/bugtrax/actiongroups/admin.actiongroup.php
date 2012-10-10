<?php

class ActionGroupAdmin extends CopixActionGroup{

	private $canWrite;
	
	public function beforeAction(){
		//test les droits d'écriture
		$this->canWrite=_ioClass('bugauth')->canWriteAdmin();
		return $this->canWrite; 		
	}
	
	public function processDefault(){
		
		$ppo = new CopixPPO();
		
		$headings = _ioDao('bugtraxheadings')->findAll();
		$arHeadings = array();
		foreach ($headings as $head){
			$user = _ioClass('bugservices')->getDevelopperById($head->lead_bughead);
			$head->lead_bughead = $user->login_dbuser;
			$arHeadings[] = $head;
		}
		
		$headings = array();
		foreach($arHeadings as $heading){
			if(!isset($headings[$heading->heading_bughead.$heading->lead_bughead])){
				$headings[$heading->heading_bughead.$heading->lead_bughead] = new stdClass();
				$headings[$heading->heading_bughead.$heading->lead_bughead]->name = $heading->heading_bughead;
			}
			$headings[$heading->heading_bughead.$heading->lead_bughead]->headings[] = $heading;
		}
		ksort($headings);		
		$ppo->headings = $headings;
		$ppo->users = _ioClass('bugservices')->getDeveloppers();
		return _arPPO($ppo,'admin.tpl');
		
	}
	
	
	public function processAddHeading(){
		CopixRequest::assert('heading_bughead');
		CopixRequest::assert('version_bughead');
		CopixRequest::assert('lead_bughead');
		
		$heading = _record('bugtraxheadings');
		$heading->heading_bughead = _request('heading_bughead');
		$heading->version_bughead = _request('version_bughead');
		$heading->lead_bughead = _request('lead_bughead');

		_ioDao('bugtraxheadings')->insert($heading);
		return _arRedirect(_url('bugtrax|admin|'));
	}
	
}

?>