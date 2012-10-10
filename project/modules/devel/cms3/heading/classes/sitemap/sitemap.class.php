<?php
/**
 * Classe représentant un sitemap
 * @author fredericb
 *
 */
class SiteMap{

	/**
	*@var int
	*/
	private $_id;
	
	/**
	*@var SitemapLink
	*/
	private $_siteMapLink;
	
	function __construct(){
		$this->_siteMapLink = new SiteMapLink();
	}
	
	/**
	*@return SitemapLink
	*/
	public function getSiteMapLink(){
		return $this->_siteMapLink;
	}
	
	/**
	*@param SitemapLink
	*/
	public function setSiteMapLink ($pSiteMapLink){
		$this->_siteMapLink = $pSiteMapLink;
	}
	
	/**
	*@return int
	*/
	public function getId(){
		return $this->_id;
	}
	
	/**
	*@param int
	*/
	public function setId ($pId){
		$this->_id = $pId;
	}
	
}