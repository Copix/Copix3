<?php
class ActionGroupXMLGenerator extends CopixActionGroup {
    function getTablesFromDB(){
        $tpl = new CopixTpl();
        $tpl->assign ('TITLE_PAGE', CopixI18N::get ('daoxmlgenerator.title'));
        $tpl->assign ('MAIN', CopixZone::process('showtables',$this->vars));
        return new CopixActionReturn (CopixActionReturn::DISPLAY, $tpl) ;
    }

    function getXMLDao(){
        $tpl = & new CopixTpl();
        $tpl->assign ('TITLE_PAGE', CopixI18N::get ('daoxmlgenerator.title'));

        //Case of dao xml requested
        if($this->vars['xmltype']=="dao" || $this->vars['xmltype']=="daov1" || $this->vars['xmltype']=="daov2"){
            $tpl = new CopixTpl();
            if ($this->vars['tablename']){
                $tablename=$this->vars['tablename'];
                $tableSQL = $this->_createTablesFromList($tablename);
            }
            $tpl->assign ('MAIN', CopixZone::process('showxml',
										            array('tableSQL'=>$tableSQL,
										            'iso'=>$this->vars['iso'],
										            'xmlheader'=>$this->vars['xmlheader'],
										            'xmltype'=>$this->vars['xmltype'])));
        }
        return new CopixActionReturn (CopixActionReturn::DISPLAY, $tpl);
    }

    function doDownload(){
        $tablelist= $this->_createTablesFromList($this->vars['table']);
        $tablefiles=array();
        $zone="";
        switch($this->vars['xmltype']){
            case 'dao':
	            $zone='Daov0';
	            break;
            case 'daov1':
	            $zone='Daov1';
	            break;
            case 'daov2':
	            $zone='Daov2';
	            break;
            default:
            break;
        }

        $content=array();
        $properties=array();
        foreach($tablelist as $tname=>$fields){
            //get dao files
            $content [$tname.'.dao.definition.xml']= CopixZone::process($zone,
            array('fields'=>$fields,
            'tname'=>$tname,
            'xmlheader'=>$this->vars['xmlheader'],
            'iso'=>$this->vars['iso'],
            'fordownload'=>true));
            $tname=null;
        }

        if(count(CopixRequest::get('properties'))){
            foreach(CopixRequest::get('properties') as $tname){
                //get properties files
                $properties['dao'.$tname.'_fr.properties']= CopixZone::process('properties',
                array('fields'=>$fields,
                'tname'=>$tname,
                'fordownload'=>true));
                $tname = null ;
            }
        }

        //CopixClassesFactory::fileInclude('compressor|CopixCompressorFactory');
        Copix::RequireClass ('CopixCompressorFactory');
        $zip = CopixCompressorFactory::create('Zip');

        foreach($content as $name=>$dao){
            $zip->compressContent ($dao,'resources/'.$name);
        }

        foreach($properties as $name=>$prop){
            $zip->compressContent ($prop,'resources/'.$name);
        }

        return new CopixActionReturn (CopixActionReturn::DOWNLOAD_CONTENT, $zip->getBuffer(), "dao.zip");
    }

    function _createTablesFromList($tablename=array()){
		//Copix::RequireOnce  (COPIX_UTILS_PATH.'CopixUtils.lib.php');
		CopixClassesFactory::fileInclude ('sqltable');

        $tableSQL=array();
        if(!count($tablename)) return $tableSQL;
        foreach($tablename as $table){
            //$t = new SQLTable();
            //$t->tableName=$table;
            $serv = CopixClassesFactory::create('xmldaoservice');
            $fdef = $serv->getFields($table);

            foreach($fdef as $fieldname=>$field){
	            $t = new SQLTable();
	            $t->tableName=$table ;
                $t->Field=$fieldname ;
                $field->primary ? $t->tpk="true" : $t->tpk="false";
                $field->notnull ?  $t->required="yes" : $t->required="no";
                $t->fktable = isset ($field->fktable) ? $field->fktable : null;
                $t->fkfieldname = isset ($field->fkfieldname) ?  $field->fkfieldname : null;
                
                if($field->isAutoIncrement){
					$t->type = "autoincrement";
                } elseif ($field->type){
                    $realtype = preg_replace('/([a-zA-Z])\(\d*\)/','\\1',$field->type);
                    $text_properties = array('varchar','text','tinytext','string');
                    if (in_array($realtype,$text_properties)){
                        $realtype="string" ;
                    }
                    $text_properties = array('bigint');
                    if (in_array($realtype,$text_properties)){
                        $realtype="int" ;
                    }
                    $t->type = $realtype;
                    $t->maxlength = (is_null($field->length)) ? "" : $field->length;
                }
                $tableSQL[$table][]=$t;
            }
        }
        return $tableSQL;
    }
}
?>