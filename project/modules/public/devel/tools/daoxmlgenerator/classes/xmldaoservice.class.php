<?php
CopixClassesFactory::fileInclude ('daoxmlgenerator|sqltable');

class XMLDaoService {
    function getFields ($pTableName){
        return CopixDB::getConnection ()->getFieldList($pTableName);
    }

    function getTables (){
        return CopixDB::getConnection ()->getTableList ();
    }
}
?>