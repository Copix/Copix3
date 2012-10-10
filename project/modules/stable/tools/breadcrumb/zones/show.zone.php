<?php
/**
 * @package tools
 * @subpackage breadcrumb
 * @copyright CopixTeam
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 * @author Steevan BARBOYON
 * @link http://www.copix.org
 */

/**
 * Retourne les informations sur le fil d'ariane indiqué vi l'événement breadcrumb
 * 
 * @package tools
 * @subpackage breadcrumb
 */
class ZoneShow extends CopixZone {
	/**
	 * Création du contenu HTML
	 *
	 * @param string $toReturn HTML à retourner
	 * @return boolean
	 */
	public function _createContent (&$toReturn) {
		$id = $this->getParam ('id', 'default');
		$tpl = new CopixTPL ();
		$tpl->assign ('links', _ioClass ('breadcrumb|breadcrumb')->get ($id));
		$tpl->assign ('showLastLink', _ioClass ('breadcrumb|breadcrumb')->getShowLastLink ($id));
		$toReturn = $tpl->fetch ($this->getParam ('template', 'breadcrumb|show.php'));
		return true;
	}
}