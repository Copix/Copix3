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
class ZoneInfos extends CopixZone {
	function _createContent (&$toReturn) {
		$tpl = new CopixTpl ();
		$tpl->assign ('modulePaths', CopixConfig::instance ()->arModulesPath);
		$toReturn = $tpl->fetch ('infos.zone.tpl');
		return true;
	}
}
?>