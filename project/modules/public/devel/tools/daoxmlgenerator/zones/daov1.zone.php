<?php
class ZoneDaoV1 extends CopixZone{
    function _createContent(&$toReturn){
        $tpl = & new CopixTpl();

        $tpl->assign('arrtable',$this->getParam('fields', '') );
        $tpl->assign('tname', $this->getParam('tname', '') );
        $tpl->assign('xmlheader', $this->getParam('xmlheader', ''));
        $tpl->assign('iso', $this->getParam('iso', ''));
        $tpl->assign('raw', $this->getParam('fordownload', '') );
        
        $toReturn = $tpl->fetch('daov1.tpl');
        return true;
    }
}
?>