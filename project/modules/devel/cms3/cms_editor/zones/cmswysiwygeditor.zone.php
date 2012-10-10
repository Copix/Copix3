<?php

class ZoneCmsWysiwygEditor extends CopixZone {
	
	public function _createContent (&$toReturn){
		//dans le cas de plusieurs domaine pour le chargement des resources
		$config = CopixConfig::instance ();
		$config->copixresource_removeServers ();
		
		$tpl = new CopixTpl ();

		$tpl->assign('theme', $this->getParam('theme', CopixTpl::getTheme()));
		$tpl->assign ('text', $this->getParam('value', $this->getParam('text')));
		$tpl->assign ('name', $this->getParam('name', 'text'));
		$tpl->assign ('height', $this->getParam('height', '250'));
		$tpl->assign ('preview', $this->getParam('preview', true));
		
		$toReturn = $tpl->fetch ('cmswysiwygeditor.php');		
		return true;
	}
}
?>