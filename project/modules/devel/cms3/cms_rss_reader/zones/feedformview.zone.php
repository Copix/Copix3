<?php

class ZoneFeedFormView extends CopixZone {
	
	public function _createContent (&$toReturn) {
		$feed = $this->getParam('feed');
		
		$tpl = new CopixTpl ();
		$tpl->assign('feed', $feed);

		$toReturn = $tpl->fetch ('cms_rss_reader|feedformview.php');
		return true;
	}
}
?>