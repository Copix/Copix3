<?php
class ActionGroupAdmin extends CopixActionGroup {
	
	/**
	 * Création d'un nouveau test SOAP
	 */
	public function processCreate () {
		$recordTest = _record ('copixtesttest');
		$recordTest->type_test = CopixRequest::get ('type');
		CopixSession::set ('copixtestsoap|edit', $recordTest);
		return _arRedirect (_url('admin|edit'));
	}
	
	/**
	 * Edition du test
	 */
	public function processEdit () {
		$ppo = new CopixPpo ();
		$ppo->TITLE_PAGE = _i18n('copixtest_soap.edit.title');
		
		/**
		 * On affiche les éventuelles erreurs
		 */
		if (_request('errors')) {
			$ppo->arErrors = _request('errors');
		}
		
		$ppo->arCategories = _dao('copixtestcategory')->findAll ();
		$ppo->arLevel = _dao('copixtestlevel')->findAll ();
		if (CopixRequest::get('id_test')) {
			$recordTest = _dao('copixtesttest')->get (CopixRequest::get('id_test'));
			CopixSession::set('copixtestsoap|edit', $recordTest);
		} else {
			$recordTest = CopixSession::get ('copixtestsoap|edit');
		}
		if (isset($recordTest->id_test)) {
			$recordTestSOAP = _dao ('copixtestsoap')->get ($recordTest->id_test);
		}
		$ppo->toEdit = $recordTest;
		if(isset($recordTestSOAP)) {
		$ppo->toEditSOAP = $recordTestSOAP;
		if ($recordTestSOAP->proxy == 1) {
			$ppo->proxyenabed = 'checked';
			$ppo->proxydisabled = null;
		} else {
			$ppo->proxyenable = null;
			$ppo->proxydisabled = 'checked';
		}
		CopixSession::set ('copixtestsoap|configure', $recordTestSOAP);
		}
		return _arPpo ($ppo, 'copixtestsoap.edit.tpl');
	}
	
	public function processConfigure () {
		$ppo = new CopixPpo ();
		$ppo->TITLE_PAGE = _i18n('copixtest_soap.configure.title');
		
		/**
		 * On vérifie si tous les paramètres ont bien été saisis
		 */
		if (!CopixRequest::get('caption_test') or
			!CopixRequest::get('level_test') or
			!CopixRequest::get('category_test') or
			!CopixRequest::get('address')) {
				
				return _arRedirect ( _url('copixtest_soap|admin|edit',
				array ('errors' => _i18n('copixtest_soap.edit.incompleteForm'))));
		}
		
		$recordTest = CopixSession::get ('copixtestsoap|edit');
		$recordTest->caption_test = CopixRequest::get('caption_test');
		$recordTest->level_test = CopixRequest::get('level_test');
		$recordTest->category_test = CopixRequest::get('category_test');
		if (CopixRequest::get('address')) {
		$recordTestSOAP = new stdClass();
		$recordTestSOAP->address_soap = CopixRequest::get ('address');
		$recordTestSOAP->proxy = CopixRequest::get ('proxy');
		}
		CopixSession::set('copixtestsoap|edit', $recordTest);
		CopixSession::set('copixtestsoap|configure', $recordTestSOAP);
		
		// On affiche les méthodes de chaque classe
		$soapClient = new CopixSoapClient ($recordTestSOAP->address_soap);
		$soapClient->setProxy((bool)$recordTestSOAP->proxy);
		$soap = $soapClient->getSOAP();
		
		$functions = $soap->__getFunctions ();
		$ppo->arData = $functions;
		CopixSession::set('copixtestsoap|functions', $functions);
		
		// On récupère les valeurs précédentes
		if (isset($recordTest->id_test)) {
			$parameters = _daoSP()->addCondition('id_test', '=', $recordTest->id_test);
			$ppo->previousValues = _dao('copixtestsoapfunctions')->findBy($parameters);
		}
		
		return _arPpo ($ppo, 'copixtestsoap.configure.php');
	}

	public function processSave () {
		$recordTest = CopixSession::get ('copixtestsoap|edit');
		$recordTestSOAP = CopixSession::get ('copixtestsoap|configure');
		$recordTestSOAP->id_test = $recordTest->id_test;

			if ($recordTest->id_test) {
				_dao('copixtesttest')->update ($recordTest);
				_dao('copixtestsoap')->update ($recordTestSOAP);
			} else {
				$id = _dao('copixtesttest')->insert ($recordTest);
				$recordTestSOAP->id_test = $recordTest->id_test;
				_dao('copixtestsoap')->insert ($recordTestSOAP);
			}
			
		// On supprime les enregistrements précédents
		if ($recordTestSOAP->id_test) {
			$params = _daoSP()->addCondition ('id_test', '=', $recordTestSOAP->id_test);
			_dao('copixtestsoapfunctions')->deleteby ($params);
		}
		
		// On met les méthodes à vérifier dans un tableau
		$recordTestSOAPFunctions = array ();
		foreach (CopixSession::get('copixtestsoap|functions') as $id => $value) {

			if (CopixRequest::get ('checktype_'.$id) !== 'notest') {
				$recordTestSOAPFunction = _record('copixtestsoapfunctions');
				$recordTestSOAPFunction->id_test = $recordTest->id_test;
				$recordTestSOAPFunction->name_function = $value;
				$recordTestSOAPFunction->checktype = CopixRequest::get ('checktype_'.$id);
				$recordTestSOAPFunctions[] = $recordTestSOAPFunction;
			}
		}
		if ($recordTestSOAPFunctions) {
			foreach ($recordTestSOAPFunctions as $key => $record) {
				_dao ('copixtestsoapfunctions')->insert ($record);
			}
		}
		

		CopixSession::destroyNamespace('copixtestsoap|edit');
		CopixSession::destroyNamespace('copixtestsoap|configure');
		CopixSession::destroyNamespace('copixtestsoap|functions');
		return _arRedirect(_url('copixtest|admin|'));
	}
	
	/**
	 * Suppression du test
	 */
	public function processDelete () {
	 	CopixRequest::assert('id_test');
 	 	$record = _ioDAO ('copixtesttest')->get(CopixRequest::get('id_test'));
		if(!$record->id_test) {
 	 		return _arRedirect(_url('copixtest|admin|'));
		} else {
			$params = _daoSp()->addCondition('id_test','=',$record->id_test);
			_ioDao('copixtesttest')->deleteby($params);
			_ioDao('copixtestsoap')->deleteby($params);
			_ioDao('copixtestsoapfunctions')->deleteby($params);
		}
		return _arRedirect(_url('copixtest|admin|'));
	}
	
	public function processCancel () {
		CopixSession::delete('copixtestsoap|edit');
		CopixSession::delete('copixtestsoap|configure');
		return _arRedirect (_url ('copixtest|admin|default'));
	}
}
?>