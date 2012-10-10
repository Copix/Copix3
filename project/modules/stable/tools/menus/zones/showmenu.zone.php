<?php
/**
 * @package tools
 * @subpackage menus
 * @copyright CopixTeam
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 * @author Steevan BARBOYON
 * @link http://www.copix.org
 */

/**
 * Retourne l'HTML du menu demandé
 *
 * @package tools
 * @subpackage menus
 */
class ZoneShowMenu extends CopixZone {
	/**
	 * Création du contenu HTML
	 *
	 * @param string $toReturn HTML à retourner
	 * @return boolean
	 */
	public function _createContent (&$toReturn) {
		$tpl = new CopixTPL ();
		try {
			$menu = MenusService::get ($this->getParam ('id', 'main'));
		} catch (Exception $e) {
			return false;
		}
		$tpl->assign ('menu', MenusService::get ($this->getParam ('id', 'main')));
		$toReturn = $tpl->fetch ($this->getParam ('template', 'menus|ulli.php'));
		return true;
	}
}