<?php

class ZoneDeleteModuleConfirm extends CopixZone {
	function _createContent (& $toReturn){
        $arModuleToDelete = CopixSession::get('arModuleToDelete','copix');
        $lastModuleInstalled = end($arModuleToDelete);
        $othermodules = array_slice($arModuleToDelete, 0, count($arModuleToDelete) - 1) ;
        $id = uniqid();

        $toReturn = '<div id="block'.$id.'">';
        $toReturn .= _i18n('install.module.delete.confirm', array($lastModuleInstalled, join(',', $othermodules))).'<br/>';
        $toReturn .= '<input type="button" id="btn'.$id.'" name="remove" value="'._i18n('copix:common.buttons.yes').'"/>';
        $toReturn .= '<input type="button" value="'._i18n('copix:common.buttons.no').'" onclick="javascript:document.location.href=\''._url('admin|install|manageModules').'\'"/>';
        $toReturn .= '</div>';
    	$toReturn .= _tag('copixzone',array ('id'=>'zone'.$id,'process'=>'admin|deletemodule', 'ajax' => true));
        
        $js = <<<EOJS
        $('btn$id').addEvent('click', function(){
			$('zone$id').fireEvent('display');
			$('block$id').setStyle('display', 'none');
		}); 
EOJS;
        CopixHTMLHeader::addJSCode($js, true);

        return true;
	}
}
?>