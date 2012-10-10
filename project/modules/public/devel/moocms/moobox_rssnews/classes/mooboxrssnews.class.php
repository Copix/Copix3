<?php
class MooboxRssNews extends MooBox {
	
	public function getContent($params){
		$rss = _ioClass('rss|reader')->read($params['url']);
		$tpl = new CopixTpl();
		$tpl->assign('rss', $rss);
		return $tpl->fetch("moobox_rssnews|lastnews.php");
	}
	
	public function getEdit(){
		$tpl= new CopixTpl();
		return $tpl->fetch("moobox_rssnews|edit.php");		
	}
	
}
?>