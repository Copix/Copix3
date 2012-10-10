<?php
/**
 * @package cms
 * @subpackage heading
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * @author Steevan BARBOYON
 */

/**
 * Options d'affichage pou rle dashboard
 *
 * @package cms
 * @subpackage heading
 */
class ZoneDBScreenOptions extends CopixZone {
	/**
	 * Création du contenu
	 *
	 * @param string $pToReturn
	 * @return boolean
	 */
	protected function _createContent (&$pToReturn) {
		$tpl = new CopixTPL ();
		$tpl->assign ('cols', CopixUserPreferences::get ('heading|dashBoardColumns'));
		$pToReturn = $tpl->fetch ('heading|dashboard/screenoptions.php');
		return true;
	}
}