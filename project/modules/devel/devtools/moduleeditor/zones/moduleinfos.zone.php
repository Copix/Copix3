<?php
/**
 * @package devtools
 * @subpackage moduleeditor
 * @copyright CopixTeam
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 * @author Steevan BARBOYON
 * @link http://www.copix.org
 */

/**
 * Affiche la partie informations sur le module
 * @package devtools
 * @subpackage moduleeditor
 */
class ZoneModuleInfos extends CopixZone {
	function _createContent (&$toReturn) {
		
        $tpl = new CopixTpl ();
		$tpl->assign ('arModulePaths', CopixConfig::instance ()->arModulesPath);
		$arModuleGroups = CopixModule::getGroupList ();
        if (isset ($arModuleGroups)) {
            foreach ($arModuleGroups as $groupId => $groupInfos) {
                $arModuleGroups[$groupId] = '[' . $groupId . '] ' . $groupInfos->caption;
            }
        }
		$tpl->assign ('arModuleGroups', $arModuleGroups);
		$toReturn = $tpl->fetch ('moduleinfos.zone.tpl');
		return true;
	}
}
?>