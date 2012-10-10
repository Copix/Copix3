<?php
//convoyeur de tables
class SQLtable{
    var $tableName="";
    var $Field="";
    var $type ="";
    var $maxlength=0;
    var $tpk ="";
    var $required ="";
    var $fktable ="";
    var $fkfieldname ="";

    function SQLTable(){
        return true;
    }
}
?>