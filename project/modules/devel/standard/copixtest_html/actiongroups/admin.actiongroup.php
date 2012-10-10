<?php
/**
 * @package standard
 * @subpackage copixtest_html
 * @author		Croës Gérald, Julien Alexandre
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */


/**
 * Prise en charge des opération de création / modification d'un type de test donné
 */
class ActionGroupAdmin extends CopixActionGroup {
	
	/**
	 * Création d'un test d'URL
	 */
	public function processCreate () {
		$recordTest = _record ('copixtesttest');
		$recordTest->type_test = CopixRequest::get ('type');
		CopixSession::set ('copixtesthtml|edit', $recordTest);
		return _arRedirect (_url('admin|edit'));
	}
	
	/**
	 * Edition du test
	 */
	public function processEdit () {
		$ppo = new CopixPpo ();
		$ppo->TITLE_PAGE = _i18n('copixtest_html.edit.title');
		
		/**
		 * On affiche les éventuelles erreurs
		 */
		if (_request('errors')) {
			$ppo->arErrors = _request('errors');
		}
		
		$ppo->arDomain = _dao ('copixtesthtmldomain')->findAll ();
		
		if(CopixRequest::get ('id_test')) {
			$recordTest = _dao('copixtesttest')->get(CopixRequest::get ('id_test'));
			$recordTestHTML = _dao('copixtesthtml')->get(CopixRequest::get('id_test'));
			$ppo->toEditHTML = $recordTestHTML;
			$ppo->toEdit = $recordTest;
			$ppo->arLevel = _dao('copixtestlevel')->findAll();
			$ppo->arCategories = _dao ('copixtestcategory')->findAll ();
			$ppo->arSessions = _dao('copixtesthtmlsession')->findAll ();
			if (isset($ppo->toEditHTML->proxy) && $ppo->toEditHTML->proxy == 1) {
				$ppo->proxyenabled = 'checked';
				$ppo->proxydisabled = null;
			} else {
				$ppo->proxyenabled = null;
				$ppo->proxydisabled = 'checked';
			}
			CopixSession::set('copixtesthtml|edit', $recordTest);
			CopixSession::set('copixtesthtml|configure', $recordTest);
			
		} else {
			$recordTest = CopixSession::get ('copixtesthtml|edit');
			$ppo->toEdit = $recordTest;
			$ppo->arLevel = _dao('copixtestlevel')->findAll();
			$ppo->arCategories = _dao ('copixtestcategory')->findAll ();
			$ppo->arSessions = _dao('copixtesthtmlsession')->findAll ();
		}
		return _arPpo ($ppo, 'copixtesthtml.edit.tpl');
	}
	
	/**
	 * Configuration du test : définition des balises à tester
	 */
	public function processConfigure () {
		
		$ppo = new CopixPpo ();
		$ppo->TITLE_PAGE = _i18n('copixtest_html.configure.title');
		CopixSession::delete('copixtesthtml|configure');
		
		/**
		 * On vérifie si tous les paramètres ont bien été saisis
		 */
		if (!CopixRequest::get('caption_test') or
			!CopixRequest::get('level_test') or
			!CopixRequest::get('category_test') or
			!CopixRequest::get('id_domain')) {
				return _arRedirect ( _url('copixtest_html|admin|edit',
				array ('errors' => _i18n('copixtest_html.edit.incompleteForm'))));
		}
		
		
		/* On récupère les paramètres qui définissent le test depuis le formulaire précédent */
		$recordTest = CopixSession::get ('copixtesthtml|edit');
		$recordTest->caption_test = (CopixRequest::get ('caption_test'));
		$recordTest->level_test = CopixRequest::get ('level_test');
		$recordTest->category_test = CopixRequest::get('category_test');
		$recordTestHTML = _record ('copixtesthtml');
		$recordTestHTML->proxy = CopixRequest::get('proxy');
		$recordTestHTML->session = CopixRequest::get('session');
		$recordTestHTML->domain = CopixRequest::get('id_domain');
		$recordTestHTML->path = CopixRequest::get('path');
		$recordTestHTML->url = CopixRequest::get('id_domain').CopixRequest::get('path');
		$recordTestHTML->param_post = CopixRequest::get('param_post');
		$recordTestHTML->fileParam_test = CopixRequest::getFile('param_file'); // en réflexion
		$recordTestHTML->cookiesParam_test = CopixRequest::get('param_cookies');
				
		CopixClassesFactory::fileInclude('copixtest_html|session');
		
		/* On fait une requête sur la page */
		$request = new CopixHTTPClientRequest ($recordTestHTML->url);
		$request->setCookie($recordTestHTML->cookiesParam_test);
		$request->setPost(array('login' => 'expert', 'password' => 'bambou'));
		$request->setProxy((bool)$recordTestHTML->proxy);
		$request->setFile ($recordTestHTML->fileParam_test);
		$request->setFollowRedirect(true);
		
		/* On prépare la session si il y en a une */
		 if ($recordTestHTML->session !== null) {
			$session_data = _dao('copixtesthtmlsession')->get ($recordTestHTML->session);
			$session_login = _dao('copixtesthtml')->get ($session_data->login_session);
			$session_logout = _dao('copixtesthtml')->get ($session_data->logout_session);
			
			$request_login = new CopixHttpClientRequest ($session_login->url);
			$request_login-> setFollowRedirect(true);
			$request_login-> setPost (explode(',',$session_login->param_post));
			$request_login-> setProxy ((bool)$session_login->proxy);
			$request_login-> setFile ($session_login->param_file);
			$request_login-> setCookie($session_login->param_cookies);

			if ($session_logout) {
				$request_logout = new CopixHttpClientRequest ($session_logout->url);
				$request_logout-> setFollowRedirect(true);
				$request_logout-> setPost (explode(',',$session_logout->param_post));
				$request_logout-> setProxy ((bool)$session_logout->proxy);
				$request_logout-> setFile ($session_logout->param_file);
				$request_logout-> setCookie($session_logout->param_cookies);
			}
		}
			
		/*
		 * On récupère les données de la base si on fait une modification
		 */
		$parameters = _daoSp()->addCondition ('id_test', '=', (int)  $recordTest->id_test);
		if ($recordTest->id_test) {			
			$ppo->headerPreviousValues = _dao('copixtesthtmlheader')->findBy($parameters);
			$ppo->bodyPreviousValues = _dao('copixtesthtmlbody')->findBy($parameters);
		} else {
			$ppo->headerPreviousValues = array ();
			$ppo->bodyPreviousValues = array ();
		}
		
		/*
		 * On enregistre les balises du Body
		 */
		CopixClassesFactory::fileInclude ('copixtest_html|tag');
		$tagInstance = new Tag ();
		
		if ($recordTestHTML->session == null) {
			$tagInstance->requestHTTP($request);
		} else {
			$copixHttpSession = new Session($request_login, $request_logout);
			$copixHttpSession-> addHttpClientRequest($request);
			$copixHttpClient = $copixHttpSession->getHttpClientRequest();
			$tagInstance->requestHTTPSession($copixHttpClient);
		}
		
		$ppo->header = $tagInstance->getHeader();
		$tagInstance->configureBody();
		$ppo->bodyPreviousValues = _dao('copixtesthtmlbody')->findBy($parameters);
		$ppo->body = $tagInstance->getXML();
		
		/*
		 * On affiche l'interface des tests libres
		 */
		foreach ($ppo->bodyPreviousValues as $key => $value) {
			if ($value->id_tag >= count($ppo->body)-1) {
				$ppo->freetest[] = $value;	
			}
		}	
		
		/*
		 * On met à jour les données de configuration dans la session
		 */
		CopixSession::set('copixtesthtml|configure', $recordTestHTML);

		return _arPpo($ppo, 'copixtesthtml.configure.php');
	}
	
	/**
	 * Affichage d'une configuration d'une balise
	 * ActionGroup appellé avec Ajax uniquement
	 *
	 * @return unknown
	 */
	public function processGetConfigurationAjax () {
		$ppo = new CopixPpo ();
		CopixClassesFactory::fileInclude ('copixtest_html|tag');
		$ppo->arData = _record('copixtesthtmlbody');
		$ppo->arData->id = CopixRequest::get('id_tag');
		$ppo->arData->type = CopixRequest::get('type');
		$ppo->arData->path = CopixRequest::get('path');
		$ppo->arData->name = CopixRequest::get('name');
		$ppo->arData->attributes = CopixRequest::get('attributes');
		$ppo->arData->contains = CopixRequest::get('contains');
		return _arDirectPpo($ppo, 'copixtesthtml.configure.ajax.php');
	}
	
	/**
	 * Affiche une interface pour créer un test libre sur une balise qui n'est pas forcement
	 * existante.
	 * 
	 * @return unknown
	 */
	public function processGetNewFreeConfigurationAjax () {
		$ppo = new CopixPpo();
		$ppo->arData = _record ('copixtesthtmlbody');
		$ppo->arData->id_tag = CopixRequest::get('id_tag');
		if (_dao('copixtesthtmlbody')-> get(CopixRequest::get('id_test'),CopixRequest::get('id_tag'))) {
			$ppo->arData = _dao('copixtesthtmlbody')->get (CopixRequest::get('id_test'),CopixRequest::get('id_tag'));
		}
		return _arDirectPpo ($ppo, 'copixtesthtml.freeconfigure.ajax.php');
	}
	
	/**
	 * Sauvegarde d'un test et des balises
	 */
	public function processSave () {
		
		/*
		 * On récupère les données de configuration depuis la session
		 */
		$recordTest = CopixSession::get ('copixtesthtml|edit');
		$recordTestHTML = CopixSession::get ('copixtesthtml|configure');
		
		/*
		 * On écrase les données sur le header et les propriétés s'il y a une mise à jour
		 */
		if ($recordTest->id_test) {
			$params = _daoSP()->addCondition('id_test', '=', $recordTest->id_test);
			_dao('copixtesthtml')->deleteby($params);
			_dao('copixtesthtmlheader')->deleteby($params);
		}
		
		/*
		 * Procédure d'enregistrement des tests libres
		 */
		foreach (_request('freetest') as $key => $id) {
			if (CopixRequest::get('activation_' . $id) == 'yes' && isset($id)) {
				$record = _record ('copixtesthtmlbody');
				$record->id_test = $recordTest->id_test;
				$record->id_tag = $id;
				$record->path_tag = CopixRequest::get('path_' . $id);
				$record->name_tag = CopixRequest::get('name_' . $id);
				$record->attributes_tag = CopixRequest::get('attributes_' . $id);
				$record->contains = CopixRequest::get('contains_' . $id);
				$record->checkType = CopixRequest::get('checktype_' . $id);
				$record->validType = CopixRequest::get('validType_' . $id);
				
				if (_dao('copixtesthtmlbody')-> get($record->id_test, $id)) {
					_dao('copixtesthtmlbody')-> update ($record);
				} else {
					_dao('copixtesthtmlbody')-> insert ($record);
				}
			} else if (CopixRequest::get('activation_' + $recordTest->id_test) == false && _dao('copixtesthtmlbody')->get ($recordTest->id_test , $id)) {
				$parameters = _daoSp()->addCondition ('id_tag', '=', $id)
									  ->addCondition ('id_test', '=', $recordTest->id_test);
				_dao ('copixtesthtmlbody')->deleteby ($parameters);
			}
		}
		
		/*
		 * On vérifie si on doit faire une mise à jour ou une nouvelle enregistrement dans la base
		 *  de données 'copixtestest'
		 */
		if ($recordTest->id_test) {
			$id = $recordTest->id_test;
			$recordTest->id_test = $id;
			$recordTestHTML->id_test = $id;
			_dao('copixtesttest')->update ($recordTest);
			_dao('copixtesthtml')->insert ($recordTestHTML);
		} else {
			$record = _dao('copixtesttest')->insert ($recordTest);
			$id = $recordTest->id_test;
			$recordTestHTML->id_test = $id;
			_dao('copixtesthtml')->insert ($recordTestHTML);
		}

			/*
			 * On enregistre chaque ligne du HEADER que l'on veut vérifier dans la base de données
			 */
		foreach (_request('header') as $key => $value) {
		$recordTestHTMLHeader = new stdClass();
		$recordTestHTMLHeader->id_test = $id;
		$recordTestHTMLHeader->id_mark = $key;
		$recordTestHTMLHeader->value_mark = $value;
			if($value !== null) {
				_dao('copixtesthtmlheader')->insert ($recordTestHTMLHeader);
			}
		}
		
		/*
		 * Traitement du BODY avec la librairie php_tidy
		 */
	if (CopixRequest::get('body')) {
		foreach (CopixRequest::get('body') as $tag) {
			$tags[] = explode('|', $tag);
		}
		
		
		foreach ($tags as $key => $value) {
			
			$recordTestHTMLBody = new stdClass ();
			$recordTestHTMLBody->id_test = $id;
			$recordTestHTMLBody->id_tag = $value[0];
			$recordTestHTMLBody->path_tag = $value[1];
			$recordTestHTMLBody->name_tag = $value[2];
			$recordTestHTMLBody->attributes_tag = $value[3];
			$recordTestHTMLBody->contains = $value[4];
			$recordTestHTMLBody->checkType = CopixRequest::get('checktype_'.$value[0]);
			if ($recordTestHTMLBody->checkType !== null && $recordTestHTMLBody->checkType !== 'notest') {
				if (_dao('copixtesthtmlbody')->get ($id, $value[0])) {
					_dao('copixtesthtmlbody')->update($recordTestHTMLBody);
				} else {
					_dao('copixtesthtmlbody')->insert($recordTestHTMLBody);
				}
			} elseif ($recordTestHTMLBody->checkType == 'notest') {
				$parameters = _daoSP()->addCondition('id_tag', '=', $value[0]);
				_dao('copixtesthtmlbody')->deleteby ($parameters);
			}

		}
	}
			CopixSession::delete('copixtesthtml|configure');
			CopixSession::delete('copixtesthtml|edit');
			
			return _arRedirect (_url ('copixtest|admin|'));
		}
	
		/**
		 * Suppression d'un test URL et des balises
		 */
	public function processDelete () {
 	 	CopixRequest::assert('id_test');
 	 	$record = _ioDAO ('copixtesttest')->get(CopixRequest::get('id_test'));

		if($record == false) {
 	 		return _arRedirect(_url('copixtest|admin|'));
		} else {
			switch ($record->type_test){
				case "html" :
						$criteres =  _daoSp ()->addCondition('id_test','=',$record->id_test);
   						_ioDAO ('copixtesttest')->deleteBy ($criteres);
						_ioDAO ('copixtesthtml')->deleteBy ($criteres);
						_ioDAO ('copixtesthtmlheader')->deleteBy ($criteres);
						_ioDAO ('copixtesthtmlbody')->deleteBy ($criteres);
						break;
				default:
						break;
				}
		}
		return _arRedirect(_url('copixtest|admin|'));
	}
	
	/**
	 * Annulation d'une configuration de test d'URL
	 */
	public function processCancel () {
		CopixSession::delete('copixtesthtml|configure');
		CopixSession::delete('copixtesthtml|edit');
		return _arRedirect(_url('copixtest|admin|default'));
	}
}
?>