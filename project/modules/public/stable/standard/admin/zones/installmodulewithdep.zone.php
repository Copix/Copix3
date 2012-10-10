<?php

class ZoneInstallModuleWithDep extends CopixZone {
	function _createContent (& $toReturn){
		if (CopixZone::getParam('arModule')!==null && is_array (CopixZone::getParam('arModule'))) {
			$arModule = CopixZone::getParam('arModule'); 
		} else {
			$arModule = array (CopixZone::getParam('moduleName'));
		}
		$arModuleToInstall = array ();
        $arOrder = array ();
		foreach ($arModule as $moduleName) {
        	$arDependency = CopixModule::getDependenciesForInstall ($moduleName);
        	foreach ($arDependency as $key=>$dependency) {
            	if ($dependency->kind === 'module') {
            		//Gestion des modules en double avec les dependences
            		if (!in_array($dependency->name,$arModuleToInstall)) {
                		$arModuleToInstall[] = $dependency->name;
                		$arOrder[] = $dependency->level;
            		} else {
            			//Gestion du niveau d'install des dependences
            			$key = array_search($dependency->name, $arModuleToInstall);
            			if ($arOrder[$key] < $dependency->level) {
            				$arOrder[$key] = $dependency->level;
            			}
            		}
            	}
        	}
		}
        array_multisort ($arOrder,SORT_ASC, $arModuleToInstall, SORT_DESC);
        $arModuleInfos = array ();
        $tpl = new CopixTpl();
        $tpl->assign ('arModuleToInstall', $arModuleToInstall);
        $tpl->assign ('arModuleInfos'    , $arModuleInfos);
        CopixSession::set ('arModuleToInstall', $arModuleToInstall,'copix');
        CopixSession::set ('arInstalledModule', array(), 'copix');      
        $tpl->assign ('id', uniqid());
		$tpl->assign ('url', CopixZone::getParam('url_return', _url('admin|install|manageModules')));
		$tpl->assign ('messageConfirm', CopixZone::getParam('messageConfirm',true));
        $toReturn = $tpl->fetch ('admin|install.script.tpl');
        return true;
	}
}
?>