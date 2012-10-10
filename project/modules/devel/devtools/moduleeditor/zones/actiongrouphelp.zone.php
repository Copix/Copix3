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
 * Affiche l'aide sur la création d'actiongroup
 * @package devtools
 * @subpackage moduleeditor
 */
class ZoneActionGroupHelp extends CopixZone {
	
	function _createContent (&$toReturn) {
		$tpl = new CopixTpl ();
		$toReturn = $tpl->fetch ('actiongroup.zone.tpl');
		return true;
	}
}
?>