<?php
class ZoneSiteMapLink extends CopixZone {	
	function _createContent (& $toReturn) {
		$tpl = new CopixTpl();
		$tpl->assign('sitemapLink', $this->getParam('sitemapLink'));
		$tpl->assign('isRoot', $this->getParam('isRoot', false));
		$toReturn = $tpl->fetch('sitemap/sitemaplink.php');
	}	
}	