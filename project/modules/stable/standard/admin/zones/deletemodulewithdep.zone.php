<?php

class ZoneDeleteModuleWithDep extends CopixZone {
	function _createContent (& $toReturn){
        $moduleName = CopixZone::getParam('moduleName');
        $arDependency = CopixModule::getDependenciesForDelete($moduleName);
        $tpl = new CopixTpl();
        $tpl->assign('arDependency', $arDependency);
        $tpl->assign('arModuleToDelete', $arDependency);
        CopixSession::set('arModuleToDelete',$arDependency,'copix');
        $tpl->assign('id',uniqid());
        $tpl->assign('arModuleToDelete',$arDependency);
        $toReturn = $tpl->fetch('delete.script.tpl');
        return true;
	}
}
?>