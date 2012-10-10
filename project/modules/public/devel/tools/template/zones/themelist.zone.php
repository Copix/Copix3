<?php
class ZoneThemeList extends CopixZone {
	function _createContent (&$toReturn){
		
		$templateBasePath = COPIX_PROJECT_PATH.'themes/';
		$arList = scandir($templateBasePath);
		foreach ($arList as $key=>$Dir) {
			if (!is_dir($templateBasePath.$Dir) || $Dir=='default' || !file_exists($templateBasePath.$Dir.'/theme.xml')) {
				unset($arList[$key]);
			}
		}
		
		$tpl = new CopixTpl ();
		$tpl->assign ('validUrl', $this->getParam ('validUrl'));
		$tpl->assign ('arTheme', $arList);
		$tpl->assign ('selectedTheme', $this->getParam ('selectedTheme', null));
		$toReturn =  $tpl->fetch ('theme.list.tpl');
		return true;
	}
}
?>