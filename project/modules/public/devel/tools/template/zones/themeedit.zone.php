<?php
class ZoneThemeEdit extends CopixZone {
	function _createContent (& $toReturn){
		$tpl = & new CopixTpl ();
		$tpl->assign ('edited', $edited = $this->getParam ('edited'));
		$daoTheme =  & CopixDAOFactory::getInstanceOf ('copixtemplate_theme');
		if ($this->getParam ('showErrors', false) !== false){
			$tpl->assign ('errors', (($errors = $daoTheme->check ($edited)) === true) ? array () : $errors);
		}else{
			$tpl->assign ('errors', array ());
		}
		
		//New theme, scanning non enabled themes to import them into copix
		$arExistingThemes = array ();
		if ($edited->id_ctpt === null){
			$templateScanner = CopixClassesFactory::create ('TemplateScanner');
			$arExistingThemes = $templateScanner->getNonInstalledThemes ();
		}else{
			$dao = & CopixDAOFactory::getInstanceOf ('copixtemplate');
			$tpl->assign ('templateCount', $dao->countByTheme ($edited->id_ctpt));
		}
		$tpl->assign ('nonInstalledExistingTheme', $arExistingThemes);
		$dao = & CopixDAOFactory::getInstanceOf ('copixtemplate');
		$tpl->assign ('templateCount', $dao->countByTheme ($edited->id_ctpt));
		$toReturn = $tpl->fetch ('theme.edit.tpl');
		return true;
	}
}
?>