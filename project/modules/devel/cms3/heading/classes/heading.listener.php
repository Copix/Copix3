<?php
/**
 * @package	cms
 * @subpackage	heading
 * @author	Gérald Croës - Sylvain VUIDART
 * @copyright CopixTeam
 * @link      http://copix.org
 * @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Listener du CMS
 * @package cms
 * @subpackage heading
 */
class ListenerHeading extends CopixListener {
	
	/**
	 * 
	 * Evenement en charge en particulier la demande de réindexation
	 * @param unknown_type $pEvent
	 * @param unknown_type $pEventResponse
	 */
	public function processListContent ($pEvent, $pEventResponse){
		$arResults = _doQuery ('select distinct(public_id_hei), type_hei, caption_hei from cms_headingelementinformations where status_hei = :status and type_hei = :type', array (':status'=>HeadingElementStatus::PUBLISHED, ':type'=>'page'));
		foreach ($arResults as $key=>$result){
			$urls[$key] = _url ('heading|default|indexcontent', array ('public_id'=>$result->public_id_hei));
		}
		$pEventResponse->add (array ('url'=>$urls));
	}
	
	/**
	 * 
	 * Evenement appelé quand un menu, portlet, page du CMS est affiché
	 * On met l'evenement en session
	 * @param unknown_type $pEvent
	 * @param unknown_type $pEventResponse
	 */
	public function processCMS_Display ($pEvent, $pEventResponse){
		if (($displayedElementsEvents = CopixSession::get ('displayedElementsEvents', 'CMS')) == null){
			$displayedElementsEvents = array();
		}
		$displayedElementsEvents [] = $pEvent;
		if ($pEvent->getParam('displayToolsBar', true) == false){
			CopixSession::set('displayToolsBar', false, 'CMS');
		}
		CopixSession::set('displayedElementsEvents', $displayedElementsEvents, 'CMS');
	}
}