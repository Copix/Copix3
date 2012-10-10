<?php

class ZoneDocumentOptionMenu extends CopixZone {
	
	public function _createContent (&$toReturn){
		$identifiantFormulaire = $this->getParam ('identifiantFormulaire');
		$options = $this->getParam ('options');
		$position = $this->getParam ('position');		
		
		$params = new CopixParameterHandler ();
		$params->setParams ($options);
		
		$tpl = new CopixTpl ();
		$tpl->assign ('identifiantFormulaire', $identifiantFormulaire);
		$tpl->assign ('options', $params);
		$tpl->assign ('position', $position);
		$tpl->assign ('portlet_id', $this->getParam ('portlet_id'));

		$toReturn = $tpl->fetch ('documentoptionmenu.php');
		return true;
	}
}
?>