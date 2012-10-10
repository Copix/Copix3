<?php
/**
 * @package cms
 * @subpackage heading
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU General Lesser Public Licence, see LICENCE file
 * @author Sylvain Vuidart
 */

/**
 * Zone d'export des éléments du CMS
 *
 * @package cms
 * @subpackage heading
 */
class ZoneHeadingElementExport extends CopixZone {
	
	/**
	 * Création du contenu
	 *
	 * @param string $pToReturn HTML à retourner
	 * @return boolean
	 */
	protected function _createContent (&$pToReturn) {
		$tpl = new CopixTpl();
		$tpl->assign('nbElements', $this->getParam('nbElements'));
		$pToReturn = $tpl->fetch('export/headingelementexport.zone.php');
		return true;
	}
}