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
class ZoneCMSRSSInformations extends CopixZone {
	/**
	 * Création du contenu
	 *
	 * @param string $toReturn HTML à afficher
	 * @return boolean
	 */
	protected function _createContent (&$toReturn) {
		$record = $this->getParam ('record');
		if (is_array ($record)) {
			return true;
		}
		$arFlux = _ioClass ('cms_rss|rssservices')->getListFlux ();
		if (!empty ($arFlux)) {
			$arElementFlux = _ioClass ('cms_rss|rssservices')->getHeadingElementListFlux ($record->public_id_hei);
			$tpl = new CopixTPL ();
			$tpl->assign ('uniqId', $this->getParam ('uniqId'));
			$tpl->assign ('arFlux', $arFlux);
			$tpl->assign ('arElementFlux', $arElementFlux);
			$toReturn['flux_rss'] = $tpl->fetch ('cms_rss|cmsrssinformations.php');
		}
		return true;
	}
}