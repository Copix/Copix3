<?php
/**
 * Trackback Default Actiongroup
 * Based on http://silent-strength.com/?articles/php/trackbacks
 * @author Patrice Ferlet - <metal3d@copix.org>
 * @package webtools
 * @subpackage trackback
 * @copyright Copix Team (c) 2007-2008
 */

class ActionGroupDefault extends CopixActionGroup{
	
	public function processSend (){
		$ppo = new CopixPPO();
		$ppo->TITLE_PAGE = $ppo->TITLE_BAR = "TrackBack";  
		$ppo->response = _ioClass('trackback|trackback')->send();
		return _arPPO($ppo,"sended.tpl");
	}
	
	public function processSendForm (){
		$ppo = new CopixPPO();
		$ppo->TITLE_PAGE = $ppo->TITLE_BAR = "TrackBack"; 
		
		$ppo->title = _request('title');
		$ppo->excerpt = str_pad(trim(strip_tags(_request('excerpt'))),250);
		$ppo->url = _request('url');
		$ppo->blog_name = _request('blogname');
		
		return _arPPO($ppo,"send.form.tpl");
	}
	
	public function processTb (){
		$ppo = new CopixPPO();
		$ppo->MAIN = _ioClass('trackback')->recieve()->message;
		return _arDirectPPO($ppo,'generictools|blank.tpl');
	}
}

?>