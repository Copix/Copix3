<?php
class ActionGroupDefault extends CopixActionGroup{

	protected function _beforeAction ($pActionName){
		_currentUser()->assertCredential('basic:admin');
	}

	public function processDefault(){
		$ppo = _ppo();
		$ppo->TITLE_PAGE = _i18n('wsdl2php.title');
		$ppo->modules = CopixModule::getList();
		$ppo->wsdl = _request('wsdl');
		$ppo->module = _request('classmodule');
		$ppo->success = _request('success');
		$ppo->error = _request('error');
		$ppo->missing = _request('missing');
		return _arPPO($ppo, 'define.php');
	}

	public function processValidate(){
		$arMissing = array();
		if(($wsdl = _request('wsdl')) === null){
			$arMissing[] = 'wsdl';
		}
		if(($module = _request('classmodule')) === null){
			$arMissing[] = 'module';
		}
		$params = array('wsdl' => $wsdl, 'classmodule' => $module);
		if(count($arMissing) > 0){
			return _arRedirect(_url('wsdl2php||',array_merge($params, array('missing' => join($arMissing)))) );
		}
		
		try{
			$modules = CopixModule::getList();
			$moduleName = $modules[$module];
			$interpreter = new WSDLInterpreter($wsdl);
			$outputFolder = CopixModule::getPath($moduleName).COPIX_CLASSES_DIR;
			$params ['success'] = join(',', $interpreter->savePHP($outputFolder));
		}catch(Exception $e){
			return _arRedirect(_url('wsdl2php||', array_merge($params, array('error' => $e->getMessage()))));
		}
		return _arRedirect(_url('wsdl2php||', $params));
	}

}