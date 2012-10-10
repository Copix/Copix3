<?php

class ZoneRSS extends CopixZone {
	
	function _createContent(&$toReturn){
		
		$tpl = new CopixTpl;
		
		$sp = _daoSP();
		if($this->getParam('category')){
			$sp->addCondition('rss_category','=',$this->getParam('category'));
		}
		$res = _ioDAO('rss_feeds')->findBy($sp->orderBy('rss_pubdate'));
		
		foreach($res as $feed){
			$feed->rss_pubdate = date ('l, d F Y H:i:s',mktime(
												substr($feed->rss_pubdate,8,2),
												substr($feed->rss_pubdate,10,2),
												substr($feed->rss_pubdate,12,2),
												substr($feed->rss_pubdate,4,2),
												substr($feed->rss_pubdate,6,2),
												substr($feed->rss_pubdate,0,4)
												));
			
		}
		
		$tpl->assign('items',$res);
		$tpl->assign('title',"TEST");
		$tpl->assign('desc',"A new test");
		$tpl->assign('url',CopixUrl::getRequestedBaseUrl());
		$toReturn = $tpl->fetch('rss.xml.php');
		return true;
	}
	
}

?>