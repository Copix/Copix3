<?php
/**
 * @package     cms
 * @subpackage  heading
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * @author      Sylvain Vuidart
 */

/**
 * Edition de menu pour les pages du CMS
 * 
 * @package cms
 * @subpackage heading
 */
class ActionGroupMenuEditor extends CopixActionGroup {
	
    protected function _beforeAction ($pAction) {
    	if (! (CopixAuth::getCurrentUser ()->testCredential ('basic:admin') || CopixAuth::getCurrentUser ()->testCredential ('cms:write@'._request ('heading', 0)) || CopixAuth::getCurrentUser ()->testCredential ('cms:write@'._request ('inheading', 0)))) {
	   		throw new CopixCredentialException ('basic:admin');
    	}
		CopixPage::add ()->setIsAdmin (true);
		if (CopixConfig::get('heading|usecmstheme') && CopixTheme::getInformations(CopixConfig::get('heading|cmstheme')) !==false){
        	CopixTPL::setTheme (CopixConfig::get('heading|cmstheme'));
        } 
		_ioClass ('HeadingElementInformationServices')->breadcrumbAdmin ();
    }
	
    public function processDefault(){
    	$ppo = _ppo();
    	$ppo->TITLE_PAGE = "Edition des menus";
    	//Mise en place du thème graphique
    	$element = _class('heading|headingelementinformationservices')->get(_request('public_id', 0));
		$theme = _class('heading|headingelementinformationservices')->getTheme ($element->public_id_hei, $foo);
		if (!$theme){
			$theme = CopixConfig::get ('default|publicTheme');
		}
    	$ppo->arListeMenus = _class('heading|headingelementmenuservices')->getListMenus($theme);
    	$ppo->theme = CopixTheme::getInformations($theme);
    	$ppo->element = $element;
    	$ppo->notify = _request('valid', 0);
    	return _arPPO($ppo, "menueditor/menueditor.php");
    }
    
    public function processGetElementInformations(){
    	CopixRequest::assert('public_id_hei');
    	$element = _class('heading|headingelementinformationservices')->get(_request('public_id_hei'));
		$heiservices = _ioClass ('heading|headingelementinformationservices');
		$ppo = _ppo();
    	//récupération des informations de visibilité
		$visibility_inherited_from = false;
		$visibility = $heiservices->getVisibility ($element->public_id_hei, $visibility_inherited_from);
		if ($visibility_inherited_from != null) {
			$visibility_inherited_from = $heiservices->get ($visibility_inherited_from)->caption_hei;
		}
		$ppo->visibility_inherited_from = $visibility_inherited_from;
		$ppo->visibility = $visibility;
    	$ppo->element = $element;
    	return _arPPO($ppo, array('template'=>'heading|menueditor/elementinformations.php', 'mainTemplate'=>'generictools|blank.tpl'));
    }
    
    public function processGetElementMenuInformations(){
    	CopixRequest::assert('public_id_hei');
    	$element = _class('heading|headingelementinformationservices')->get(_request('public_id_hei'));
		$theme = _class('heading|headingelementinformationservices')->getTheme ($element->public_id_hei, $foo);
    	if (!$theme){
			$theme = CopixConfig::get ('default|publicTheme');
		}
		$heiservices = _ioClass ('heading|headingelementinformationservices');
		$ppo = _ppo();
    	$ppo->arListeMenus = _class('heading|headingelementmenuservices')->getListMenus($theme);
    	$ppo->theme = CopixTheme::getInformations($theme);
    	$ppo->element = $element;
    	return _arPPO($ppo, array('template'=>'heading|menueditor/elementmenuinformations.php', 'mainTemplate'=>'generictools|blank.tpl'));
    }
    
    public function processGetMenuInformations(){
    	CopixRequest::assert('public_id_hei', 'type_hem');
    	$element = _class('heading|headingelementinformationservices')->get(_request('public_id_hei'));

		if (!HeadingElementCredentials::canModerate ($element->public_id_hei)) {
			return _arString("Vous n'avez pas les droits suffisant pour modifier ces informations");
		}
		
		$heiservices = _ioClass ('heading|headingelementinformationservices');
		$menuServices = _ioClass ('heading|headingelementmenuservices');
		
		//menus
		$inherited_menu = $menuServices->getInheritedHeadingElementMenu ($element->public_id_hei, _request('type_hem'));		
		$menu_informations = $menuServices->getMenu ($element->public_id_hei, _request('type_hem'));
		$theme = $heiservices->getTheme ($element->public_id_hei, $foo);
        if (!$theme){
			$theme = CopixConfig::get ('default|publicTheme');
		}
		$menu_caption = $menuServices->getCaption(_request('type_hem'), $theme);

		$ppo = _ppo();
		$tpl = new CopixTpl();
		$ppo->element = $element;
		$ppo->type_hem = _request('type_hem');
		$ppo->inherited_menu = $inherited_menu;
		$ppo->menu_informations = $menu_informations;
		$ppo->menu_caption = $menu_caption;
		return _arPPO($ppo, array('template'=>'heading|menueditor/menuinformations.php', 'mainTemplate'=>'generictools|blank.tpl'));
    }
    
    public function processGetApercuMenu (){
    	$ppo = _ppo();
    	if(_request('public_id_hei') != null){
    		$element = _class('heading|headingelementinformationservices')->get(_request('public_id_hei'));
    		if ($element->type_hei == 'portlet'){
				try{
					$portlet = _ioClass('portal|portletservices')->getHeadingElementPortletByPublicId ($element->public_id_hei);
					$content = $portlet->render (RendererMode::HTML, RendererContext::DISPLAYED);
				} catch (CopixException $e){
					//la portlet a été supprimée ou la base n'est pas bonne, on ne lance pas d'exception afin de pouvoir utiliser quand meme le site.
					_log ("Portlet de menu introuvable. ".$e->getMessage(), "errors", CopixLog::WARNING, array ('public_id' => $element->public_id_hei));
				}
    		} else {
    			$heading = $element;
    			$publicId = $element->public_id_hei;
				$depth = 2;
				$level = 0;
				//si le niveau est différent de 0 c'est qu'on prends un element au dessus de l'element de départ
				if ($level != 0) {
					$heading = _ioClass('HeadingElementInformationServices')->getParentAtLevel($publicId, $level);
					$publicId = $heading->public_id_hei;
				} 
				
				$tree = _ioClass('HeadingElementInformationServices')->getTree ($publicId, $depth);
				$heading->children = $tree;
				$tpl = new CopixTpl();
				$tpl->assign('elementsTypes', _ioClass ('HeadingElementType')->getList ());
				$tpl->assign('tree', array($heading));
				$content = $tpl->fetch("heading|menueditor/menupreview.php");			
    		}
    		$ppo->MAIN = $content;
    		return _arDirectPPO($ppo, 'generictools|blank.tpl');
    	}
    	return _arNone();
    }
    
    public function processSaveMenu(){
    	if (_request('editmenusubmit', false)){
    		$element = _ioClass("heading|headingelementinformationservices")->get(_request('public_id'));
    		$element->show_in_menu_hei = _request('show_in_menu_hei');
    		$element->menu_html_class_name_hei = _request('class_name_hei');
    		_ioClass("heading|headingelementinformationservices")->update($element);

    		foreach (_request('elements') as $publicId=>$infos){
    			$menuElementInfos = _ioClass("heading|headingelementinformationservices")->get($publicId);
    			$menuElementInfos->show_in_menu_hei = $infos['visibility_inherited_from'] == '' ? $infos['visibility'] : 2;
    			$menuElementInfos->display_order_hei = $infos['order'];
    			_ioClass("heading|headingelementinformationservices")->update($menuElementInfos);
    		    if ($publicId != 0){
    				//on ne teste pas le parent pour la racine du site 
    				_ioClass("heading|headingelementinformationservices")->move($publicId, $infos['parent']);
    			}
    		}
    		
    		$menu = _class('heading|headingelementmenuservices')->getMenu (_request('public_id'), _request('menu'));
			if (_request('menu_select') != 2){
				$headingMenu = ($menu == null) ? DAORecordcms_headingelementinformations_menus::create () : clone ($menu);						
				$headingMenu->type_hem = _request('menu');
				$headingMenu->public_id_hei = _request('public_id');
				$headingMenu->public_id_hem = _request('menu_public_id_hem');
				$headingMenu->level_hem = _request('menu_level', 0);
				$headingMenu->depth_hem = _request('menu_depth', 1);
				$headingMenu->portlet_hem = $element->type_hei == 'portlet' ? 1 : 0;
				$headingMenu->template_hem = _request('template', null);	
				$headingMenu->class_hem = _request('class');
				$headingMenu->is_empty_hem = _request('menu_select') ? 0 : 1;
				$headingMenu->modules_hem = _request('modules_hem');

				if (DAOcms_headingelementinformations_menus::instance ()->check ($headingMenu) === true){
					if ($headingMenu->is_empty_hem == 1 && ($menu == null || $headingMenu->is_empty_hem != $menu->is_empty_hem)) {
						_ioClass ('HeadingActionsService')->notify (HeadingActionsService::MENU_NONE, $headingMenu);
					}
					if ($menu == null){
						if ($headingMenu->is_empty_hem == 0) {
							$extras = array ('menu_type' => $headingMenu->type_hem, 'menu_public_id_hei' => $headingMenu->public_id_hem);
							_ioClass ('HeadingActionsService')->notify (HeadingActionsService::MENU_CHANGE, $headingMenu, $extras);
						}
						DAOcms_headingelementinformations_menus::instance ()->insert ($headingMenu);
					} else {
						if ($headingMenu->is_empty_hem == 0 && $headingMenu->public_id_hem != $menu->public_id_hem) {
							$extras = array ('menu_type' => $headingMenu->type_hem, 'menu_public_id_hei' => $headingMenu->public_id_hem);
							_ioClass ('HeadingActionsService')->notify (HeadingActionsService::MENU_CHANGE, $headingMenu, $extras);
						}
						DAOcms_headingelementinformations_menus::instance ()->update ($headingMenu);
					}
				}
			} else if ($menu != null){
				_ioClass ('HeadingActionsService')->notify (HeadingActionsService::MENU_INHERITED, $menu, array ('menu_type' => $menu->type_hem));
				DAOcms_headingelementinformations_menus::instance ()->delete ($menu->id_hem);
			}	
		
    		return _arRedirect(_url('heading|menueditor|', array('valid'=>1, 'public_id'=>_request('public_id'))));
    	}
    	return $this->processDefault();
    }
	
}