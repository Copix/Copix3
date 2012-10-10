<?php

/**
 * Zone de visualisation du média
 */
class ZoneMediaFormView extends CopixZone {
	
	public function _createContent (&$toReturn){
		$options = $this->getParam('options');
		$media = $this->getParam('media');
		$admin = $this->getParam('admin', false);
		$mediaType = $this->getParam('mediaType');
		$tpl = new CopixTpl ();
		$tpl->assign('media', $media);
		$params = new CopixParameterHandler();
		$params->setParams($options);
		$tpl->assign('options', $params);
		$tpl->assign('admin', $admin);
		$tpl->assign('width', $params->getParam ('x', '300'));
		$tpl->assign('height', $params->getParam ('y', '200'));
		$tpl->assign('identifiantFormulaire', $this->getParam ('identifiantFormulaire'));
		$tpl->assign ('include_media_code', $tpl->fetch('medias|includecode.' . $mediaType . '.php'));
		//Choix du template en fonction du type de média
		$tplName = $mediaType . 'formview.php';
		
		$toReturn = $tpl->fetch($tplName);
		return true;
	}
}
?>