<?php
/**
 * @package standard
 * @subpackage admin
 * @copyright CopixTeam
 * @license lgpl
 * @author Steevan BARBOYON
 */

/**
 * Affiche le tag error si on a des modules à mettre à jour
 *
 * @package standard
 * @subpackage admin
 */
class ZoneModulestoUpdate extends CopixZone {
	protected function _createContent (&$pToReturn) {
		$updates = array ();
		foreach (CopixModule::getList () as $module) {
			$infos = CopixModule::getInformations ($module);
			if ($infos->getVersion () != $infos->getInstalledVersion ()) {
				$updates[$infos->getName ()] = '[' . $infos->getName () . '] ' . $infos->getDescription () . ' (' . $infos->getInstalledVersion () . ' vers ' . $infos->getVersion () . ')';
			}
		}
		if (count ($updates) > 0) {
			$pToReturn = $this->_usePPO (_ppo (array ('updates' => $updates)), 'module/toupdate.php');
		}
		return true;
	}
}