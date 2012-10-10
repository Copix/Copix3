<?php
/**
 * @package		standard
 * @subpackage	admin
 * @author		Croës Gérald
 * @copyright	CopixTeam
 * @link		http://www.copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser Public Licence, see LICENCE file
 * @since		3.0.4 
 */

/**
 * Permet la récupération de tout ce qui concerne les liens d'administration des modules (links dans module.xml)
 * 
 * @package		standard
 * @subpackage	admin
 */
class AdminMenu {
	/**
	 * Retourne la liste des liens 
	 *
	 * @param array $pArModule Liste des modules dont on veut les liens, ils doivent être installés
	 * @return array
	 */
	public function getLinks ($pArModules = null) {
		//Si rien n'est passé, on récupère l'ensemble des modules installés
		if ($pArModules === null) {
			$pArModules = CopixModule::getList (); 
		} else {
			//Sinon on filtre par rapport aux modules installés
			foreach ($pArModules as $key => $name) {
				if (!CopixModule::isValid ($name)) {
					unset ($pArModules[$key]);
				}
			}
		}
		
		//Création des liens
		$links = array ();
		foreach ($pArModules as $moduleName) {
			$moduleInformations = CopixModule::getInformations ($moduleName);
			foreach ($moduleInformations->getAdminLinksGroups () as $id => $group) {
				$linksOk = array ();
				foreach ($group->getLinks () as $link) {
					if ($link->getCredentials () == null || _currentUser ()->testCredential ($link->getCredentials ())) {
						$linksOk[] = $link;
					}
				}
				if (count ($linksOk) > 0) {
					$links[$id]['caption'] = $group->getCaption ();
					$links[$id]['icon'] = $group->getIcon ();
					if (isset ($links[$id]['links'])) {
						$links[$id]['links'] = array_merge ($links[$id]['links'], $linksOk);
					} else {
						$links[$id]['links'] = $linksOk;
					}
				}
			}
		}

		return $links;
	}
}