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
 * Affichage des informations sur le RSS pour un élément
 *
 * @package cms
 * @subpackage heading
 */
class ZoneMVTestingInformations extends CopixZone {
	/**
	 * Création du contenu
	 *
	 * @param string $toReturn HTML à afficher
	 * @return boolean
	 */
	protected function _createContent (&$toReturn) {
		$record = $this->getParam ('record');

		if (is_array ($record) || $record->type_hei != 'mvtesting') {
			return true;
		}

		$mvtesting = _ioClass ('MVTestingServices')->getById ($record->id_helt);
		$countShow = 0;
		foreach ($mvtesting->elements as $element) {
			$countShow += $element->show_element;
		}
		foreach ($mvtesting->elements as $element) {
			if ($countShow > 0) {
				$element->show_percents = $element->show_element * 100 / $countShow;
				if (is_float ($element->show_percents)) {
					$element->show_percents = number_format ($element->show_percents, 0, ',', ' ');
				}
			} else {
				$element->show_percents = 0;
			}
			if ($element->type_element == MVTestingServices::TYPE_CMS) {
				$element->caption = _ioClass ('HeadingElementInformationServices')->get ($element->value_element)->caption_hei;
				$element->url = _url ('heading||', array ('public_id' => $element->value_element));
			} else {
				$element->caption = $element->value_element;
				$params = explode ('?', $element->value_element);
				$element->url = _url ($params[0]) . (count ($params) > 1 ? '?' . $params[1] : '');
			}
		}

		$tpl = new CopixTPL ();
		$tpl->assign ('mvtesting', $mvtesting);
		$toReturn['mv_testing'] = $tpl->fetch ('cms_mvtesting|mvtesting.informations.php');
		return true;
	}
}