<?php
/**
 * @package standard
 * @subpackage test
 * @author		Croës Gérald
 * @copyright	CopixTeam
 * @link			http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Actions pour lancer les tests fonctionnels
 *
 * @package standard
 * @subpackage test
 */
class ActionGroupDefault extends CopixActionGroup {
	/**
	 * Par défaut, on lancera les tests.
	 */
	public function processDefault (){
		return _arRedirect (_url ('unittest|'));
		_notify ('breadcrumb', array (
			'path' => array ('admin||' => _i18n ('admin|breadcrumb.admin'))
		));
	}

	/**
	 * Affichage de la liste des tests à lancer
	 */
	public function processTest () {
		$ppo = new CopixPPO();
		$ppo->TITLE_PAGE = _i18n('test.test.title');
		$ppo->arCategories = _dao('testcategory')->findAll ();
		$ppo->arData = array ();
		foreach ($ppo->arCategories as $id => $value) {
			$parameters = _daoSP()->addCondition ('category_test', '=', $value->id_ctest);
			$ppo->arData[$id] = _dao('test')->findBy($parameters);
		}
		$ppo->arLevels = _dao ('testlevel')->findAll ();
		$ppo->level = array ();
		foreach ($ppo->arLevels as $value) {
			$ppo->level[$value->id_level] = '<b>'.'Niveau : '.'<font color="red">'.$value->id_level.' </font>('.$value->caption_level.')<br>'.'Contact : '.$value->email.'</b>';
		}
		return _arPpo($ppo, 'test.list.tpl');
	}
	
	/**
	 * Lance un test donné a partir de son identifiant
	 */
	public function processAjaxLaunch () {
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = _i18n('test.launch.title');
		
		$id = CopixRequest::get('id');
		
		$results = array ();
		$finalResult = true;
		$errorCounter = 0;
		
		$test = $id;
		$result = _ioClass ('testfactory')->create ($test)-> check ();
		// Enregistrement pour l'historique et les statistiques
		if (isset($test)) {
			$historyAdd = new stdClass();
			$historyAdd->id_test = $test;
		}
		
		if ($result->result) {
			$historyAdd->result = 1;
		} else {
			$historyAdd->result = 0;
		}
		$string_error = '';
		
		foreach ($result->errors as $error) {
			$string_error = $string_error.$error; 
		}
		
		if ($string_error) {
			$historyAdd->exception = $string_error;
		} else {
			$historyAdd->exception = 'NULL';
		}
			if (is_string($result->timing)) {
				$historyAdd->timing = (string) $result->timing;
			} else {
				$historyAdd->timing = 'NULL';
			}
			
		CopixDB::getConnection()->doQuery ('insert into testhistory(id_test, time_date, result, exception, timing) values ('.
		$historyAdd->id_test.','.'CURRENT_TIMESTAMP'.','."'".$historyAdd->result."'".','
		.'"'.addslashes($historyAdd->exception).'",'."'".$historyAdd->timing."'".' )');

			/*if ($result->result === false) {
				$finalResult = false;
				$errorCounter = $errorCounter + 1;
			}*/
		$ppo->arData = $result;
		$ppo->errors = $string_error;
		return _arDirectPPO($ppo, 'ajax.result.tpl');
	}
	
	/**
	 * Lancement des tests (version avec Ajax)
	 *
	 * @return unknown
	 */
	public function processLaunch () {
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = _i18n('test.launch.title');
		
		// Si on veut lancer à partir d'une catégorie
		if (_request('cat')) {
			$arId = _request('id');
			foreach (_request('cat') as $id => $value) {
				$parameters = _daoSP()->addCondition ('category_test', '=', $value);
				$tests = _dao('test')->findBy($parameters);
				foreach ($tests as $id) {
					if (_request('id') == null or array_search($id->id_test, _request('id')) === false) {
						$arId[] = $id->id_test;
					}
				}
			}
			// On ajoute les tests à éxécuter en paramètre
			CopixRequest::set('id', $arId);
		}
		
		$id = CopixRequest::get('id');
		if (!$id && !_request('cat')) {
			return _arRedirect (_url ('|test'));
		}
		$finalResult = true;
		$errorCounter = 0;
		$results = array ();
		
		
		foreach ($id as $id2 => $test) {
		CopixRequest::set('id', $test);
			$results[$id2] = $test;
		}
		
		$ppo->arData = $results;
		
		$tests = '';
		foreach ($results as $test) { 
			$tests = $tests.$test.'|';
		}
		$ppo->tests = $tests;
		return _arPpo($ppo, 'launch.view.tpl');
	}
	
	/**
	 * Lance un test donné a partir de son identifiant (version sans ajax)
	 */
	/*public function processLaunch () {
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = _i18n('test.launch.title');
		
		// Si on veut lancer à partir d'une catégorie
		if (_request('cat')) {
			$arId = _request('id');
			foreach (_request('cat') as $id => $value) {
				$parameters = _daoSP()->addCondition ('category_test', '=', $value);
				$tests = _dao('test')->findBy($parameters);
				foreach ($tests as $id) {
					if (_request('id') == null or array_search($id->id_test, _request('id')) === false) {
						$arId[] = $id->id_test;
					}
				}
			}
			// On ajoute les tests à éxécuter en paramètre
			CopixRequest::set('id', $arId);
		}
		
		$id = CopixRequest::get('id');
		if (!$id && !_request('cat')) {
			return _arRedirect (_url ('|test'));
		}
		$results = array ();
		$finalResult = true;
		$errorCounter = 0;
		
		foreach ($id as $id => $test) {
		CopixRequest::set('id', $test);
		$results[$id] = _ioClass ('testfactory')->create ($test)-> check ();

		// Enregistrement pour l'historique et les statistiques
		if (isset($test)) {
			$historyAdd = new stdClass();
			$historyAdd->id_test = $test;
		}
		
		if ($results[$id]->result) {
			$historyAdd->result = 1;
		} else {
			$historyAdd->result = 0;
		}
		$string_error = '';
		
		foreach ($results[$id]->errors as $error) {
			$string_error = $string_error.$error; 
		}
		if ($string_error) {
			$historyAdd->exception = $string_error;
		} else {
			$historyAdd->exception = 'NULL';
		}
			if (is_string($results[$id]->timing)) {
				$historyAdd->timing = (string) $results[$id]->timing;
			} else {
				$historyAdd->timing = 'NULL';
			}
			
		CopixDB::getConnection()->doQuery ('insert into testhistory(id_test, time_date, result, exception, timing) values ('.
		$historyAdd->id_test.','.'CURRENT_TIMESTAMP'.','."'".$historyAdd->result."'".','
		.'"'.addslashes($historyAdd->exception).'",'."'".$historyAdd->timing."'".' )');
		
		
			if ($results[$id]->result === false) {
				$finalResult = false;
				$errorCounter = $errorCounter + 1;
			}
		}
		$ppo->finalResult = $finalResult;
		$ppo->errorCounter = $errorCounter;
		$ppo->arData = $results;
		return _arPpo($ppo, 'launch.view.tpl');
	}*/
	
	/**
	 * Permet de lancer tous les tests en faisant un appel à processLaunch()
	 */
	public function processLaunchAll () {
		$all = _dao ('test')->findAll ();
		$arID = array ();
		foreach ($all as $test) {
			$arID[] = $test->id_test;
		}
		$arID = array('id' => $arID);
		return _arRedirect(_url('default|launch', $arID));
	}
	

	/**
	 * Lance un test et affiche le résultat de chaque étape
	 */
	public function processLaunchDetails () {
		$ppo = new CopixPpo ();
		$ppo->TITLE_PAGE =  _i18n('test.launchdetails.title').' n°: '.CopixRequest::getInt('id');
		$result = _ioClass ('testfactory')->create (CopixRequest::getInt ('id'))->checkSteps ();
		$ppo->results = $result->getResults();
		return _arPpo($ppo, 'launchdetails.view.tpl');
	}
	
	/**
	 * Interception des erreurs de test de façon groupée
	 * @param	Exception	$pException	L'exception a traiter	 
	 */
	public function catchActionExceptions ($pException){
		if ($pException instanceof CopixTestException) {
			//@todo a traiter pour ajouter le fait que le test a échouer dans le fichier XML
		} else {
			throw $pException;
		}

		throw $pException;
	}
}