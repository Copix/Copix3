<?php

class ActionGroupDefault extends CopixActionGroup {
	
	private $canWrite;
	private $canRead;
	private $canWriteAdmin;
	
	public function beforeAction(){
		//test les droits d'écriture
		$this->canWrite=_ioClass('bugauth')->canWrite();
		$this->canRead=_ioClass('bugauth')->canRead();
		$this->canWriteAdmin=_ioClass('bugauth')->canWriteAdmin();
		return $this->canRead; 		
	}
	
	public function processDefault(){
		$ppo = new CopixPPO();
		$bugs = _ioDao('bugtrax')->findBy(_daoSp()->orderBy('modificationdate_bug'));
		$colors = _ioClass('bugservices')->assignColors();
		$arBugs = array();
		foreach($bugs as $bug){
			$headings = _ioDao('bugtraxheadings')->findBy(_daoSp()
														->addCondition('id_bughead','=',$bug->id_bughead)
														);
			$bug->heading=$headings[0];
			$bug->color = $colors[$bug->severity_bug];
			$this->_setDates($bug);
			$arBugs[]=$bug;
		}
		$bugs = &$arBugs;
		$ppo->bugs = $bugs;
		
		return _arPPO($ppo,"index.tpl");		
	}
	
	public function processNewBug(){
		
		$ppo = new CopixPPO();
		$headings = _ioDao('bugtraxheadings')->findAll();
		$h =  _ioDao('bugtraxheadings')->findBy(_daoSp()
												  ->addCondition('heading_bughead','!=','pouet')
												  ->groupBy('heading_bughead')
												  );
		$ppo->headings = $headings;
		$ppo->list = $h;
		$ppo->author = $user = CopixAuth::getCurrentUser()->getLogin();
		$ppo->severities = explode(";",CopixConfig::get('bugtrax|severity'));
		$ppo->states= explode(";",CopixConfig::get('bugtrax|states'));
		
		return _arPPO($ppo,"newbug.tpl");		
	}
	
	public function processAdd(){
		CopixRequest::assert('heading_bughead',
							 'version_bughead',
							 'description_bug',
							 'severity_bug',
							 'author_bug',
							 'name_bug');
							 
        $heading = _ioDao('bugtraxheadings')->findBy(_daoSp()->startGroup()
                                                     ->addCondition('heading_bughead','=',_request('heading_bughead'))
                                                     ->addCondition('version_bughead','=',_request('version_bughead'))
                                                     ->endGroup()
                                                     );				 
							 

        $bug = _record('bugtrax');
		$bug->id_bughead = $heading[0]->id_bughead;
		$bug->name_bug= _request('name_bug');
		$bug->description_bug= _request('description_bug');
		$bug->author_bug= _request('author_bug');
		$bug->severity_bug = _request('severity_bug');
		$bug->state_bug = 'open';
		$bug->date_bug = date('YmdHis');
		$bug->modificationdate_bug = date('YmdHis');
		
		_ioDao('bugtrax')->insert($bug);
		
		return _arRedirect(_url('bugtrax|default|default'));
	}
	
	public function processShowBug(){
		CopixRequest::assert('id_bug');		
		$bug = _ioDao('bugtrax')->get(_request('id_bug'));
		$this->_setDates($bug);
		$ppo = new CopixPPO();
		$ppo->bug=$bug;
		$ppo->TITLE_PAGE = $bug->name_bug;
		$ppo->canWriteAdmin = $this->canWriteAdmin;
		return _arPPO($ppo,"showbug.tpl");
	}
	
	
	private function _setDates($bug){
		$bug->date_bug = CopixDateTime::yyyymmddhhiissToDateTime($bug->date_bug);
		$bug->modificationdate_bug = CopixDateTime::yyyymmddhhiissToDateTime($bug->modificationdate_bug);
	}
}

?>