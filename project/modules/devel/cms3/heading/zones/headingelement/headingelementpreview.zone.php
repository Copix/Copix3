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
 * Prévisualisation d'un élément
 *
 * @package cms
 * @subpackage heading
 */
class ZoneHeadingElementPreview extends CopixZone {
	/**
	 * Création du contenu
	 *
	 * @param string $toReturn HTML à afficher
	 * @return boolean
	 */
	protected function _createContent (&$toReturn) {
		$record = $this->getParam ('record');
		$types = _ioClass ('HeadingElementType')->getList ();
		$type = $types[$record->type_hei];
		$infos = array ('type' => array ('caption' => 'Type', 'value' => $type['caption']));
		$infos = array_merge ($infos, $this->getParam ('infos', array ()));
		$img = '<img src="' . _resource ($this->getParam ('icon', $type['icon'])) . '" alt="' . $infos['type']['caption'] . '" title="' . $infos['type']['caption'] . '" width="16px" height="16px" /> ';
		$infos['type']['value'] = $img . $infos['type']['value'];

		$tpl = new CopixTPL ();
		$tpl->assign ('record', $record);
		$tpl->assign ('type', $type);
		$tpl->assign ('link', $this->getParam ('link', _url ('heading||', array ('public_id' => $record->public_id_hei))));
		$tpl->assign ('caption', $this->getParam ('caption', $type['caption']));
		$tpl->assign ('infos', $infos);
		$tpl->assign ('actions', _ioClass ('HeadingElementInformationServices')->getActions ($record->id_helt, $record->type_hei));
		$tpl->assign ('linkPublish', _url ('heading|element|publish', array ('heading' => $record->parent_heading_public_id_hei, 'elements[]' => $record->id_helt.'|'.$record->type_hei)));
		$tpl->assign ('linkPlan', _url ('heading|element|plan', array ('heading' => $record->parent_heading_public_id_hei, 'elements[]' => $record->id_helt.'|'.$record->type_hei)));
		$tpl->assign ('linkArchive', _url ('heading|element|archive', array ('heading' => $record->parent_heading_public_id_hei, 'elements[]' => $record->id_helt.'|'.$record->type_hei)));
		$tpl->assign ('linkCopy', _url ('heading|element|copy', array ('heading' => $record->parent_heading_public_id_hei, 'elements[]' => $record->id_helt.'|'.$record->type_hei)));
		$tpl->assign ('linkCut', _url ('heading|element|cut', array ('heading' => $record->parent_heading_public_id_hei, 'elements[]' => $record->id_helt.'|'.$record->type_hei)));
		$tpl->assign ('linkDelete', _url ('heading|element|delete', array ('heading' => $record->parent_heading_public_id_hei, 'elements[]' => $record->id_helt.'|'.$record->type_hei)));
		$toReturn = $tpl->fetch ('heading|informations/headingelementpreview.php');
		return true;
	}
}