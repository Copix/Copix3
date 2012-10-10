<?php
/**
 * @package     cms3
 * @subpackage  portal
 * @copyright	CopixTeam
 * @link	http://copix.org
 * @license	http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * @author      VUIDART Sylvain
 */

/**
 * Actiongroup pour les actions dans portal n'ayant aucun rapport avec l'edition et creation de page ou de portlet (voir actiongroup admin et adminportlet)
 * @package     cms3
 * @subpackage  portal
 */
class ActionGroupDefault extends CopixActionGroup{
	
	/**
	 * 
	 * Liste les elements dans la page, qui ne sont pas publiés.
	 */
	public function processPublishElementsInPage (){
		CopixRequest::assert('public_id_hei');
		$page = _class("portal|pageservices")->getByPublicId(_request('public_id_hei'));
		$ppo = new CopixPPO();
		$ppo->TITLE_PAGE = "Publication des éléments brouillon de la page.";
		$ppo->currentElement = $page;
		$liste = $page->getListElementsInPage();
		$listElements = array();
		foreach ($liste as $element){
			if ($element->status_hei == HeadingElementStatus::DRAFT){
				$listElements[] = $element;
			} else {
				$lastVersion = _ioClass("heading|headingelementinformationservices")->hasANewVersion ($element->public_id_hei, $element->id_helt);
				if ($lastVersion){
					$listElements[] = $lastVersion;
				}
			}
		}
		$ppo->toPublish = $listElements;
		$headingElementType = new HeadingElementType ();
        $ppo->arHeadingElementTypes = $headingElementType->getList ();
		return _arPPO($ppo, "publishelementsinpage.php");
	}
	
	/**
	 * 
	 * Liste les elements dans la portlet, qui ne sont pas publiés.
	 */
	public function processPublishElementsInPortlet (){
		CopixRequest::assert('public_id_hei');
		$portlet = _class("portal|portletservices")->getHeadingElementPortletByPublicId(_request('public_id_hei'));
		$ppo = new CopixPPO();
		$ppo->TITLE_PAGE = "Publication des éléments brouillon de la portlet.";
		$ppo->currentElement = $portlet;
		$toPublish = array();
		foreach ($portlet->getElementsToSave() as $element){
			$elementToReturn = _ioClass ('heading|headingelementinformationservices')->get($element->getHeadingElement()->public_id_hei);
			if ($elementToReturn->status_hei == HeadingElementStatus::DRAFT){
				$toPublish[] = $element;
			} else {
				$lastVersion = _ioClass("heading|headingelementinformationservices")->hasANewVersion ($elementToReturn->public_id_hei, $elementToReturn->id_helt);
				if ($lastVersion){
					$toPublish[] = $lastVersion;
				}
			}
		}
		$ppo->toPublish = $toPublish;
		$headingElementType = new HeadingElementType ();
        $ppo->arHeadingElementTypes = $headingElementType->getList ();
		return _arPPO($ppo, "publishelementsinpage.php");
	}
}
?>