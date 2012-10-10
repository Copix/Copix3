<?php

class ActionGroupAdmin extends CopixActionGroup {
    
	public function processListExport () {
		$ppo = new CopixPPO();
		$ppo->arModuleList = CopixModule::getList(false);
		return _arPpo($ppo,'module.list.tpl');
	}
	
    public function processExportConfirm () {
        CopixRequest::assert('moduleName');
        $ppo = new CopixPPO ();
        
        $ppo->name = _request('moduleName');
        
        $infos = CopixModule::getInformations(_request('moduleName'));
        
        $ppo->version = $infos->version;
        
        $ppo->description = (isset($infos->longdescription) ? $infos->longdescription : $infos->description);
        
        return _arPpo($ppo,'export.form.tpl');
        
    }
    
    public function processExport () {
        
        $record = _record('moduleserver');
        
        $record->module_name = _request('name');
        $record->module_description = _request('description');
        $record->module_version = _request('version');
        
        _dao('moduleserver')->insert($record);
        
        $id = $record->id_export;
        CopixClassesFactory::fileInclude('compressor|copixcompressorfactory');
        $zipper = CopixCompressorFactory::create('zip');

        $path = CopixModule::getPath(_request('name'));
        foreach (CopixFile::search('*',$path) as $file) {
            if (!strpos($file,'/CVS/')) {
                $zipper->compressContent(CopixFile::read($file),str_replace($path,_request('name').'/',$file));
            }
        }
        
        CopixFile::write(COPIX_TEMP_PATH.'/module_'.$id.'.zip',$zipper->file());
        
        return _arRedirect (_url('admin||'));
    }
    
    public function processGetModule () {
        $id = _request('id');
        return _arFile(COPIX_TEMP_PATH.'/module_'.$id.'.zip');
    }
    
}

?>