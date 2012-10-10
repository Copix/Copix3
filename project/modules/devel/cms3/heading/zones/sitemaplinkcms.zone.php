<?php
class ZoneSiteMapLinkCms extends CopixZone {	
	function _createContent (& $toReturn) {
		$tpl = new CopixTpl();
		$tpl->assign('elements', $this->getParam('elements'));
		$toReturn = $tpl->fetch('sitemap/sitemaplinkcms.php');
	}	
}	