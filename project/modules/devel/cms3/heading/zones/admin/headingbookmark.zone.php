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
 * Ligne d'un favori
 *
 * @package cms
 * @subpackage heading
 */
class ZoneHeadingBookmark extends CopixZone {
	/**
	 * Création du contenu
	 *
	 * @param string $pToReturn
	 * @return boolean
	 */
	protected function _createContent (&$pToReturn) {
		$tpl = new CopixTPL ();
		$tpl->assign ('element', $this->getParam ('element'));
		$tpl->assign ('treeId', $this->getParam ('treeId'));
		$tpl->assign ('filters', $this->getParam ('filters'));
		$pToReturn = $tpl->fetch ('heading|admin/headingbookmark.php');
		return true;
	}
}