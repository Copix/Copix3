<?php
/**
 * @package standard
 * @subpackage admin
 * @author Bertrand Yan, Croes Gérald
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Liste des modules disponibles pour installation
 *
 * @package standard
 * @subpackage admin
 */
class ZoneCustomisedInstall extends CopixZone {
	/**
	 * Création du contenu de la page
	 *
	 * @param string $toReturn Contenu à retourner
	 */
	protected function _createContent (& $toReturn) {
		$tpl = new CopixTpl ();
		$tpl->assign ('arModulesPath', CopixConfig::instance ()->arModulesPath);
		$modules = _class ('admin|InstallService')->getModules ();
		$groupsInstalled = array ();
		$groupsAvailables = array ();
		foreach ($modules as $status => $groups) {
			if ($status == 'installed') {
				$groupToEdit = &$groupsInstalled;
			} else {
				$groupToEdit = &$groupsAvailables;
			}
			foreach ($groups as $groups) {
				foreach ($groups as $module) {
					$group = $module->getGroup ();
					if (!array_key_exists ($group->getId (), $groupToEdit)) {
						$groupToEdit[$group->getId ()] = array ('caption' => $group->getCaption (), 'count' => 0);
					}
					$groupToEdit[$group->getId ()]['count'] += 1;
				}
			}
		}
		$tpl->assign ('groupsInstalled', $groupsInstalled);
		$tpl->assign ('groupsAvailables', $groupsAvailables);
		$tpl->assign ('arModules', $modules);
		$toReturn = $tpl->fetch ('module/modules.list.tpl');
	}
}