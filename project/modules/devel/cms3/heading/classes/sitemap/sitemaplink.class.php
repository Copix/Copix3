<?php
/**
 * Classe représentant un lien de sitemap
 * @author fredericb
 *
 */
class SiteMapLink{

	const URL_MODE_CMS = 1;
	const URL_MODE_CUSTOM = 2;
	
	const CHILD_MODE_MANUAL = 1;
	const CHILD_MODE_HEADING = 2;
	
	/**
	 *@var int
	 */
	private $_id;


	/**
	 *@var string
	 */
	private $_caption;

	/**
	 *@var int
	 */
	private $_urlMode;

	/**
	 *@var int
	 */
	private $_cmsLink;

	/**
	 *@var string
	 */
	private $_customUrl;

	/**
	 *@var int
	 */
	private $_childMode;

	/**
	 *@var int
	 */
	private $_parentId;

	/**
	 *@var int
	 */
	private $_cmsHeading;
	
	/**
	*@var int
	*/
	private $_newWindow;
	
	/**
	*@var int
	*/
	private $_position;
	
	public function __construct(){
		$this->_childMode = self::CHILD_MODE_MANUAL;
		$this->_urlMode = self::URL_MODE_CUSTOM;
		$this->_position = 0;
	}
	
	/**
	*@return int
	*/
	public function getPosition(){
		return $this->_position;
	}
	
	/**
	*@param int
	*/
	public function setPosition ($pPosition){
		$this->_position = $pPosition;
	}
	
	/**
	*@return int
	*/
	public function getNewWindow(){
		return $this->_newWindow;
	}
	
	/**
	*@param int
	*/
	public function setNewWindow ($pNewWindow){
		$this->_newWindow = $pNewWindow;
	}

	/**
	 *@return int
	 */
	public function getCmsHeading(){
		return $this->_cmsHeading;
	}

	/**
	 *@param int
	 */
	public function setCmsHeading ($pCmsHeading){
		$this->_cmsHeading = $pCmsHeading;
	}

	/**
	 *@return int
	 */
	public function getParentId(){
		return $this->_parentId;
	}

	/**
	 *@param int
	 */
	public function setParentId ($pParentId){
		$this->_parentId = $pParentId;
	}

	/**
	 *@return int
	 */
	public function getChildMode(){
		return $this->_childMode;
	}

	/**
	 *@param int
	 */
	public function setChildMode ($pChildMode){
		$this->_childMode = $pChildMode;
	}

	/**
	 *@return string
	 */
	public function getCustomUrl(){
		return $this->_customUrl;
	}

	/**
	 *@param string
	 */
	public function setCustomUrl ($pCustomUrl){
		$this->_customUrl = $pCustomUrl;
	}

	/**
	 *@return int
	 */
	public function getCmsLink(){
		return $this->_cmsLink;
	}

	/**
	 *@param int
	 */
	public function setCmsLink ($pCmsLink){
		$this->_cmsLink = $pCmsLink;
	}

	/**
	 *@return int
	 */
	public function getUrlMode(){
		return $this->_urlMode;
	}

	/**
	 *@param int
	 */
	public function setUrlMode ($pUrlMode){
		$this->_urlMode = $pUrlMode;
	}

	/**
	 *@return string
	 */
	public function getCaption(){
		return $this->_caption;
	}

	/**
	 *@param string
	 */
	public function setCaption ($pCaption){
		$this->_caption = $pCaption;
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

	public function isValid () {
		return _validator ('SiteMapLinkValidator')->check ($this);
	}

}