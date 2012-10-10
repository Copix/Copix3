<?php
/**
 * @package     standard
 * @subpackage  test
 * @author		Alexandre Julien, Gérald Croës
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Actions d'administration sur les tests de webservices
 * @package standard
 * @subpackage test
 */
class ActionGroupAdmin extends CopixActionGroup {
	/**
	 * Création d'un nouveau test SOAP
	 */
	public function processCreate () {
		//Création de l'enregistrement
		$recordTest = _record ('test');
		$recordTest->type_test = 'soap';
		CopixSession::set ('testsoap|edit', $recordTest);

		//redirection vers la modification du test nouvellement crée
		return _arRedirect (_url('admin|edit'));
	}

	/**
	 * Modification du test
	 */
	public function processEdit () {
		//Préparation des informations générales
		$ppo = _ppo (array ('TITLE_PAGE'=>_i18n('test_soap.edit.title')));
		$ppo->arCategories = _dao ('testcategory')->findAll ();
		$ppo->arLevel      = _dao ('testlevel')->findAll ();

		//Si c'est une demande de modification d'un test, on charge le test a modifier
		if (_request ('id_test')) {
			//Mise en session des informations générales sur le test
			$ppo->toEdit = _dao('test')->get (_request ('id_test'));
			CopixSession::set ('testsoap|edit', $ppo->toEdit);
		} else {
			$ppo->toEdit = CopixSession::get ('testsoap|edit');
		}
		
		//On regarde si l'url de test est donnée. Si c'est le cas, 
		// on affiche la liste des fonctions disponibles à cette adresse
		if ($ppo->toEdit->url_stest){
			try {
				$soap = CopixSoapClient::get ($ppo->toEdit->url_stest);
				foreach ($soap->__getFunctions () as $function){
					$ppo->toEdit->functions[$function] = $function; 
				}
				
			}catch (CopixException $e){
				$ppo->soapFault = $e->getMessage ();
			}
		}

		//On regarde s'il est possible d'afficher la configuration du reste du test
		return _arPpo ($ppo, 'testsoap.edit.tpl');
	}
	
	/**
	 * Sauvegarde intermédiaire des éléments en cours de modification sur le test SOAP
	 */
	public function processValid (){
		//Sauvegarde des modifications apportées au formulaire
		$test = CopixSession::get ('testsoap|edit');
		_ppo (CopixRequest::asArray ('caption_test', 'level_test', 'id_ctest', 'url_stest', 'function_stest'))->saveIn ($test);
		
		//Mise à jour des données en session
		CopixSession::set ('testsoap|edit', $test);
		
		//redirection sur la page de modification
		return _arRedirect ('admin|edit');
	}

	/**
	 * Configuration supplémentaires sur les tests soap
	 *
	 * @return unknown
	 */
	public function processConfigure () {
		$ppo = _ppo (array ('TITLE_PAGE'=>_i18n('test_soap.configure.title')));

		//vérification des paramètres envoyés
		try {
			CopixRequest::assert ('caption_test', 'level_test', 'id_ctest');
		}catch (CopixException $e){
			return _arRedirect ( _url('test_soap|admin|edit',
			   array ('errors' => _i18n('test_soap.edit.incompleteForm'))));
		}

		//sauvegarde des valeurs du formulaire
		$recordTest = CopixSession::get ('testsoap|edit');
		_ppo (CopixRequest::asArray ('caption_test', 'level_test', 'id_ctest'))->saveIn ($recordTest);

		CopixSession::set ('testsoap|edit', $recordTest);

		// On affiche les méthodes de chaque classe
		$soapClient = new CopixSoapClient ($recordTest->address_soap);
		$soap = $soapClient->getSOAP ();

		$functions = $soap->__getFunctions ();
		$ppo->arData = $functions;
		CopixSession::set ('testsoap|functions', $functions);

		// On récupère les valeurs précédentes
		if (isset($recordTest->id_test)) {
			$parameters = _daoSP ()->addCondition('id_test', '=', $recordTest->id_test);
			$ppo->previousValues = _dao ('testsoapfunctions')->findBy ($parameters);
		}
		return _arPpo ($ppo, 'testsoap.configure.php');
	}

	public function processSave () {
		$recordTest     = CopixSession::get ('testsoap|edit');
		$recordTestSOAP->id_test = $recordTest->id_test;

		if ($recordTest->id_test) {
			_dao ('test')->update ($recordTest);
			_dao ('testsoap')->update ($recordTestSOAP);
		} else {
			$id = _dao ('test')->insert ($recordTest);
			$recordTestSOAP->id_test = $recordTest->id_test;
			_dao ('testsoap')->insert ($recordTestSOAP);
		}
			
		// On supprime les enregistrements précédents
		if ($recordTestSOAP->id_test) {
			$params = _daoSP ()->addCondition ('id_test', '=', $recordTestSOAP->id_test);
			_dao ('testsoapfunctions')->deleteby ($params);
		}

		// On met les méthodes à vérifier dans un tableau
		$recordTestSOAPFunctions = array ();
		foreach (CopixSession::get ('testsoap|functions') as $id => $value) {
			if (CopixRequest::get ('checktype_'.$id) !== 'notest') {
				$recordTestSOAPFunction = _record ('testsoapfunctions');
				$recordTestSOAPFunction->id_test = $recordTest->id_test;
				$recordTestSOAPFunction->name_function = $value;
				$recordTestSOAPFunction->checktype = CopixRequest::get ('checktype_'.$id);
				$recordTestSOAPFunctions[] = $recordTestSOAPFunction;
			}
		}
		if ($recordTestSOAPFunctions) {
			foreach ($recordTestSOAPFunctions as $key => $record) {
				_dao ('testsoapfunctions')->insert ($record);
			}
		}

		CopixSession::delete ('testsoap|edit');
		CopixSession::delete ('testsoap|functions');
		return _arRedirect (_url('test|admin|'));
	}

	/**
	 * Suppression du test
	 */
	public function processDelete () {
		CopixRequest::assert ('id_test');
		$record = _ioDAO ('test')->get (CopixRequest::get ('id_test'));
		if (!$record->id_test) {
			return _arRedirect (_url('test|admin|'));
		} else {
			$params = _daoSp ()->addCondition ('id_test', '=', $record->id_test);
			_ioDao('test')->deleteby ($params);
			_ioDao('testsoap')->deleteby ($params);
			_ioDao('testsoapfunctions')->deleteby ($params);
		}
		return _arRedirect (_url('test|admin|'));
	}

	/**
	 * On annule les modifications sur le test en cours
	 *
	 * @return
	 */
	public function processCancel () {
		CopixSession::delete ('testsoap|edit');
		return _arRedirect (_url ('test|admin|default'));
	}
}