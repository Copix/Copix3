<?php
class ZoneSelectStandardTemplate extends CopixZone {
    function _createContent (& $toReturn){
    	$tpl             = & new CopixTpl ();
    	$templateScanner = & CopixClassesFactory::getInstanceOf ('templatescanner');
    	
    	$templates = array ();
    	$moduleTemplates = $templateScanner->scanStandardTemplates ();
		
    	foreach ($moduleTemplates as $module=>$templates){
    		$moduleInformations = CopixModule::getInformations ($module);
    		$arTemplates[$moduleInformations->description] = $templates;
    	}

    	$tpl->assign ('arTemplate', $arTemplates);
    	$tpl->assign ('editId',     $this->getParam ('editId'));
		$tpl->assign ('modifiedTemplates', $templateScanner->getNonStandardTemplatesList (false));
		$tpl->assign ('newTemplates', $templateScanner->getNonStandardTemplatesList (true));
    	$toReturn = $tpl->fetch ('standardtemplate.select.tpl');
    	return true;
    }
}
?>
