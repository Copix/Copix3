<?php

class ZoneDrafts extends CopixZone {
	
	public function _createContent (&$toReturn){
		$service = new HeadingElementInformationServices();
		$tpl = new CopixTpl();
		$tpl->assign('justTable', $this->getParam('justTable', false));
		$tpl->assign ('show', $this->getParam ('show', true));
		$selectedHeading = $this->getParam("heading", CopixUserPreferences::get('heading|dashboard|headingdraftoption', 0));
		$tpl->assign('selectedHeading', $selectedHeading);
		$tpl->assign('listeBrouillons', $service->find(array('status_hei'=>HeadingElementStatus::DRAFT, "hierarchy_hei"=>$selectedHeading)));
		$tpl->assign('elementsTypes', _ioClass ('HeadingElementType')->getList ());
		$toReturn = $tpl->fetch ("dashboard/drafts.php");
	}
	
}