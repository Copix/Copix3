<?php

class ZoneRssReaderOptionMenu extends CopixZone {
	
	public function _createContent (&$toReturn) {		
		$tpl = new CopixTpl ();
		$tpl->assign ('portlet', $this->getParam ('portlet'));
        $toReturn = $tpl->fetch ('cms_rss_reader|rssreaderoptionmenu.php');
		return true;
	}
}
?>