<?php
/**
 * @package     standard
 * @subpackage  test
 * @author		Croës Gérald
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Actions pour définir des tests fonctionnels
 *
 * @package standard
 * @subpackage test
 */
class ActionGroupAdmin extends CopixActionGroup {
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
	 * Affichage des tests existants, par groupe
	 */
	public function processDefault (){
		$ppo = new CopixPpo (array ('TITLE_PAGE'=>_i18n ('test.messages.admintest')));
		_notify ('breadcrumb', array (
			'path' => array ('#' => _i18n ('test.messages.admintest'))
		));
		
		$ppo->arTests = _dao ('test')->findAll ();
		$ppo->arErrors = _request('errors');
		foreach ($ppo->arTests as $id => $value) {
			$value->category_test = _dao('testcategory')->get($value->category_test)->caption_ctest;
			$value->level_test = _dao('testlevel')->get($value->level_test)->caption_level;
		}

		$ppo->arLevels = _dao ('testlevel')->findAll ();
		$ppo->level = array ();
		foreach ($ppo->arLevels as $value) {
			$ppo->level[$value->caption_level] = '<b>'.'Niveau : '.'<font color="red">'.$value->id_level.' </font>('.$value->caption_level.')<br>'.'Contact : '.$value->email.'</b>';
		}

		$ppo->arTypeTest = _ioClass ('TestFactory')->getTypeList ();
		return _arPpo ($ppo, 'tests.list.tpl');
	}

	/**
	 * Demande de création d'un élément
	 */
	public function processCreate () {
		if (! in_array (_request ('type'), _ioClass ('TestFactory')->getTypeList ())){
			return _arRedirect (_url ('admin|'));
		}
		return _arRedirect (_url ('test_'._request ('type').'|admin|create', array ('type'=>_request ('type'))));
	}

	/**
	 * Demande la supression d'un test à partir de son identifiant
	 */
	public function processDelete () {
		if (_dao ('test')->get (_request ('id')) !== false) {
			if (! _request ('confirm', false, true)) {
				return CopixActionGroup::process ('generictools|Messages::getConfirm',
				array ('message'=>_i18n('test.delete.confirm').' "'._dao ('test')->get (_request ('id'))->caption_test.'" ?',
			   'confirm'=>_url ('test|admin|delete', array ('confirm'=>1, 'id'=>_request('id'), 'type'=>_request('type'))),
			   'cancel'=>_url ('test|admin|default')));
			} else {
				return _arRedirect (_url ('test_'._request ('type').'|admin|delete', array ('id_test'=>_request('id'))));
			}
		}else{
			throw new CopixTestNotFoundException(_request ('id'));
		}
	}

	/**
	 * Edition d'un test : Fait appel à la page de configuration du test
	 * dans le module auquel il est rattaché
	 */
	public function processEdit (){
		if(_dao('test')->get(_request ('id')) !== false) {
			return _arRedirect (_url ('test_'._request ('type').'|admin|edit', array ('id_test'=>_request('id'))));
		}
	}
}