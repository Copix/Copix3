<?php
/**
 * @package standard
 * @subpackage admin
 * @author Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Facilite la gestion des préférences
 *
 * @package standard
 * @subpackage admin
 */
class ActionGroupUserPreferences extends CopixActionGroup {
	/**
	 * Liste des utilisateurs ayant des préférences
	 *
	 * @return CopixActionReturn
	 */
	public function processDefault () {
		$ppo = ToolsAdmin::setPage (ToolsAdmin::PAGE_USERPREFERENCES_USERS);
		$user = _currentUser ();

		// pas logué
		if (!$user->testCredential ('basic:registered')) {
			throw new CopixCredentialException ('basic:registered');
		}
		
		$ppo->users = CopixUserPreferences::getUsers (!_currentUser ()->testCredential ('basic:admin'));

		if (count ($ppo->users) == 1) {
			return _arRedirect (_url ('admin|userpreferences|modules', array ('user' => $ppo->users[0]['user'], 'userhandler' => $ppo->users[0]['userhandler'])));
		}

		return _arPPO ($ppo, 'userpreferences/users.php');
	}

	/**
	 * Liste des modules qui ont des préférences
	 *
	 * @return CopixActionReturn
	 */
	public function processModules () {
		CopixRequest::assert ('user', 'userhandler');
		$ppo = ToolsAdmin::setPage (ToolsAdmin::PAGE_USERPREFERENCES_MODULES, array ('user' => _request ('user'), 'userhandler' => _request ('userhandler')));
		$ppo->user = _request ('user');
		$ppo->userhandler = _request ('userhandler');
		$ppo->highlight = _request ('highlight');

		$preferences = CopixUserPreferences::getList ();
		$ppo->modules = array ();
		foreach ($preferences as $group) {
			foreach ($group->getList () as $preference) {
				$module = substr ($preference->getName (), 0, strpos ($preference->getName (), '|'));
				$infos = CopixModule::getInformations ($module);
				$groupId = $infos->getGroup ()->getId ();
				if (!isset ($ppo->modules[$groupId]['modules'][$module])) {
					$ppo->modules[$groupId]['caption'] = $infos->getGroup ()->getCaption ();
					$ppo->modules[$groupId]['modules'][$module] = $infos;
				}
			}
		}
		return _arPPO ($ppo, 'userpreferences/modules.php');
	}

	/**
	 * Modification des préférences
	 *
	 * @return CopixActionReturn
	 */
	public function processEdit () {
		CopixRequest::assert ('user', 'userhandler', 'modulePref');
		$ppo = ToolsAdmin::setPage (ToolsAdmin::PAGE_USERPREFERENCES_EDIT, array ('user' => _request ('user'), 'userhandler' => _request ('userhandler'), 'modulePref' => _request ('modulePref')));
		$ppo->user = _request ('user');
		$ppo->userhandler = _request ('userhandler');
		$ppo->modulePref = _request ('modulePref');
		return _arPPO ($ppo, 'userpreferences/edit.php');
	}

	/**
	 * Sauvegarde les préférences
	 *
	 * @return CopixActionReturn
	 */
	public function processSave () {
		CopixRequest::assert ('user', 'userhandler');
		$user = _request ('user');
		$userhandler = _request ('userhandler');
		$modulePref = _request ('modulePref');
		ToolsAdmin::setPage (ToolsAdmin::PAGE_USERPREFERENCES_DO_EDIT, array ('user' => $user, 'userhandler' => $userhandler, 'modulePref' => $modulePref));

		foreach (CopixRequest::asArray () as $name => $value) {
			if (substr ($name, 0, 5) == 'pref_') {
				CopixUserPreferences::set (substr ($name, 5), $value, $user, $userhandler);
			}
		}

		if (CopixRequest::isAJAX ()) {
			return _arNone ();
		}
		
		// message de confirmation
		if (_request ('confirmMessage') != 'false') {
			$params = array (
				'title' => _i18n ('userpreferences.title.confirmSaved'),
				'redirect_url' => _request ('redirect'),
				'continue' => _request ('redirect'),
				'message' => _i18n ('userpreferences.confirmSaved')
			);
			return CopixActionGroup::process ('generictools|messages::getInformation', $params);
			
		// redirection directe
		} else {
			return _arRedirect (_request ('redirect'));
		}
	}

	/**
	 * Définit la valeur d'une préférence
	 *
	 * @return CopixActionReturn
	 */
	public function processSet () {
		CopixRequest::assert ('name', 'value');
		CopixUserPreferences::set (_request ('name'), _request ('value'), _request ('user'), _request ('userhandler'));
		if (CopixRequest::isAJAX ()) {
			return _arNone ();
		} else {
			$url = (CopixRequest::exists ('url')) ? _url (_request ('url')) : $_SERVER['HTTP_REFERER'];
			return _arRedirect ($url);
		}
	}
}