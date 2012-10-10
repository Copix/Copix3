<?php
/**
 * @package cms
 * @subpackage heading
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU General Lesser Public Licence, see LICENCE file
 * @author Steevan BARBOYON
 */

/**
 * Titre pour les informations d'un élément
 *
 * @package cms
 * @subpackage heading
 */
class ZoneHeadingElementInformationTitle extends CopixZone {
	/**
	 * Création du contenu
	 *
	 * @param string $toReturn HTML à retourner
	 * @return boolean
	 */
	protected function _createContent (&$toReturn) {
		$tpl = new CopixTPL ();
		$tpl->assign ('title', $this->getParam ('title'));
		$tpl->assign ('icon', $this->getParam ('icon'));
		$tpl->assign ('id', $this->getParam ('id'));
		$toReturn = $tpl->fetch ('heading|informations/title.php');
	}
}