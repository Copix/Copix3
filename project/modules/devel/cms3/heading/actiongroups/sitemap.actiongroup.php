<?php
/**
 * Permet de gérer les sitemap
 * @author fredericb
 *
 */
class ActionGroupSiteMap extends CopixActionGroup{
	
	/**
	 * On permet la visualisation des sitemap sansn droits
	 */
	protected function _beforeAction ($pActionName){
		if(strtolower($pActionName) != 'getsitemap' && strtolower($pActionName) != 'getsitemaplink'){
			_currentUser ()->assertCredential ('basic:admin');
			CopixPage::add ()->setIsAdmin (true);
			if (CopixConfig::get('heading|usecmstheme') && CopixTheme::getInformations(CopixConfig::get('heading|cmstheme')) !==false){
	        	CopixTPL::setTheme (CopixConfig::get('heading|cmstheme'));
	        } 
			_notify ('breadcrumb', array ('path' => array ('heading|sitemap|' => 'SiteMap')));
		}
	}
	/**
	 * Affichage des sitemap
	 * @return CopixActionReturn
	 */
	public function processDefault(){
		$ppo = _ppo();
		$ppo->TITLE_PAGE = 'Liste des sitemap';
		$ppo->list = SiteMapServices::getSiteMapList();	
		return _arPPO($ppo, 'sitemap/sitemaplist.php');
	}
	
	/**
	 * Edition des sitemap et des catégories
	 * @return CopixActionReturn
	 */
	public function processEditSitemap(){
		$ppo = _ppo();
		$ppo->TITLE_PAGE = 'Edition des sitemap';
		$parentId = _request('parentId');
		$linkId = _request('linkId');
		// création d'un sitemap
		if(!$parentId && !$linkId){
			$ppo->link = new SiteMapLink();
			_notify ('breadcrumb', array ('path' => array ('#' => 'Nouveau')));
		}else if(!$linkId) {
			// création d'un lien
			$link = new SiteMapLink();
			$link->setParentId($parentId);
			$ppo->link = $link;
			_notify ('breadcrumb', array ('path' => array ('#' => 'Modification')));
		}else{
			// modification d'un lien
			$ppo->link = SiteMapServices::getSiteMapLink($linkId);
			_notify ('breadcrumb', array ('path' => array ('#' => 'Modification d\'un lien')));
		}
		return _arPPO($ppo, 'sitemap/sitemapedit.php');
	}
	
	/**
	 * Sauvagarde d'un sitemap
	 * @return CopixActionReturn
	 */
	public function processDoEditSitemap(){
		
		if(_request('linkId')){
			$sitemapLink = SiteMapServices::getSiteMapLink(_request('linkId'));	
		}else{
			$sitemapLink = new SiteMapLink();	
		}
		
		$oldChildMode = $sitemapLink->getChildMode();
		
		$sitemapLink->setParentId(_request('parentId'));
		$sitemapLink->setId(_request('linkId'));
		$sitemapLink->setCaption(_request('caption'));
		$sitemapLink->setUrlMode(_request('url_mode'));
		$sitemapLink->setCustomUrl(_request('custom_url'));
		$sitemapLink->setCmsLink(_request('cms_link'));
		$sitemapLink->setCmsHeading(_request('cms_heading'));
		$sitemapLink->setNewWindow(_request('new_window'));
		if($sitemapLink->getId()){
			$sitemapLink->setChildMode(_request('child_mode'));
		}
		if(($sitemapLink->getId()) && ($oldChildMode == SiteMapLink::CHILD_MODE_MANUAL) && ($sitemapLink->getChildMode() == SiteMapLink::CHILD_MODE_HEADING)){
			$children = SiteMapServices::getChildHeading($sitemapLink->getId());
			if(count($children) != 0){
				if(!_request('confirm')){
					
					$confirmParams = CopixRequest::asArray();
					$confirmParams['confirm'] = 1;
					
					return CopixActionGroup::process ('generictools|Messages::getConfirm', 
					array ('message'=>'L\' ajout du contenu d\'une rubrique du CMS aura pour conséquence de supprimer toutes les catégories associées à cette catégorie, <br/> Voulez vous continuer : ?',
					   'confirm'=>_url ('heading|sitemap|doEditSitemap', $confirmParams),
					   'cancel'=>_url ('heading|sitemap|editSitemap', array('linkId' => $sitemapLink->getId(), 'parentId' => $sitemapLink->getParentId())))
					);
					
				}else{
					foreach ($children as $link){
						SiteMapServices::deleteSiteMapLinkWithChildren($link->getId());
					}
				}
			}
		}
		
		if(($errors = $sitemapLink->isValid()) !== true){
			$ppo = _ppo();
			$ppo->TITLE_PAGE = 'Edition des sitemap';
			$ppo->link = $sitemapLink;
			$ppo->errors = $errors;
			return _arPPO($ppo, 'sitemap/sitemapedit.php');
		}
		// si on est dans la création d'un sitemap, on le sauve
		if(!$sitemapLink->getParentId() && !$sitemapLink->getId()){
			$siteMap = new SiteMap();
			$siteMap->setSiteMapLink($sitemapLink);
			$id = SiteMapServices::saveSiteMap($siteMap);
		}else {
			$id = SiteMapServices::saveSiteMapLink($sitemapLink);
		}
		
		return _arRedirect(_url('heading|sitemap|editSitemap', array('linkId' => $id, 'parentId' => $sitemapLink->getParentId(), 'success' => 'true')));
	}
	
	/**
	 * Suppression d'un sitemap
	 * @return CopixActionReturn
	 */
	public function processDeleteSitemap(){
		$id = _request('id');
		
		if (!_request ('confirm', false)){
			$sitemap = SiteMapServices::getSiteMap($id);
			_notify ('breadcrumb', array ('path' => array ('#' => 'Suppression')));
	   		return CopixActionGroup::process ('generictools|Messages::getConfirm', 
				array ('message'=>'Êtes vous sûr de vouloir supprimer le sitemap : '.$sitemap->getSiteMapLink()->getCaption(). ' et ses catégories ?',
					   'confirm'=>_url ('heading|sitemap|deleteSitemap', array ('confirm'=>1, 'id'=>$id)),
					   'cancel'=>_url ('heading|sitemap|'))
				);
	   	}else{
	   		SiteMapServices::deleteSiteMap($id);
	   	}
		
		return _arRedirect(_url('heading|sitemap|'));
	}
	
	/**
	 * Suppression d'un lien de sitemap
	 * @return CopixActionReturn
	 */
	public function processDeleteSitemapLink(){
		$parentId = _request('parentId');
		$linkId = _request('linkId');
		$sitemapLink = SiteMapServices::getSiteMapLink($linkId);
		$parent = SiteMapServices::getSiteMapLink($sitemapLink->getParentId());
		if (!_request ('confirm', false)){
			_notify ('breadcrumb', array ('path' => array ('#' => 'Suppression d\'un lien')));
	   		return CopixActionGroup::process ('generictools|Messages::getConfirm', 
				array ('message'=>'Êtes vous sûr de vouloir supprimer le sitemap : '.$sitemapLink->getCaption(). ' et ses catégories ?',
					   'confirm'=>_url ('heading|sitemap|deleteSitemapLink', array ('confirm'=>1, 'parentId'=>$parentId, 'linkId' => $linkId)),
					   'cancel'=>_url ('heading|sitemap|editSitemap', array('parentId'=>$parent->getParentId(), 'linkId' => $parent->getId())))
				);
	   	}else{
	   		SiteMapServices::deleteSiteMapLink($linkId);
	   	}
	   	
		return _arRedirect(_url ('heading|sitemap|editSitemap', array ('parentId'=>$parent->getParentId(), 'linkId' => $parent->getId())));
	}
	
	
	
	/**
	 * Prévisualisation d'un sitemap
	 * @return CopixActionReturn
	 */
	public function processGetSitemap(){
		$sitemap = SiteMapServices::getSiteMap(_request('id'));
		$siteMapLink = $sitemap->getSiteMapLink();
		
		$ppo = _ppo();
		$content = CopixZone::process('heading|SiteMapLink', array('isRoot' => true, 'sitemapLink' => $siteMapLink));
		if($siteMapLink){
			$ppo->MAIN = '<h2> Sitmap '.$siteMapLink->getCaption().'</h2>';
			$ppo->MAIN .= $content;
		}else{
			$ppo->MAIN = '';
		} 
		$ppo->TITLE_PAGE = 'Sitmap '.$siteMapLink->getCaption();
		return _arPPO($ppo, 'generictools|blank.tpl');
	}
	
	/**
	 * Prévisualisation d'un lien de sitemap
	 * @return CopixActionReturn
	 */
	public function processGetSitemapLink(){
		$siteMapLink = SiteMapServices::getSiteMapLink(_request('id'));
		$ppo = _ppo();
		if($siteMapLink){
			$ppo->TITLE_PAGE = 'Element de Sitmap '.$siteMapLink->getCaption();
			$ppo->MAIN = '<h2> Element de  '.$siteMapLink->getCaption().'</h2>';
		}else{
			$ppo->MAIN = '';
		}
		$ppo->MAIN .= CopixZone::process('heading|SiteMapLink', array('isRoot' => true, 'sitemapLink' => $siteMapLink));
		return _arPPO($ppo, 'generictools|blank.tpl');
	}
	
	/**
	 * Permet de monter un lien
	 * @return CopixActionReturn
	 */
	public function processMoveUp(){
		$parentId = _request('parentId');
		$linkId = _request('linkId');
		$siteMapLink = SiteMapServices::getSiteMapLink($linkId);
		SiteMapServices::moveUpSiteMapLink($siteMapLink);
		$parent = SiteMapServices::getSiteMapLink($parentId);
		return _arRedirect(_url ('heading|sitemap|editSitemap', array ('parentId'=>$parent->getParentId(), 'linkId' => $parent->getId())));
	}
	
	/**
	 * Petmet de descendre un lien
	 * @return CopixActionReturn
	 */
	public function processMoveDown(){
		$parentId = _request('parentId');
		$linkId = _request('linkId');
		$siteMapLink = SiteMapServices::getSiteMapLink($linkId);
		SiteMapServices::moveDownSiteMapLink($siteMapLink);
		$parent = SiteMapServices::getSiteMapLink($parentId);
		return _arRedirect(_url ('heading|sitemap|editSitemap', array ('parentId'=>$parent->getParentId(), 'linkId' => $parent->getId())));
	}
	
}