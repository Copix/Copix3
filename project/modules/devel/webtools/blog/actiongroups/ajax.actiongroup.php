<?php
class ActionGroupAjax extends CopixActionGroup {

	public function processGetEditor(){

		$mode = _request('mode','wiki');
		$ppo = new CopixPPO();
		$ppo->content="";
		
		$ticket = _record('blog_ticket');
		
		if(_request("id",0)>0){
			$ticket = _ioDAO('blog_ticket')->get(_request("id"));
			$ppo->content = $ticket->content_blog;
		}
		
		
		if(_request('content',false)){
				$ppo->content = _request('content'); 
		}
		/*				
		if($ticket->typesource_blog=="wiki" && $mode=="html"){
			//process html
			$ppo->content = _ioClass("wikirender|wiki")->render($ppo->content);
		}else if($ticket->typesource_blog=="html" && $mode=="wiki"){
			//inverse... as best as we can...
			$ppo->content = _ioClass("wikirender|wiki")->reverse($ppo->content);
		}*/
		
		
		$ppo->mode = $mode;
		return _arDirectPPO($ppo,"editor.tpl");
	}

}
?>