<?php

class ZoneCMSActions extends CopixZone {
	
	public function _createContent (&$toReturn){
		$service = _ioClass ('HeadingActionsService');
		$tpl = new CopixTpl();
		$tpl->assign('justTable', $this->getParam('justTable', false));
		$selectedHeading = $this->getParam("heading", CopixUserPreferences::get('heading|dashboard|heading'.$this->getParam('id').'option', 0));
		$tpl->assign('selectedHeading', $selectedHeading);
		$tpl->assign('title', $this->getParam ('title', 'Historique'));
		$tpl->assign('id', $this->getParam('id'));
		$tpl->assign('createLog', !in_array ('cms_actions', CopixConfig::instance ()->copixlog_getRegistered ()));
		$tpl->assign('elementsTypes', _ioClass ('HeadingElementType')->getList ());
		$tpl->assign ('icon', $this->getParam ('icon', 'heading|img/actions/publish.png'));
		$tpl->assign ('link', $this->getParam ('link'));
		$options = _ppo();
		$options->hierarchy_hei = $selectedHeading;
		$options = $this->getParam("params",_ppo());
		$options->hierarchy_hei = $selectedHeading;
		$tpl->assign('logs', $service->search (ActionGroupActionsLogs::PROFILE, $options, 0, 5));
		$tpl->assign ('showMessage', $this->getParam ('showMessage', true));
		$tpl->assign ('show', $this->getParam ('show', true));
		$toReturn = $tpl->fetch("dashboard/cmsactions.php");
	}
	
}