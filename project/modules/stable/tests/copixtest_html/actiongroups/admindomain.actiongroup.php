<?php
class ActionGroupAdminDomain extends CopixActionGroup {
	
	/**
	 * Administration des domaines 
	 * Affichage des domaines
	 * @return unknown
	 */
	public function processDefault () {
		$ppo = new CopixPpo ();
		$ppo->TITLE_PAGE = _i18n('copixtest_html.domain.title');
		$ppo->arErrors = _request('errors');
		$ppo->arData = _dao('copixtesthtmldomain')->findAll();
		return _arPpo ($ppo, 'admindomain.view.tpl');
	}
	
	/**
	 * Création d'un nouveau domaine
	 *
	 * @return unknown
	 */
	public function processCreate () {
		$record = _record ('copixtesthtmldomain');
		CopixSession::set('copixtesthtmldomain|edit', $record);
		return $this->processEdit();
	}
	
	/**
	 * Edition d'un domaine
	 *
	 * @return unknown
	 */
	public function processEdit () {
		$ppo = new CopixPpo ();
		$record = CopixSession::get('copixtesthtmldomain|edit');
		$ppo->domains = _dao ('copixtesthtmldomain')->findAll ();
		if(_request('id')) {
			$ppo->edit = _dao('copixtesthtmldomain')->get(_request('id'));
			CopixSession::set('copixtesthtmldomain|edit', $ppo->edit);
		}
		return _arPpo($ppo, 'admindomain.edit.tpl');
	}
	
	/**
	 * Sauvegarde d'un domaine
	 *
	 * @return unknown
	 */
	public function processSave () {
		
		$record = CopixSession::get('copixtesthtmldomain|edit');
		$parameters = _daoSp()->addCondition('url_domain','=',$record->url_domain);
		$record->caption_domain = _request('caption_domain');
		$record->url_domain = _request('url_domain');
		if (_dao('copixtesthtmldomain')->get ($record->url_domain) == false) {
			_dao('copixtesthtmldomain')->deleteBy ($parameters);
			_dao('copixtesthtmldomain')->insert ($record);
		} 
		CopixSession::delete('copixtesthtmldomain|edit');
		return _arRedirect(_url('admindomain|default'));
	}
	
	/**
	 * Suppression de d'un domaine
	 */
		public function processDelete () {
		$params = _daoSP();
		if (! _request ('confirm', false, true)){
		   return CopixActionGroup::process ('generictools|Messages::getConfirm',
		   array ('message'=>_i18n('copixtest_html.delete.confirm').' "'._dao('copixtesthtmldomain')->get (_request('id'))->caption_domain.'" ?',
		   'confirm'=>_url ('admindomain|delete', array ('confirm'=>1, 'id'=>_request('id'))),
		   'cancel'=>_url ('admindomain|default')));
		  } else {
		  	$parameters = _daoSP()->addCondition ('url_domain', '=', _request('id'));
			_dao('copixtesthtmldomain')->deleteby ($parameters);
			}
		return _arRedirect (_url ('admindomain|default'));
	}
	
	
	/**
	 * Annulation d'une configuration
	 */
	public function processCancel () {
		CopixSession::delete('copixtestdomain|edit');
		return _arRedirect(_url('admindomain|default'));
	}
	
}
?>