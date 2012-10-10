<?php

class ZoneHeadingElementPortletMenu extends CopixZone {
	
	public function _createContent (&$toReturn){	
		CopixHTMLHeader::addJSLink (_resource ('heading|js/portalgeneralmenu.js'));
		$edition = $this->getParam ('edition');
		$renderContext = $this->getParam ('renderContext');
		$actions = array ('savedraft', 'savepublish', 'saveplanned');

		if (!$edition) {
			$actions[] = array ('img' => 'img/tools/update.png', 'caption' => 'Informations', 'url' => _url ('adminportlet|edit', array ('editId' => _request ('editId'))));
		}
		if ($edition || $renderContext != RendererContext::UPDATED) {
			$actions[] = array ('img' => 'img/tools/update.png', 'caption' => 'Contenu', 'url' => _url ('adminportlet|DisplayPortlet', array ('editId' => _request ('editId'), 'etat'=>Portlet::UPDATED)));
		} 

		if ($renderContext == RendererContext::UPDATED) {
			$actions[] = array ('img' => 'img/tools/show.png', 'caption' => 'Aperçu', 'url' => _url ('portal|adminportlet|DisplayPortlet', array ('editId' => _request ('editId'), 'etat'=>Portlet::DISPLAYED)));
		}

		if (!$edition) {
			$actions[] = 'cancel';
			$showBack = false;
		} else {
			$showBack = true;
		}
	        if ($this->getParam('id_portlet')){	
			$element = _ioClass('heading|headingelementinformationservices')->getById($this->getParam('id_portlet'), 'portlet');
		} else {
			$element = null;
 		}
		$toReturn = CopixZone::process ('heading|headingelement/HeadingElementButtons', array ('form' => 'formPortlet', 'actions' => $actions, 'showBack' => $showBack, 'element'=>$element));
		return true;
	}
}
?>
