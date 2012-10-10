<?php

class ZoneInstallModule extends CopixZone {
	function _createContent (& $toReturn){
        $arModuleToInstall = CopixSession::get('arModuleToInstall','copix');
        $arInstalledModule = CopixSession::get('arInstalledModule','copix');
        $moduleName = array_pop($arModuleToInstall);
        if (($message = CopixModule::installModule($moduleName))===true) {
            $toReturn = _i18n('install.module.install').' '.$moduleName.' <img src="'._resource('img/tools/valid.png').'" />';
            if (count($arModuleToInstall)>0) {
                $toReturn .= _tag('copixzone',array ('id'=>uniqid(),'process'=>'admin|installmodule','auto'=>true, 'ajax'=>true));
            } else {
            	$toReturn .= "<script>$('back').setStyle('display','');</script>";
            }
            array_push($arInstalledModule,$moduleName);
        } else {
            array_push($arInstalledModule,$moduleName);
            $toReturn = _i18n('install.module.install').' '.$moduleName.' '._tag('popupinformation',array('img'=>_resource('img/tools/delete.png')),$message);
            $toReturn .= '<div class="errorMessage">'.$message.'</div>';
            if (count($arInstalledModule)>0) {
                CopixSession::set('arModuleToDelete',$arInstalledModule,'copix');
                CopixSession::set('arInstalledModule',null,'copix');
                CopixSession::set('arModuleToInstall',null,'copix');
                $toReturn .= _tag('copixzone',array ('id'=>uniqid(),'process'=>'admin|deletemodule','auto'=>true,'ajax'=>true));
            }
        }
        CopixSession::set('arModuleToInstall',$arModuleToInstall,'copix');
        CopixSession::set('arInstalledModule',$arInstalledModule,'copix');        
        return true;
	}
}
?>