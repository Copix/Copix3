<?php

class ActionGroupDefault extends CopixActionGroup {
    
    
    public function processListModuleDeporte () {
         $server = $this->_getServer(); 
         $soap = CopixSoapClient::get($server);
         $arModule = unserialize($soap->getList());
         $ppo = new CopixPPO();
         $ppo->arModuleInstall = CopixModule::getList();
         $ppo->distantModule = $arModule;
         foreach ($ppo->distantModule as $key=>$module) {
         	if (in_array($module->module_name,$ppo->arModuleInstall)) {
         		$ppo->distantModule[$key]->installed = true;
         	}
         }
         return _arPpo ($ppo,'module.list.tpl');
    }
    
    public function processInstall () {
    	$arId = _request('id');
    	$server = $this->_getServer();
        $soap = CopixSoapClient::get($server);
        $arName = array ();
        foreach ($arId as $idname) {
        	$arTemp = explode('|',$idname);
        	$id = $arTemp[0];
        	$name = $arTemp[1];
        	$arModuleList = CopixModule::getList(false);
        	if (!in_array($name, $arModuleList)) {
		        $module = base64_decode($soap->getModule($id));
		    	CopixFile::write(COPIX_TEMP_PATH.'moduletest_'.$id.'.zip',$module);
		    	CopixClassesFactory::fileInclude('compressor|CopixCompressorFactory');
		    	$zipper = CopixCompressorFactory::create('zip');
		    	$zipper->uncompress(COPIX_TEMP_PATH.'moduletest_'.$id.'.zip',COPIX_VAR_PATH.'/modules');
        	}
	    	$arName[] = $name;
        }
    	$tpl = new CopixTpl();
  	    //$tpl->assign('TITLE_PAGE',_i18n('install.title.installModule',_request('moduleName')));
		return _arRedirect(_url('admin|install|installModules',array('arModule'=>implode('|',$arName))));    
		
    }

    
    private function _getServer () {
        $soapserver = CopixUrl::get(CopixConfig::get('moduleclient|server'));
 		return CopixFile::trailingSlash($soapserver).'moduleserver';
    }
}

?>