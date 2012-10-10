<?php

class ZoneUpdateModule extends CopixZone {
	function _createContent (& $toReturn){
        $moduleName = CopixZone::getParam('moduleName');
	    if (($message = CopixModule::updateModule($moduleName))===true) {
            $toReturn = _i18n('install.module.update').' '.$moduleName.' <img src="'._resource('img/tools/valid.png').'" />';
        } else {
            $toReturn = _i18n('install.module.update').' '.$moduleName.' '._tag('popupinformation',array('img'=>_resource('img/tools/delete.png')),$message);
            $toReturn .= '<div class="errorMessage">'.$message.'</div>';
        }
        return true;
	}
}
?>