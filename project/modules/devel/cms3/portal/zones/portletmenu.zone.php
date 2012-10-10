<?php

class ZonePortletMenu extends CopixZone {
	
	public function _createContent (&$toReturn){
		$tpl = new CopixTpl ();
		$portlet = $this->getParam ('portlet');
		
		$tpl->assign ('display', $this->getParam ('display') ? 'block' : 'none');
        $xmlPath = $this->getParam ('xmlPath', CopixTpl::getFilePath($this->getParam ('module')."|portlettemplates/portlettemplates.xml"));
		$tpl->assign ('xmlPath', $xmlPath);
		$tpl->assign ('templateNb', _class('portal|templateservices')->getTemplateNb($xmlPath));

		$tpl->assign ('portletRandomId', $portlet->getRandomId ());
		$tpl->assign ('template', $portlet->getOption ('template'));
		$tpl->assign ('module', $this->getParam ('module'));

		$toReturn = $tpl->fetch ('portletmenu.php');		
		return true;
	}
}
?>