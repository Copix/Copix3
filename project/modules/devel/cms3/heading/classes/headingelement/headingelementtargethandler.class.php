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
 * Actions d'administration générales
 * @package cms
 * @subpackage heading
 */
class HeadingElementTargetHandler {
	
	//constante d'ouverture de page : ouverture dans une nouvelle page
	const BLANK = 1;
	//constante d'ouverture de page : ouverture dans une popup
	const POPUP = 2;
	//constante d'ouverture de page : ouverture dans une modalBox
	const SMOOTH_BOX = 3;
	
	//instance privée
	private static $_handler = null;
	
	//tableau des liens vers onglet vide
	private $_arBlankTarget = array ();
	
	//tableau des liens vers popup
	private $_arPopupTarget = array ();
	
	//tableau des liens vers smooth box
	private $_arModalBoxTarget = array ();

	/**
	 * Retourne le libellé d'une valeur de constante
	 *
	 * @param int $pValue
	 * @return string
	 */
	public static function getCaption ($pValue) {
		switch ($pValue) {
			case self::BLANK : return 'Nouvelle fenêtre'; break;
			case self::POPUP : return 'Popup'; break;
			case self::SMOOTH_BOX : return 'SmoothBox'; break;
			default : return 'Page courante'; break;
		}
	}
	
	/**
	 * Constructeur
	 *
	 */
	private function __construct(){
		CopixHTMLHeader::addJSCode("var arBT = [], arPT = [], arSBT = [];");
		CopixHTMLHeader::addJSLink (_resource ('heading|js/tools.js'));
		CopixHTMLHeader::addJSDOMReadyCode("checkBlankTarget (arBT);");
		CopixHTMLHeader::addJSDOMReadyCode("checkPopupTarget (arPT);");
		CopixHTMLHeader::addJSDOMReadyCode("checkSmoothBoxTarget (arSBT);");
	}
	
	/**
	 * Methode publique de récupération de l'handler
	 *
	 * @return HeadingElementTargetHandler
	 */
	public static function getHandler (){
		if (self::$_handler == null){
			self::$_handler = new HeadingElementTargetHandler();
		}
		return self::$_handler; 
	}
	
	/**
	 * Methode d'ajout d'url
	 *
	 * @param String $pUrl
	 * @param String $pParams
	 * @param int $pTargetType
	 */
	public function addUrl ($pUrl, $pParams, $pTargetType = 1){
		switch ($pTargetType){
			//nouvelle page
			case self::BLANK :
				if (empty($this->_arBlankTarget) || !array_key_exists ($pUrl, $this->_arBlankTarget)){
					$this->_arBlankTarget[$pUrl] = $pUrl;
					CopixHTMLHeader::addJSCode ("arBT[".(sizeof($this->_arBlankTarget) - 1)."] = '".$pUrl."';");
				}
				break;
			//popup
			case self::POPUP :
				if (empty($this->_arPopupTarget) || !array_key_exists ($pUrl, $this->_arPopupTarget)){
					$this->_arPopupTarget[$pUrl] = $pUrl;
					CopixHTMLHeader::addJSCode ("arPT[".(sizeof($this->_arPopupTarget) - 1)."] = '".$pUrl."|".$pParams."';");
				}
				break;
			//smooth box
			case self::SMOOTH_BOX :
				_etag('mootools', array ('plugins'=>'smoothbox'));
				if (empty($this->_arModalBoxTarget) || !array_key_exists ($pUrl, $this->_arModalBoxTarget)){
					$this->_arModalBoxTarget[$pUrl] = $pUrl;
					CopixHTMLHeader::addJSCode ("arSBT[".(sizeof($this->_arModalBoxTarget) - 1)."] = '".$pUrl."|".$pParams."';");
				}
		}
	}
}