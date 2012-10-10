<?php
/**
 * @package cms
 * @subpackage heading
 * @copyright opixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * @author VUIDART Sylvain, Steevan BARBOYON
 */

/**
 * Fournit la liste des elements dependant de l'element de publicId donné en paramètre
 */
class ZoneHeadingElementDependencies extends CopixZone {
	/**
	 * Création du contenu
	 *
	 * @param string $pToReturn HTML à retourner
	 * @return boolean
	 */
	protected function _createContent (&$pToReturn) {
		$public_id = $this->getParam ('public_id', 0);
		$dependencies = _ioClass("heading|headingelementinformationservices")->getDependencies ($public_id);

		//pour les elements page ou portlet, on indique les elements qui sont utilisés
		$element = _ioClass("heading|headingelementinformationservices")->get ($public_id);
		$listElements = array();
		if ($element->type_hei == "page"){
			$page = _class ('portal|pageservices')->getByPublicId($public_id);
			$liste = $page->getListElementsInPage ();
			$listElements = array();
			foreach ($liste as $elementInPage){
				$listElements[] = $elementInPage;				
				$lastVersion = _ioClass("heading|headingelementinformationservices")->hasANewVersion ($elementInPage->public_id_hei, $elementInPage->id_helt);
				if ($lastVersion){
					$listElements[] = $lastVersion;
				}
			}
		}
		if ($element->type_hei == "portlet"){
			$portlet = _class ('portal|portletservices')->getHeadingElementPortletByPublicId($public_id);
			foreach ($portlet->getElementsToSave () as $portletElement){
				$elementToReturn = _ioClass ('heading|headingelementinformationservices')->get($portletElement->getHeadingElement()->public_id_hei);					
				$listElements[] = $elementToReturn;
				$lastVersion = _ioClass("heading|headingelementinformationservices")->hasANewVersion ($elementToReturn->public_id_hei, $elementToReturn->id_helt);
				if ($lastVersion){
					$listElements[] = $lastVersion;
				}
			}
		}

		$tpl = new CopixTPL ();
		$tpl->assign('listElements', $listElements);
		$tpl->assign('currentElement', $element);
		$tpl->assign ('status', _ioClass ('heading|HeadingElementStatus')->getList ());
		$tpl->assign ('dependencies', $dependencies);
		$pToReturn = $tpl->fetch ('heading|headingelement/headingelementdependencies.php');
		return true;
	}
}