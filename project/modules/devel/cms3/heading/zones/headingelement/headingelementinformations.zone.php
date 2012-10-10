<?php
/**
 * @package cms
 * @subpackage heading
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU General Lesser Public Licence, see LICENCE file
 * @author Gérald Croës
 */

/**
 * Affichage des éléments
 *
 * @package cms
 * @subpackage heading
 */
class ZoneHeadingElementInformations extends CopixZone {
	/**
	 * Création du contenu
	 *
	 * @param <type> $toReturn HTML à afficher
	 * @return boolean
	 */
	protected function _createContent (&$toReturn) {
		$zones = array ();
		$record = $this->getParam ('record');
		$uniqId = uniqid ();
		foreach (_ioClass ('HeadingElementInformationServices')->getInformationsEditors () as $zone) {
			if (!$zone['mode'] || $zone['mode'] == CopixUserPreferences::get('heading|cms_mode')){
				$result = CopixZone::process ($zone['zoneid'], array ('record' => $record, 'uniqId' => $uniqId));
				if (is_array ($result)) {
					$zones = array_merge ($zones, $result);
				}
			}
		}
		ksort ($zones);
		$tpl = new CopixTPL ();
		$tpl->assign ('zones', $zones);
		$tpl->assign ('uniqId', $uniqId);
		$tpl->assign ('record', $record);
		$tpl->assign ('uniqueElement', !is_array ($record));
		if (is_array ($record)) {
			$rightsToSave = true;
			foreach ($record as $right) {
				if (!HeadingElementCredentials::canModerate ($right->public_id_hei)) {
					$rightsToSave = false;
					break;
				}
			}
		} else {
			$rightsToSave = HeadingElementCredentials::canModerate ($record->public_id_hei);
		}
		$tpl->assign ('rightsToSave', $rightsToSave);
		$toReturn = $tpl->fetch ('heading|informations/headingelementinformations.php');
		return true;
	}
}