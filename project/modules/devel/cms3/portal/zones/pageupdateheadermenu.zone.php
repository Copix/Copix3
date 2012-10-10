<?php
/**
 * @package     cms
 * @subpackage  heading
 * @copyright	CopixTeam
 * @link	http://copix.org
 * @license	http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * @author   Sylvain Vuidart
 */

/**
 * Menu affiché au dessus des pages en mode modification.
 */
class ZonePageUpdateHeaderMenu extends CopixZone {
	
	public function _createContent (&$toReturn) {
		$tpl = new CopixTpl();
		$tpl->assign('public_id_hei', $this->getParam('public_id_hei'));
		$tpl->assign("zoneBoutons", CopixZone::process('portal|pagemenu', array('renderContext'=>$this->getParam('renderContext'), 'element'=>$this->getParam('element'))));
		$tpl->assign("zonePagePath", CopixZone::process('portal|pagepath', array('public_id'=>$this->getParam("parent_public_id"), 'caption_hei'=>$this->getParam("caption_hei"))));
		$toReturn = $tpl->fetch("pageupdatemenu.php");
		return true;
	}
}