<?php

class ZoneCmsWikiEditor extends CopixZone {
	
	public function _createContent (&$toReturn){
		$tpl = new CopixTpl ();
		
		$tpl->assign ('text', $this->getParam('value', $this->getParam('text')));
		$tpl->assign ('name', $this->getParam('name', 'text'));
		$tpl->assign ('style', $this->getParam('style', _class('cms_editor|CmsWikiStyle')->getList()));
		$tpl->assign ('height', $this->getParam('height', '250'));
		$tpl->assign ('preview', $this->getParam('preview', true));

		$tpl->assign ('iconImage', $this->getParam ('iconImage', true));
		$tpl->assign ('iconLink', $this->getParam ('iconLink', true));
		
		$toReturn = $tpl->fetch ('cmswikieditor.php');		
		return true;
	}
}
?>