<?php
class ZoneShowXML extends CopixZone {
    function _createContent (&$toReturn) {
        $tpl = & new CopixTpl ();
        $tpl->assign('xmlheader',$this->_params['xmlheader']);
        $tpl->assign('iso',$this->_params['iso']);
        $tpl->assign('xmltype',$this->_params['xmltype']);
        $zone="";
        switch($this->_params['xmltype']){
            case 'dao':
               $zone = 'Daov0';
               break;
            case 'daov1':
               $zone = 'Daov1';
               break;
            case 'daov2':
               $zone = 'Daov2';
               break;
            default:
               break;
        }
        $content    = "";
        $properties = "";
        
        if (count ($this->_params['tableSQL'])){
            foreach($this->_params['tableSQL'] as $tname=>$fields){
                $content .= CopixZone::process($zone,array('fields'=>$fields,'tname'=>$tname,'xmlheader'=>$this->_params['xmlheader'],'iso'=>$this->_params['iso']));
                $properties .= CopixZone::process('properties',array('fields'=>$fields,'tname'=>$tname));
            }
        }
        $tpl->assign('content',$content);
        $tpl->assign('properties',$properties);
        $toReturn = $tpl->fetch ('show.daoxml.tpl');

        return true;
    }
}
?>