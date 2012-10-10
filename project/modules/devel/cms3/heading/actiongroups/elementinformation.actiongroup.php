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
 * Actions sur la récupération / mise à jour des informations génériques aux éléments de rubriques
 * 
 * @package cms
 * @subpackage heading
 */
class ActionGroupElementInformation extends CopixActionGroup {
	
	/**
	 * Les services 
	 *
	 * @var unknown_type
	 */
	protected $_headingElementInformationServices;
	
	/**
	 * Création des classes de services nécessaires et controle des droits si besoin.
	 */
	protected function _beforeAction ($pActionName) {
		CopixPage::add ()->setIsAdmin (true);
		if (CopixConfig::get('heading|usecmstheme') && CopixTheme::getInformations(CopixConfig::get('heading|cmstheme')) !==false){
        	CopixTPL::setTheme (CopixConfig::get('heading|cmstheme'));
        } 
		$this->_headingElementInformationServices = new HeadingElementInformationServices ();		
		
	}

	/**
	 * Récupération des informations sur un ou plusieurs éléments donnés
	 */
	public function processDefault (){
		CopixHTMLHeader::addJSLink (_resource ('|js/headingelementinformation.admin.js'));
		if (is_array(_request ('id_helt'))){
			$element = array();
			foreach (_request ('id_helt') as $elementInfos){
				list ($id_helt, $type_hei, $public_id) = explode('|', $elementInfos);
				$element[] = $this->_headingElementInformationServices->getById ($id_helt, $type_hei);
			}
		} else {
			$element = $this->_headingElementInformationServices->getById (_request ('id_helt'), _request ('type_hei'));
		}
		//on affiche le theme de la rubrique dans laquelle on est.
		$theme = $this->_headingElementInformationServices->getTheme (is_array($element) ? $element[0]->public_id_hei : $element->public_id_hei, $foo);
		if ($theme != null && CopixConfig::get ('heading|useDefinedThemeForAdmin') == 1) {
			CopixTpl::setTheme ($theme);
		}
		return _arDirectPpo (_ppo (array ('MAIN'=>CopixZone::process ('heading|headingelement/headingelementinformations', array ('record'=>$element)))), 'generictools|blank.tpl');
	}

	/**
	 * Demande de sauvegarde des informations génériques sur l'élément 
	 */
	public function processSave (){
		if (_request('arPublicId', false)){
			return $this->_saveMultiple ();
		} else {
			try {
				CopixRequest::assert ('id_helt','type_hei');
				return $this->_saveUnique ();
			} catch (CopixRequestException $e) {
				return _arPpo (_ppo ('MAIN=>NOK'), 'generictools|blanknohead.tpl');
			}
		}
	}
	
	/**
	 * Demande de sauvegarde des informations génériques sur l'élément 
	 */
	private function _saveUnique (){
		$newVersion = false;
		//Récupération des données actuelles de l'élément a modifier
		$typeInformations = _ioClass('heading|headingelementtype')->getInformations (_request ('type_hei'));
		$elementServices = _ioClass($typeInformations['classid']);
		$element = $elementServices->getById (_request ('id_helt'));
		
		
	//	$element = $this->_headingElementInformationServices->getById (_request ('id_helt'), _request ('type_hei'));

		//Affichage dans les menus
		$element->show_in_menu_hei = _request ('show_in_menu_hei');
		
		if (_request ('theme_use_parent') != null) {
			$element->theme_id_hei = null;
		} else {
			$element->theme_id_hei = _request ('theme_id') . '|' . _request ('theme_template_' . _request ('theme_id'));
		}
		$element->menu_html_class_name_hei = _request ('class_name_hei');
		
		/*************Gestion des urls**************/
		//Sauvegarde des informations d'url
		if (CopixRequest::get('base_url')) {
			if (_request('base_url_inherited') !== "true") {
				$element->base_url_hei = _request ('base_url', null);
			} else {
				$element->base_url_hei = null;
			}
		}

		/************Gestion des URLs***************/
		// On ne doit enregistrer de version que si l'URL change, et que l'élément est déjà publié
		if( _request ('url_id' ) && $element->url_id_hei != null && $element->url_id_hei != _request ('url_id') && $element->status_hei == HeadingElementStatus::PUBLISHED ){
			$newVersion = true;
		}
		$element->url_id_hei = _request ('url_id', null);
		
		/************Gestion des menus***************/
		$this->_saveMenus($element);
		
		/*********************gestion des flux rss *****************/
		if (CopixModule::isEnabled('cms_rss')){
			$arFlux = _request('rss'._request('uniqId'), array());	
			$arElementFlux = DAOcms_rss_headingelementinformations::instance ()->findBy(_daoSP()->addCondition('headingelement_public_id', '=', $element->public_id_hei))->fetchAll();
			if(!empty($arElementFlux)){
				foreach ($arElementFlux as $elementFlux){
					if (($key = array_search($elementFlux->id_rss, $arFlux)) === false){
						DAOcms_rss_headingelementinformations::instance ()->delete($elementFlux->id_rss, $element->public_id_hei);
					} else {
						unset ($arFlux[$key]);
					}
				}
			}
			if (!empty($arFlux)){
				foreach ($arFlux as $key=>$idflux){
					$flux = _class('cms_rss|rssservices')->getById($idflux);
					$record = DAORecordcms_rss_headingelementinformations::create ();
					$record->id_rss = $idflux;
					$record->headingelement_public_id = $element->public_id_hei;
					$record->rss_public_id = $flux->public_id_hei;
					DAOcms_rss_headingelementinformations::instance ()->insert($record);
				}
			}
		}
		
		/********************* gestion du fil d'ariane *****************/
		if ($element->type_hei == 'heading') {
			$element->breadcrumb_show_heading = _request ('breadcrumb_show_heading');
			HeadingElementServices::call ('heading', 'update', $element);
		} else if ($element->type_hei == 'page') {
			$element->breadcrumb_type_page = _request ('breadcrumb_type_page');
			HeadingElementServices::call ('page', 'update', $element);
		}
		
		if (CopixUserPreferences::get('heading|cms_mode') == "advanced"){
		
			/*********************gestion des droits********************/
			if(HeadingElementCredentials::canModerate($element->public_id_hei)){
				
				$rights_inherited = _request('rights_inherited');
				if (isset ($rights_inherited)){
					$element->credentials_inherited_hei = 1;
					HeadingElementInformationServices::deleteCredentialsFromPublicId($element->public_id_hei);
				} else {			
					$element->credentials_inherited_hei = 0;
					$credentials = _request('credentials');
					if (is_array($credentials)){
						foreach ($credentials as $idGroupHandler => $arGroups){
							foreach ($arGroups as $idGroup => $credential){
								$this->_headingElementInformationServices->saveCredentials ($idGroup, $idGroupHandler,$element->public_id_hei, $credential);
							}
						}
					}
					$newGroupCredentials = _request('newcredentialsgroup');
					$newCredentials = _request('newcredentials');
					if (is_array($newGroupCredentials) && is_array($newCredentials)){
						foreach ($newGroupCredentials as $id => $infosGroup ){
							if(isset($newCredentials[$id])){
								$infos = explode('~',$infosGroup);
								$this->_headingElementInformationServices->saveCredentials ($infos[1], $infos[0],  $element->public_id_hei, $newCredentials[$id]);
							}
						}
					}
				}
			}
			
			/*********************gestion du référencement ********************/
	        if (CopixConfig::get('heading|robotsActivated')) {
	            if (1 == ($meta_robots_inherited = _request ('meta_robots_inherited'))) {
	                $robots = NULL;
	            }
	            else {
	                $robots = _request ('meta_robots', '', '');
	                if (is_array ($robots)) {
	                    $robots = implode (',', $robots);
	                }
	            }
	            $element->robots_hei = $robots;
	        }
	        
			/*********************gestion des cibles********************/
			$element->target_hei = _request ('target_hei');
			if ($element->target_hei == 2 || $element->target_hei == 3){
				$element->target_params_hei = "width=" . CopixRequest::getInt ('target_width', 300) . ";height=" . CopixRequest::getInt ('target_height', 300);
			}
		
	        /*********************tags*******************************/
			if (CopixModule::isEnabled('tags')){
				$requestTagsInformation =  _ppo( _request('tags', array()) );
				$element->tags_inherited_hei = ($requestTagsInformation->inherited) ? 1 : 0;
				$this->_headingElementInformationServices->setTags( $element->public_id_hei, $requestTagsInformation );
			}
			
			/*********************comments*******************************/
			$element->comment_hei = _request ('comment_hei', null);
		}
        
		if (CopixRequest::get('publishedfrom') == '__/__/____') {
			$element->published_date_hei = NULL;
		} else {
			if (CopixRequest::get('publishedfrom')) {
				if (CopixDateTime::DateTimeToyyyymmddhhiiss(_request ('publishedfrom').' '._request('publishedfromtime'))) {
					if (!$element->published_date_hei = CopixDateTime::DateTimeToyyyymmddhhiiss(_request ('publishedfrom').' '._request('publishedfromtime'))) {
						throw new CopixException ('Problème dans la date');
					}
				} else {
					$element->published_date_hei = NULL;
				}
			}
		}
		
		if (CopixRequest::get('publishedat')) {
			if (CopixRequest::get('publishedat') == '__/__/____') {
				$element->end_published_date_hei = NULL;
			} else {
				if (CopixDateTime::DateTimeToyyyymmddhhiiss(_request('publishedat').' '._request('publishedattime'))) {
					if (!$element->end_published_date_hei = CopixDateTime::DateTimeToyyyymmddhhiiss(_request('publishedat').' '._request('publishedattime'))) {
						throw new CopixException ('Problème dans la date');
					}
				} else {
					$element->end_published_date_hei = NULL;
				}
			}
		}
		
		// liens vers d'autres éléments
		$linkedElements = explode (',', _request ('linkedElements'));
		array_pop ($linkedElements);
		$this->_headingElementInformationServices->saveLinkedHeadingElements ($element->public_id_hei, $linkedElements);
		
		if (CopixModule::isEnabled('xiti')){
			_ioClass('xiti|xitiservices')->setMarker($element->public_id_hei,'headingelement',_request('xiti_marker_string'),_request('xiti_site_n1'),_request('xiti_site_n2'),_request('xiti_xtsd_http'),_request('xiti_xtsd_https'), _request ('xiti_auto_id', 0));
		}
		
		//Mise à jour de l'element
		try {
			if( $newVersion ){
				$elementServices->version ($element);
				$this->_headingElementInformationServices->publishById ($element->id_helt, $element->type_hei);
			} else {
				$this->_headingElementInformationServices->update ($element);
			}
		} catch (Exception $e){
			return _arPpo (_ppo ('MAIN=>Une erreur est survenue durant l\'enregistrement<br />'.$e->getMessage()), 'generictools|blanknohead.tpl');
		}
		
		$id_helt = $element->id_helt;
		$type_hei = $element->type_hei;
		$public_id_hei = $element->public_id_hei;
		if (!$newVersion) {
			CopixHTMLHeader::addJSCode("
				currentpublicId = lastPublicId;
				lastPublicId = false;
				showHeadingElementInformationsIn ('$id_helt','$type_hei',currentpublicId, 'HeadingElementInformationDiv');
			");
		} else {
			$url = _url ('heading|element|', array ('heading' => $element->parent_heading_public_id_hei, 'selected' => array ($element->id_helt . '|' . $element->type_hei)));
			CopixHTMLHeader::addJSCode ("document.location = '" . $url . "'");
		}
		return _arPpo (_ppo (), 'generictools|blanknohead.tpl');
	}
	
	/**
	 * Demande de sauvegarde des informations génériques sur l'élément 
	 */
	private function _saveMultiple (){
		$arPublicId = _request('arPublicId');
		
		foreach (explode('|', $arPublicId) as $idInfos){
			list ($id_helt, $type_hei) = explode(';', $idInfos);
			
			//Récupération des données actuelles de l'élément a modifier
			$element = $this->_headingElementInformationServices->getById ($id_helt, $type_hei);
	
			//Affichage dans les menus
			$element->show_in_menu_hei = _request ('show_in_menu_hei') == -1 ? $element->show_in_menu_hei : _request ('show_in_menu_hei');
			$element->theme_id_hei = _request ('theme') == -1 ? $element->theme_id_hei : _request ('theme');
			$element->menu_html_class_name_hei = _request ('class_name_hei') == '******' ? $element->menu_html_class_name_hei : _request ('class_name_hei');
			
			/*************Gestion des urls**************/
			//Sauvegarde des informations d'url			 
			if (_request('base_url_inherited') == "true") {
				$element->base_url_hei = null;
			} else if (_request ('base_url') != "******") {			
				$element->base_url_hei = _request ('base_url', null);
			}
	
			/*********************gestion des cibles********************/
			if (_request ('target_hei')){
				$element->target_hei = _request ('target_hei');
				if ($element->target_hei == 2 || $element->target_hei == 3){
					$element->target_params_hei = "width=" . _request('target_width', 300) . ";height=" . _request ('target_height', 300);
				}
			}
			
			/*********************gestion des droits********************/
			if (HeadingElementCredentials::canModerate($element->public_id_hei)){
				$arGroups = DAOdbgroup::instance ()->findAll ()->fetchAll ();
				
				$rights_inherited = _request('rights_inherited');
				if (isset ($rights_inherited)){
					$element->credentials_inherited_hei = 1;
					HeadingElementInformationServices::deleteCredentialsFromPublicId($element->public_id_hei);
				} else {			
					$element->credentials_inherited_hei = 0;
					$credentials = _request('credentials');
					if (is_array($credentials)){
						foreach ($credentials as $idGroupHandler => $arGroups){
							foreach ($arGroups as $idGroup => $credential){
								$this->_headingElementInformationServices->saveCredentials ($idGroup, $idGroupHandler,$element->public_id_hei, $credential);
							}
						}
					}
					$newGroupCredentials = _request('newcredentialsgroup');
					$newCredentials = _request('newcredentials');
					if (is_array($newGroupCredentials) && is_array($newCredentials)){
						foreach ($newGroupCredentials as $id => $infosGroup ){
							if(isset($newCredentials[$id])){
								$infos = explode('~',$infosGroup);
								$this->_headingElementInformationServices->saveCredentials ($infos[1], $infos[0],  $element->public_id_hei, $newCredentials[$id]);
							}
						}
					}					
				}
			}
			//Mise à jour de l'element
			try {
				$this->_headingElementInformationServices->update ($element);
			} catch (Exception $e){
				return _arPpo (_ppo ('MAIN=>Une erreur est survenue durant l\'enregistrement'), 'generictools|blanknohead.tpl');
			}
		}		
		return _arPpo (_ppo (), 'generictools|blanknohead.tpl');
	}

/**
	 * récupère le select des groupes
	 *
	 * @return CopixActionReturn
	 */
	public function processNewGroupSelect () {
		$groupHandlerLabels = CopixGroupHandlerFactory::getGroupLabels();
		$allGroups = CopixGroupHandlerFactory::getAllGroupList();
		$ppo = _ppo();
		$width = _request('width');
		$width = ($width) ? $width : 'auto';
		$name = 'newcredentialsgroup[]';
		$select = '<select name="'.$name.'" style="width: '.$width.'px">';
		foreach ($allGroups as $handlerName => $groups){
			$label = $groupHandlerLabels[$handlerName];
			asort ($groups);
			$select .= '<optgroup label="'.$label.'">';	
			foreach ($groups as $groupId => $groupName){
				$select .= '<option value="'.$handlerName.'~'.$groupId.'">'.$groupName.'</option>';	
			}
			$select .= '</optgroup>';
		}
		$select.="</select>";
		$ppo->MAIN = $select; 
		return _arDirectPPO ($ppo, 'generictools|blanknohead.tpl');
	}
	

	/**
	 * récupère le select des droits
	 *
	 * @return CopixActionReturn
	 */
	public function processNewCredentialsSelect () {
		$rights = _ioClass ('HeadingElementCredentials')->getList ();
		$select = _Tag ('select', array (
			'extra' => 'class="rightSelect"',
			'emptyShow' => true,
			'emptyValues' => '-- Droits de la rubrique parente --',
			'name' => 'newcredentials[]',
			'id' => uniqid('newcredentials'),
			'values' => $rights,
		));
		$ppo = _ppo();
		$ppo->MAIN = $select;
		return _arDirectPPO ($ppo, 'generictools|blanknohead.tpl');
	}
	
	public function processDeleteCredential () {
		try{
			CopixAuth::getCurrentUser()->testCredential("basic:admin");
			$groupId = _request('group_id');
			$publicId = _request('public_id');
			$groupHandler = _request('groupHandler');
			
			HeadingElementInformationServices::deleteCredential($groupId, $groupHandler, $publicId);
			return _arNone();
		}catch(Exception $e){
			return _ar404('Action non autorisée');
		}
	}
	
	private function _saveMenus ($pElement){
		$fooParameterIn = null;
   		$theme = _class("heading|headingelementinformationservices")->getTheme ($pElement->public_id_hei, $fooParameterIn);	
		$typesMenu = _class("heading|headingelementmenuservices")->getListMenus($theme);
		$arSelect = _request('menu_select');
		$arPublic_id_hei = _request('menu_public_id_hem');
		$arLevel = _request('menu_level', array());
		$arDepth = _request('menu_depth', array());
		$arPortlet = _request('portlet', array());
		$arTemplate = _request('template', array());
		$arClass = _request('class', array());

		if(!empty($typesMenu)){
			foreach ($typesMenu as $type){			
				$menu = _class('heading|headingelementmenuservices')->getMenu ($pElement->public_id_hei, $type['name']);
				if (!empty($arSelect) && array_key_exists($type['name'], $arSelect)){
					// on definit le menu
					if ($arSelect[$type['name']] != 2 && array_key_exists($type['name'], $arPublic_id_hei)){
						// on récupère encore le menu pour ne pas modifier l'instance de $menu, et pas de clone pour gérer d'éventuels objets à l'intérieur de $menu
						$headingMenu = ($menu == null) ? DAORecordcms_headingelementinformations_menus::create () : clone ($menu);
						
						$headingMenu->type_hem = $type['name'];
						$headingMenu->public_id_hei = $pElement->public_id_hei;
						$headingMenu->public_id_hem = $arPublic_id_hei[$type['name']];
						$headingMenu->level_hem = array_key_exists($type['name'], $arLevel) ? $arLevel[$type['name']] : 0;
						$headingMenu->depth_hem = array_key_exists($type['name'], $arDepth) ? $arDepth[$type['name']] : 1;
						$targetMenu = _class('heading|headingelementinformationservices')->get($arPublic_id_hei[$type['name']]);
						$headingMenu->portlet_hem = $targetMenu->type_hei == 'portlet' ? 1 : 0;
						$headingMenu->template_hem = array_key_exists($type['name'], $arTemplate) ? $arTemplate[$type['name']] : null;	
						$headingMenu->class_hem = array_key_exists($type['name'], $arClass) ? $arClass[$type['name']] : null;
						$headingMenu->is_empty_hem = $arSelect[$type['name']] ? 0 : 1;
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
				}elseif ($menu != null){
					_ioClass ('HeadingActionsService')->notify (HeadingActionsService::MENU_INHERITED, $menu, array ('menu_type' => $menu->type_hem));
					DAOcms_headingelementinformations_menus::instance ()->delete ($menu->id_hem);
				}
			}
		}
	}
	
	public function processSaveMenus (){
		$typeInformations = _ioClass('heading|headingelementtype')->getInformations (_request ('type_hei'));
		$elementServices = _ioClass($typeInformations['classid']);
		$element = $elementServices->getById (_request ('id_helt'));
		$this->_saveMenus($element);
	}
}