<?php
class ZoneProperties extends CopixZone{
    function _createContent(&$toReturn){
        $tplproperties = & new CopixTpl();
        
        $tplproperties->assign('fields', $this->getParam('fields', '') );
        $tplproperties->assign('tname', $this->getParam('tname', '') );
        $tplproperties->assign('raw', $this->getParam('fordownload', '') );

        $toReturn = $tplproperties->fetch('properties.tpl');
        return true;
    }

}
?>