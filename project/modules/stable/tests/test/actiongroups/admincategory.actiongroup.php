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
 * Actions pour définir des catégories de tests fonctionnels
 *
 * @package standard
 * @subpackage test
 */
class ActionGroupAdminCategory extends CopixActionGroup {
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
	 * Affichage en liste des catégories
	 */
	public function processDefault () {
		_notify ('breadcrumb', array (
			'path' => array ('#' => _i18n ('test.categories.title'))
		));		
		
		$ppo = _ppo (array ('TITLE_PAGE'=>_i18n('test.categories.title')));
		$ppo->arErrors = _request('errors');
		$ppo->categories = _dao ('testcategory')->findAll ();
		return _arPpo ($ppo, 'categories.list.tpl');
	}
	
	/**
	 * Modification d'une catégorie dans la liste
	 */
	public function processEdit () {
		$ppo = new CopixPpo ();
		$record = CopixSession::get('category|edit');
		$parameters = _daoSP();
		$ppo->novalue = _dao('testcategory')->countBy ($parameters);
		$ppo->categories = _dao ('testcategory')->findAll ();
		if(_request('idc')) {
			$ppo->edit = _dao('testcategory')->get(_request('idc'));
			CopixSession::set('category|edit', $ppo);
		}
		return _arPpo($ppo, 'categories.list.tpl');
	}
	
	/**
	 * Enregistrement d'une catégorie
	 */
	public function processSave () {
		$edited = CopixSession::get('category|edit');
		if (_request('idc')) {
			$toUpdate = _record('testcategory');
			$toUpdate->id_ctest = _request('idc');
			$toUpdate->caption_ctest = _request ('caption');
			_dao('testcategory')->update ($toUpdate);
			CopixSession::delete('category|edit');
		} else {
			$record = _record ('testcategory');
			$record->caption_ctest = htmlspecialchars(_request ('caption_ctest'));
			_dao('testcategory')->insert ($record);
		}
		
		CopixSession::delete ('category|edit');
		return _arRedirect(_url ('admincategory|default'));
	}
	
	/**
	 * Suppression d'une catégorie
	 */
	public function processDelete () {	
		$params = _daoSP();
		$params2 = _daoSP()->addCondition('id_ctest', '=', _request('idc'));
		if (_dao('testcategory')->countBy($params) == '1') {	
			return _arRedirect (_url ('admincategory|default',
			array('errors'=>_i18n('test.errors.category.one'))));
		} elseif (_dao('test')->countBy($params2) !== '0') {
			$ppo = new CopixPpo();
			$ppo->arCategories = _dao('testcategory')->findAll();
			$ppo->idc = _request('idc');
			return _arPpo ($ppo, 'category.refactor.tpl');
		} else {
			if (! _request ('confirm', false, true)){
			   return CopixActionGroup::process ('generictools|Messages::getConfirm',
			   array ('message'=>_i18n('test.delete.confirm').' "'._dao('testcategory')->get (_request('idc'))->caption_ctest.'" ?',
			   'confirm'=>_url ('admincategory|delete', array ('confirm'=>1, 'idc'=>_request('idc'))),
			   'cancel'=>_url ('adminCategory|default')));
		  }	else {
				$deleteParameters = _daoSP()->addCondition('id_ctest', '=', _request('idc'));
				_dao ('testcategory')->deleteby ($deleteParameters);
		  }
		}
		return _arRedirect (_url ('admincategory|default'));
	}
	
	/**
	 * Méthode pour rédiger les tests vers une autre catégorie pour en supprimer une
	 */
	public function processRefactor () {
		$idc = CopixRequest::get('idc');
		$newidc = _request('new');
		$parameters = _daoSP()->addCondition('id_ctest', '=', $idc);
		$search = _dao('test')->findBy($parameters);
		$deleteParameters = _daoSP()->addCondition('id_ctest', '=', $idc);
		foreach ($search as $value) {
			$newTest = $value;
			$newTest->id_ctest = (int) $newidc;
			_dao('test')->update($newTest);
			_dao('testcategory')->deleteby($deleteParameters);
		}
		return _arRedirect (_url ('admincategory|default'));
	}
	
	/**
	 * Annulation d'une saisie
	 */
	public function processCancel () {
		CopixSession::delete('category|edit');
		return _arRedirect(_url('admincategory|default'));
	}
}