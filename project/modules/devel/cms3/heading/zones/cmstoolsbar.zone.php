<?php

class ZoneCmsToolsBar extends CopixZone {
	
	public function _createContent (&$toReturn){
		CopixHTMLHeader::addCSSLink(_resource('heading|css/cms.css'));
		CopixHTMLHeader::addJSLink (_resource ('portal|js/tools.js'));
		$tpl = new CopixTpl();
		
		$elementsInPage = array();
		if ($this->getParam ("page_id", false)){
			$page = _class('portal|pageservices')->getByPublicId ($this->getParam ("page_id"));
			try{
				$elementsInPage = $page->getListElementsInPage();
			} catch (CopixException $e){
			}
			$tpl->assign ('listeElements', array_merge(array($page), $elementsInPage));
			$tpl->assign ('page', $page);
		} 
		$tpl->assign ('displayedElements', $this->getParam("displayedElements"));
		$tpl->assign ('parent', isset($page) ? _ioClass('heading|headingelementinformationservices')->get($page->parent_heading_public_id_hei) : 0);
		$tpl->assign ('listeIcones', _class ('heading|HeadingElementType')->getList ());
		$toReturn = $tpl->fetch ("cmstoolsbar.php");
	}
	
}