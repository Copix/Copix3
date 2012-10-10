<?php
/**
 * Classe de service pour les sitemap
 * @author fredericb
 *
 */
class SiteMapServices{

	/**
	 * Retourne la liste des sitemap
	 * @return array
	 */
	public static function getSiteMapList(){
		$list = array();
		$result =  DAOcms_sitemaps::instance ()->findAll();
		foreach ($result as $record){
			$sitemap = new SiteMap();
			$sitemap->setId($record->id);
			$sitemap->setSiteMapLink(self::getSiteMapLink($record->sitemap_link));
			$list[] = $sitemap;
		}
		return $list;
	}

	/**
	 * Retourne un sitemap en fonction de son id
	 * @param int $id
	 * @return SiteMap
	 */
	public static function getSiteMap($id){
		$record = DAOcms_sitemaps::instance ()->get($id);
		
		$sitemap  = new SiteMap();
		if($record){
			$sitemap->setId($record->id);
			$sitemap->setSiteMapLink(self::getSiteMapLink($record->sitemap_link));
		}
		return $sitemap;
	}

	/**
	 * Permet de sauver un sitemap 
	 * @param SiteMap $sitemap
	 * @return int
	 */
	public static function saveSiteMap($sitemap){
		$linkId = self::saveSiteMapLink($sitemap->getSiteMapLink());
		$siteMapRecord = DAORecordcms_sitemaps::create ();
		$siteMapRecord->sitemap_link = $linkId;
		$dao = DAOcms_sitemaps::instance ();
		$dao->insert($siteMapRecord);
		return $linkId;
	}
	/**
	 * Permet de sauver un lien de sitemap
	 * @param SiteMapLink $sitemapLink
	 * @return int
	 */
	public static function saveSiteMapLink($sitemapLink){
		$siteMapLinkRecord = self::_createRecordFromSiteMapLink($sitemapLink);
		$dao = DAOcms_sitemaps_links::instance ();
		if(!$siteMapLinkRecord->id){
			// pour la position, on ne traite pas la racine
			if($sitemapLink->getParentId()){
				$neighbours = self::getChildHeading($sitemapLink->getParentId());
				$maxPosition = -1;
				foreach ($neighbours as $neighbour){
					$maxPosition = max($neighbour->getPosition(), $maxPosition);
				}
				$siteMapLinkRecord->position = $maxPosition + 1; 
			}
			$dao->insert($siteMapLinkRecord);
		}else{
			$dao->update($siteMapLinkRecord);
		}
		return $siteMapLinkRecord->id;
	}
	
	
	

	/**
	 * Supprime un sitemap
	 * @param SiteMap $sitemap
	 * @return void
	 */
	public static function deleteSiteMap($id){
		self::getSiteMap($id);
		$siteMap = self::getSiteMap($id);
		$dao = DAOcms_sitemaps::instance ();
		$dao->deleteby(_daoSP()->addCondition('id', '=', $id));
		self::deleteSiteMapLinkWithChildren($siteMap->getSiteMapLink()->getId());
	}
	
	/**
	 * Supprime un lien de sitemap
	 * @param int $id
	 * @return void
	 */
	public static function deleteSiteMapLinkWithChildren($id){
		
		$dao = DAOcms_sitemaps_links::instance ();
		$dao->deleteby(_daoSP()->addCondition('id', '=', $id));
		$records = $dao->findBy(_daoSP()->addCondition('parent_id','=',$id));
		
		foreach ($records as $record){
			self::deleteSiteMapLinkWithChildren($record->id);
		}
	} 
	
	public static function deleteSiteMapLink($id){
		$siteMapLink = self::getSiteMapLink($id);
		self::deleteSiteMapLinkWithChildren($id);
		self::_reOrganizeChildren($siteMapLink->getParentId());
	}
	
	private static function _reOrganizeChildren($id){
		$children = self::getChildHeading($id);
		foreach ($children as $position => $child){
				$child->setPosition($position);
				self::saveSiteMapLink($child);
		}
	}
	

	/**
	 * Récupère les enfants d'un lien
	 * @param int $parentId
	 * @return array
	 */
	public static function getChildHeading($parentId){
		$list = array();
		$records = DAOcms_sitemaps_links::instance ()->findBy(_daoSP()
		->addCondition('parent_id','=',$parentId)
		->orderBy('position')
		);
		foreach ($records as $record){
			$list[] = self::_createSiteMapLinkFromRecord($record);
		}
		return $list;
	}

	/**
	 * Récupère un onjet lien en fonction de son id
	 * @param int $id
	 * @return SiteMapLink
	 */
	public static function getSiteMapLink($id){
		$record = DAOcms_sitemaps_links::instance ()->get($id);
		if($record){
			return self::_createSiteMapLinkFromRecord($record);
		}else {
			return null;
		}
	}
	
	/**
	 * Permet de monter un lien
	  
	 * @param SiteMapLink $pSiteMapLink
	 * @return void
	 */
	public static function moveUpSiteMapLink($pSiteMapLink){
		if($pSiteMapLink->getPosition() != 0){
			$records = DAOcms_sitemaps_links::instance ()->findBy(_daoSP()
				->addCondition('parent_id', '=', $pSiteMapLink->getParentId())
				->addCondition('position', '=', $pSiteMapLink->getPosition() - 1)
			);
			$previous = self::_createSiteMapLinkFromRecord($records[0]);
			$pSiteMapLink->setPosition($pSiteMapLink->getPosition() - 1);
			$previous->setPosition($previous->getPosition() + 1);
			self::saveSiteMapLink($pSiteMapLink);
			self::saveSiteMapLink($previous);
		}
	}
	
	/**
	 * Permet de descendre un lien
	 * @param SiteMapLink $pSiteMapLink
	 * @return void
	 */
	public static function moveDownSiteMapLink($pSiteMapLink){
		$neighbours = self::getChildHeading($pSiteMapLink->getParentId());
		$last = end($neighbours);
		if($pSiteMapLink->getPosition() != $last->getPosition()){
			$records = DAOcms_sitemaps_links::instance ()->findBy(_daoSP()
				->addCondition('parent_id', '=', $pSiteMapLink->getParentId())
				->addCondition('position', '=', $pSiteMapLink->getPosition() + 1)
			);
			$next = self::_createSiteMapLinkFromRecord($records[0]);
			$pSiteMapLink->setPosition($pSiteMapLink->getPosition() + 1);
			$next->setPosition($next->getPosition() - 1);
			self::saveSiteMapLink($pSiteMapLink);
			self::saveSiteMapLink($next);
		}
	}
	
	
	/**
	 * Crée un record cms_sitemaps_links à partir d'un objet SiteMapLink
	 * @param SiteMapLink $siteMapLink
	 * @return  CopixRecord
	 */
	private static function _createRecordFromSiteMapLink($siteMapLink){
		$record = DAORecordcms_sitemaps_links::create ();
		$record->id = $siteMapLink->getId();
		$record->caption = $siteMapLink->getCaption();
		$record->url_mode = $siteMapLink->getUrlMode();
		$record->cms_link = $siteMapLink->getCmsLink();
		$record->custom_url = $siteMapLink->getCustomUrl();
		$record->child_mode = $siteMapLink->getChildMode();
		$record->parent_id = $siteMapLink->getParentId();
		$record->cms_heading = $siteMapLink->getCmsHeading();
		$record->new_window = $siteMapLink->getNewWindow();
		$record->position = $siteMapLink->getPosition();
		return $record;
	}
	
	/**
	 * Crée un objet SiteMapLink à partir d'un record
	 * @param CopixRecord $record
	 * @return SiteMapLink
	 */
	private static function _createSiteMapLinkFromRecord($record){
		$link = new SiteMapLink();
		$link->setId($record->id);
		$link->setCaption($record->caption);
		$link->setUrlMode($record->url_mode);
		$link->setCmsLink($record->cms_link);
		$link->setCustomUrl($record->custom_url);
		$link->setChildMode($record->child_mode);
		$link->setParentId($record->parent_id);
		$link->setCmsHeading($record->cms_heading);
		$link->setNewWindow($record->new_window);
		$link->setPosition($record->position);
		return $link;
	}

}