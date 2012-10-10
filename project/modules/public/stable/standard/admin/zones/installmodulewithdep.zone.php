<?php

class ZoneInstallModuleWithDep extends CopixZone {
	function _createContent (& $toReturn){
        $moduleName = CopixZone::getParam('moduleName');
        $arDependency = CopixModule::getDependenciesForInstall($moduleName);
        $arModuleToInstall = array ();
        $arOrder = array ();
        foreach ($arDependency as $key=>$dependency) {
            if ($dependency->kind === 'module') {
                $arModuleToInstall[] = $dependency->name;
                $arOrder[] = $dependency->level;
            }
        }
        array_multisort($arOrder,SORT_ASC,$arModuleToInstall);
        $tpl = new CopixTpl();
        $tpl->assign('arModuleToInstall',$arModuleToInstall);
        CopixSession::set('arModuleToInstall',$arModuleToInstall,'copix');
        CopixSession::set('arInstalledModule',array(),'copix');      
        $tpl->assign('id',uniqid());
        $toReturn = $tpl->fetch('admin|install.script.tpl');
        return true;
	}
}
?>