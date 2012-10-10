<?php
class ZoneShowTables extends CopixZone {
    function _createContent (& $toReturn) {
        $ct = CopixDB::getConnection ()->getTableList ();

        $tpl    = new CopixTpl ();
        $tables = array();
        $serv   = CopixClassesFactory::create('xmldaoservice');

        $tables = $serv->getTables();

        $tpl->assign('iso', $this->getParam('iso', '') ) ;
        $tpl->assign('xmltype', $this->getParam('xmltype', '') ) ;
		$tpl->assign('xmlheader', $this->getParam('xmlheader', '') ) ;

        $tpl->assign('tables',$tables);
        $tpl->assign('nbr',count($tables));

        $toReturn = $tpl->fetch ('show.tables.tpl');
        
        return true;
    }
}
?>