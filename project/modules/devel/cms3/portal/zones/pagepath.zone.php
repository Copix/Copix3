<?php

class ZonePagePath extends CopixZone {
	
	public function _createContent (&$toReturn){
		$tpl = new CopixTpl();
		
		$public_id = $this->getParam ("public_id");	
		$path = _ioClass('heading|HeadingElementInformationServices')->getHeadingPath ($public_id);
		
		$breadcrumb = array ();
		foreach ($path as $id => $value) {
			$breadcrumb[$value] = _ioClass('heading|HeadingElementInformationServices')->get ($value)->caption_hei; 
		}
		
		$tpl->assign('breadcrumb', array_reverse ($breadcrumb, true));
		$tpl->assign('caption_hei', $this->getParam('caption_hei', ""));
		
		$toReturn = $tpl->fetch ("pagepath.tpl");
	}
	
}