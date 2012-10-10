<?php

class ZonePortletOptionMenu extends CopixZone {
	
	public function _createContent (&$toReturn){
		$identifiantFormulaire = $this->getParam ('identifiantFormulaire');
		$options = $this->getParam ('options');	
		
		$params = new CopixParameterHandler ();
		$params->setParams ($options);
		
		$tpl = new CopixTpl ();
		$tpl->assign ('identifiantFormulaire', $identifiantFormulaire);
		$tpl->assign ('options', $params);
		$tpl->assign ('portlet_id', $this->getParam ('portlet_id'));

		$toReturn = $tpl->fetch ($this->getParam('template'));
		return true;
	}
}
?>