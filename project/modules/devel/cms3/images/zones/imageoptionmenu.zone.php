<?php

class ZoneImageOptionMenu extends CopixZone {
	
	public function _createContent (&$toReturn){
		$identifiantFormulaire = $this->getParam ('identifiantFormulaire');
		$options = $this->getParam ('options');
		$position = $this->getParam ('position');
		$type = $this->getParam ('type', 'full');
		$params = new CopixParameterHandler ();
		$params->setParams ($options);
		
		$tpl = new CopixTpl ();
		$tpl->assign ('identifiantFormulaire', $identifiantFormulaire);
		$tpl->assign('image', $this->getParam ('image'));
		$tpl->assign ('options', $params);
		$tpl->assign ('position', $position);
		$tpl->assign ('portlet_id', $this->getParam ('portlet_id'));

		if($type == 'full'){
			$toReturn = $tpl->fetch ('imageoptionmenu.php');
		}else{
			$toReturn = $tpl->fetch ('imageoptionmenulite.php');
		}
		return true;
	}
}
?>