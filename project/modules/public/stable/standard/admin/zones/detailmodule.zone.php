<?php

class ZoneDetailModule extends CopixZone {
	function _createContent (& $toReturn){
    	$moduleName = CopixZone::getParam('moduleName');
    	
    	$infos = CopixModule::getInformations($moduleName);
        
        $tpl = new CopixTpl();
    	
    	if (in_array($moduleName,CopixModule::getList())) {
            $arModule = CopixModule::getDependenciesForDelete($moduleName);
    	    $template = 'detailmoduledelete.tpl';
    	    $record = _dao('Copix:copixmodule')->get($moduleName);
    	    $tpl->assign('version',$record->version_cpm);
    	} else {
            $arDependencies = CopixModule::getDependenciesForInstall($moduleName);
            $arModule = array();
            $arExtension = array();
            $install = true;
            foreach ($arDependencies as $key=>$dependency) {
                if ($dependency->kind === 'module') {
                    if (CopixModule::testDependency($dependency)) {
                        $dependency->ok = true;
                        $arModule[] = $dependency;
                    } else {
                        $dependency->ok = false;
                        $install = false;
                        $arModule[] = $dependency;
                    }
                } else {
                    if (CopixModule::testDependency($dependency)) {
                        $dependency->ok = true;
                        $arExtension[] = $dependency;
                    } else {
                        $dependency->ok = false;
                        $install = false;
                        $arExtension[] = $dependency;
                    }
                }
            }
            $tpl->assign('arExtension', $arExtension);
            $tpl->assign('install',$install);
            $template = 'detailmoduleinstall.tpl';
    	}
    	        
        $tpl->assign('arModule', $arModule);
        $tpl->assign('info',$infos);
        $tpl->assign('moduleName',$moduleName);
        $toReturn = $tpl->fetch($template);
        return true;
	}
}
?>
