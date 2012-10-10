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
 * Facilite la gestion des préférences de groupe
 *
 * @package standard
 * @subpackage admin
 */
class ActionGroupGroupPreferences extends CopixActionGroup {
	/**
	 * Executée avant toute action
	 *
	 * @param string $pAction Nom de l'action
	 */
	protected function _beforeAction ($pAction) {
		_currentUser ()->assertCredential ('basic:admin');
	}

	/**
	 * Liste des groupes ayant des préférences
	 *
	 * @return CopixActionReturn
	 */
	public function processDefault () {
		$ppo = ToolsAdmin::setPage (ToolsAdmin::PAGE_GROUPPREFERENCES_GROUPS);
		$ppo->groups = CopixGroupHandlerFactory::getAllGroupList ();
		return _arPPO ($ppo, 'grouppreferences/groups.php');
	}

	/**
	 * Liste des groupes qui ont des préférences
	 *
	 * @return CopixActionReturn
	 */
	public function processModules () {
		CopixRequest::assert ('groupName', 'grouphandler');
		$ppo = ToolsAdmin::setPage (ToolsAdmin::PAGE_GROUPPREFERENCES_MODULES, array ('groupName' => _request ('groupName'), 'grouphandler' => _request ('grouphandler')));
		$ppo->groupName = _request ('groupName');
		$ppo->grouphandler = _request ('grouphandler');

		$preferences = CopixGroupPreferences::getList ();
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
		return _arPPO ($ppo, 'grouppreferences/modules.php');
	}

	/**
	 * Modification des préférences
	 *
	 * @return CopixActionReturn
	 */
	public function processEdit () {
		CopixRequest::assert ('groupName', 'grouphandler', 'modulePref');
		$ppo = ToolsAdmin::setPage (ToolsAdmin::PAGE_GROUPPREFERENCES_EDIT, array ('groupName' => _request ('groupName'), 'grouphandler' => _request ('grouphandler'), 'modulePref' => _request ('modulePref')));
		$ppo->groupName = _request ('groupName');
		$ppo->grouphandler = _request ('grouphandler');
		$ppo->modulePref = _request ('modulePref');
		$ppo->preferences = CopixGroupPreferences::getList ($ppo->modulePref, false, $ppo->groupName, $ppo->grouphandler);
		return _arPPO ($ppo, 'grouppreferences/edit.php');
	}

	/**
	 * Sauvegarde les préférences
	 *
	 * @return CopixActionReturn
	 */
	public function processSave () {
		CopixRequest::assert ('groupName', 'grouphandler');
		$group = _request ('groupName');
		$grouphandler = _request ('grouphandler');
		$modulePref = _request ('modulePref');
		ToolsAdmin::setPage (ToolsAdmin::PAGE_GROUPPREFERENCES_DO_EDIT, array ('groupName' => $group, 'grouphandler' => $grouphandler, 'modulePref' => $modulePref));

		foreach (CopixRequest::asArray () as $name => $value) {
			if (substr ($name, 0, 5) == 'pref_') {
				CopixGroupPreferences::set (substr ($name, 5), $value, $group, $grouphandler);
			}
		}
		
		// message de confirmation
		$params = array (
			'title' => _i18n ('grouppreferences.title.confirmSaved'),
			'redirect_url' => _url ('admin|grouppreferences|'),
			'continue' => _url ('admin|grouppreferences|'),
			'message' => _i18n ('grouppreferences.confirmSaved')
		);
		return CopixActionGroup::process ('generictools|messages::getInformation', $params);
	}
}