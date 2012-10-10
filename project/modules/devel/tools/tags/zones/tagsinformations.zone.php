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
 * Affichage des informations sur le tags pour un élément
 *
 * @package cms
 * @subpackage heading
 */
class ZoneTagsInformations extends CopixZone {
	/**
	 * Création du contenu
	 *
	 * @param string $toReturn HTML à afficher
	 * @return boolean
	 */
	protected function _createContent (&$toReturn) {
		$record = $this->getParam ('record');
		if (is_array($record)){
			return null;
		}
		$services = _ioClass ('headingelementinformationservices');

		// Récupère les tags de l'élément, et le public_id dont il hérite éventuellement
		$record->tags = $services->getTags ($record->public_id_hei, $record->tags_parent_public_id_hei);

		$record->tags_parent_inherited = ($record->tags_parent_public_id_hei !== false);

		if ($record->tags_parent_public_id_hei) {
			// Liste des tags hérités
			$record->tags_parent_tags_inherited = $services->getTags ($record->tags_parent_public_id_hei, $tplTags->tmp);
			// Pour faire un lien vers le parent
			$record->tags_parent_caption = $services->get ($record->tags_parent_public_id_hei)->caption_hei;
			//$record->tags_parent_url = _url ('heading||', array ('public_id'=>$public_id_hei));
		} else {
			$record->tags_parent_tags_inherited = array ();
		}

		$tags = _ioClass('tags|tagservices')->listAll();
		$tplTags = array ();
		for ($i = 0; $i < count ($tags); $i++) {
			$checked = false;
			$inherited = false;
			if (in_array ($tags[$i], $record->tags)) {
				$checked = true;
			}
			if (in_array ($tags[$i], $record->tags_parent_tags_inherited)) {
				$inherited = true;
			}
			$tplTags[$i] = array ('caption' => $tags[$i], 'checked' => $checked, 'inherited' => $inherited);
		}

		$tpl = new CopixTPL ();
		$tpl->assign ('record', $record);
		$tpl->assign ('tags', $tplTags);
		$toReturn['tags'] = $tpl->fetch ('tags|tags.informations.php');
		return true;
	}
}