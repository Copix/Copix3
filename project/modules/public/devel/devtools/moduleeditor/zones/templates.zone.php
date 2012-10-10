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
 * Affiche la partie création d'actiongroups
 * @package devtools
 * @subpackage moduleeditor
 */
class ZoneTemplates extends CopixZone {
	function _createContent (&$toReturn) {
		$tpl = new CopixTpl ();
		$toReturn = $tpl->fetch ('templates.zone.tpl');
		return true;
	}
}
?>