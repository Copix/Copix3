<?php

class ZoneDeleteModule extends CopixZone {
	function _createContent (& $toReturn){
        $arModuleToDelete = CopixSession::get('arModuleToDelete','copix');
        $moduleName = array_pop($arModuleToDelete);
	    if (($message = CopixModule::deleteModule($moduleName))===true) {
            $toReturn = _i18n('install.module.delete').' '.$moduleName.' <img src="'._resource('img/tools/valid.png').'" />';
            if (count($arModuleToDelete)>0) {
                $toReturn .= _tag('copixzone',array ('id'=>uniqid(),'process'=>'admin|deletemodule','auto'=>true, 'ajax'=>true));
            } else {
            	$toReturn .= "<script>$('back').setStyle('display','');</script>";
            }
        } else {
            $toReturn = _i18n('install.module.delete').' '.$moduleName.' '._tag('popupinformation',array('img'=>_resource('img/tools/delete.png')),$message);
            $toReturn .= '<div class="errorMessage">'.$message.'</div>';
        	if (count($arModuleToDelete)>0) {
                $toReturn .= _tag('copixzone',array ('id'=>uniqid(),'process'=>'admin|deletemodule','auto'=>true, 'ajax'=>true));
            } else {
            	$toReturn .= "<script>$('back').setStyle('display','');</script>";
            }
        }
        CopixSession::set('arModuleToDelete',$arModuleToDelete,'copix');
        return true;
	}
}
?>