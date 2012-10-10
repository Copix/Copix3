<?php
/**
 * @package standard
 * @subpackage test
 * @author		Julien Alexandre
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Actions pour définir des niveaux de tests fonctionnels
 *
 * @package standard
 * @subpackage test
 */
class ActionGroupAdminLevel extends CopixActionGroup {
	/**
	 * Droits d'administrateur requis
	 */
	public function beforeAction ($pAction){
		_currentUser ()->assertCredential ('basic:admin');
		_notify ('breadcrumb', array (
			'path' => array ('admin||' => _i18n ('admin|breadcrumb.admin'))
		));
	}
	
	/**
	 *  Affichage d'une liste des niveaux de criticités avec les paramètres
	 */
	public function processDefault () {
		_notify ('breadcrumb', array (
			'path' => array ('#' => _i18n('test.level.title'))
		));

		$ppo = _ppo (array ('TITLE_PAGE'=>_i18n('test.level.title'), 
		                    'arErrors'=>_request('errors')));
		$ppo->arData = _dao('testlevel')->findAll ();
		return _arPpo ($ppo, 'level.list.tpl');
	}
	
	/**
	 * On fait un _record en cas de création d'un nouveau niveau de criticité
	 */
	public function processCreate () {
		$record = _record ('testlevel');
		CopixSession::set('testlevel|edit', $record);
		return $this->processEdit();
	}

	/**
	 * On affiche dans le formulaire les variables déjà enregistrée dans la base de données
	 *  si on effectue une modification
	 */
	public function processEdit () {
		$ppo = new CopixPpo();
		$ppo->TITLE_PAGE = _i18n('test.level');
		
		if (_request('id')) {
			$record = new stdClass();
			$record->id_level = _request('id');
			CopixSession::set('testlevel|edit', $record);
			$ppo->arData = _dao('testlevel')->get(_request('id'));
		}
		return _arPpo($ppo, 'level.form.tpl');
	}

	/**
	 * Sauvegarde d'un nouvel enregistrement ou des modifications
	 */
	public function processSave () {
		
		// On récupère les paramètres
		$record = CopixSession::get('testlevel|edit');
		$record->caption_level = _request('caption_level');
		if (_request('email')) {
		$record->email = _request('email');
		} else {
			$record->email = null;
		}
		if ($record->id_level == null) {
			_dao('testlevel')->insert ($record);
		} else {
			_dao('testlevel')->update ($record);
		}
		CopixSession::delete('testlevel|edit');
		return _arRedirect(_url('AdminLevel|default'));
	}
	
	/**
	 * Suppression de d'un niveau de criticité
	 */
		public function processDelete () {
		$params = _daoSP();
		$params2 = _daoSP()->addCondition('level_test', '=', _request('id'));

		if (_dao('testlevel')->countBy($params) == '1') {
			return _arRedirect (_url ('adminlevel|default', 
			array('errors'=>_i18n('test.errors.level.one'))));
		} elseif (_dao('test')->countBy($params2) !== '0') {
			$ppo = new CopixPpo();
			$ppo->arLevels = _dao('testlevel')->findAll();
			$ppo->id = _request('id');
			return _arPpo ($ppo, 'level.refactor.tpl');
		} else {
		if (! _request ('confirm', false, true)){
		   return CopixActionGroup::process ('generictools|Messages::getConfirm',
		   array ('message'=>_i18n('test.delete.confirm').' "'._dao('testlevel')->get (_request('id'))->caption_level.'" ?',
		   'confirm'=>_url ('adminlevel|delete', array ('confirm'=>1, 'id'=>_request('id'))),
		   'cancel'=>_url ('adminlevel|default')));
		  } else {
			_dao ('testlevel')->delete (_request('id'));
		  }
		}
		return _arRedirect (_url ('adminlevel|default'));
	}
	
	/**
	 * Méthode redirection en cas de suppression d'un niveau qui est déjà utilisé
	 */
	public function processRefactor () {
		$id = CopixRequest::get('id');
		$newid = _request('new');
		$parameters = _daoSP()->addCondition('level_test', '=', $id);
		$search = _dao('test')->findBy($parameters);
		foreach ($search as $value) {
			$newTest = $value;
			$newTest->level_test = (int) $newid;
			_dao('test')->update($newTest);
		}
		return _arRedirect (_url ('adminlevel|default'));
	}
	
	/**
	 * Annulation d'une configuration
	 */
	public function processCancel () {
		CopixSession::delete('testlevel|edit');
		return _arRedirect(_url('adminlevel|default'));
	}
}