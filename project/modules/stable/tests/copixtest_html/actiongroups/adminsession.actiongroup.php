<?php
/**
 * Gestion des sessions sur les tests de pages Web
 * Un profil de session contiendra un identifiant vers un test sur l'URL d'authentification
 * et un autre test sur l'URL de déconnexion
 * Les sites ayant le même profil de session seront lancés sur le même navigateur CURL
 * en commençant par la page d'authentification et en finissant par la page de déconnexion. 
 */
class ActionGroupAdminSession extends CopixActionGroup {
	
	/**
	 * Affichage des profils de sessions
	 */
	public function processDefault () {
		$ppo = new CopixPpo();
		$ppo->TITLE_PAGE = _i18n('copixtest_html.adminsession.maintitle');
		$arData = _dao('copixtesthtmlsession')->findAll();
		foreach ($arData as $key => $value) {
			$arData[$key]->login_session = _dao('copixtesttest')->get ($value->login_session)->caption_test;
			$arData[$key]->logout_session = _dao('copixtesttest')->get ($value->logout_session)->caption_test;
		}
		$ppo->arData = $arData;
		return _arPpo ($ppo, 'adminsession.view.tpl');
	}
	
	/**
	 * Création d'un profil de session
	 */
	public function processCreate () {
		$record = _record('copixtesthtmlsession');
		CopixSession::set('copixtestsession|edit', $record);
		return _arRedirect (_url ('adminsession|edit'));
	}
	
	/**
	 * Edition d'un profil de session
	 */
	public function processEdit () {
		$ppo = new CopixPpo() ;
		$ppo->TITLE_PAGE = _i18n('copixtest_html.adminsession.title_edit');
		if (CopixRequest::get('id') !== null) {
			$record = _dao('copixtesthtmlsession')->get (CopixRequest::get('id'));
			$record->login_session = _dao('copixtesthtml')->get ($record->login_session)->url;
			$record->logout_session = _dao('copixtesthtml')->get ($record->logout_session)->url;
		} else {
			$record = CopixSession::get('copixtestsession|edit');
		}
		$ppo = $record;
		return _arPpo($ppo, 'adminsession.edit.tpl');
	}
	
	/**
	 * Sauvegarde d'un profil de session
	 */
	public function processSave () {
		if (isset(CopixSession::get('copixtestsession|edit')->id_test)) {
			$id = CopixSession::get('copixtestsession|edit')->id_test;
		}
		if (isset($id)) {
			$record = _dao('copixtesthtmlsession')->get ($id);
		} else {
			$record = _record('copixtesthtmlsession');
		}
		$record->caption_session = CopixRequest::get('caption_session');
					
		if (CopixRequest::get('logout_session') !== null) {
			$parameters = _daoSp ()->addCondition ('url', '=', _request('logout_session'));
			$record->logout_session = _dao('copixtesthtml')->findBy ($parameters);
			$record->logout_session = $record->logout_session[0]->id_test;
		}
		$parameter = _daoSp ()->addCondition ('url', '=',str_replace('&amp;', '&',  _request('login_session')));
		$record->login_session = _ioDAO('copixtesthtml')->findBy ($parameter);
		$record->login_session = $record->login_session[0]->id_test;

		if (CopixRequest::get('id')) {
			_dao('copixtesthtmlsession')->update ($record);
		} else {
			_dao('copixtesthtmlsession')->insert ($record);
		}
		CopixSession::delete('adminsession|edit');
		return _arRedirect(_url ('adminsession|default'));
	}
	
	/**
	 * Suppression d'un profil de session
	 */
	public function processDelete () {
		if (! _request ('confirm', false, true)) {
		   return CopixActionGroup::process ('generictools|Messages::getConfirm',
		   array ('message'=>_i18n('copixtest_html.delete.confirm').' "'._dao('copixtesthtmlsession')->get (_request('id'))->caption_session.'" ?',
		   'confirm'=>_url ('adminsession|delete', array ('confirm'=>1, 'id'=>_request('id'))),
		   'cancel'=>_url ('adminsession|default')));
		  } else {
		  		$parameters = _daoSp()->addCondition ('id_session', '=', CopixRequest::get('id'));
		  		_dao('copixtesthtmlsession')->deleteBy ($parameters);
			}
		return _arRedirect (_url ('adminsession|default'));
	}
	
	/**
	 * Annulation d'une modification sur un profil de session
	 * Suppression des variables de sessions
	 */
	public function processCancel () {
		CopixSession::delete ('adminsession|edit');
		return _arRedirect (_url ('adminsession|default'));
	}
}
?>