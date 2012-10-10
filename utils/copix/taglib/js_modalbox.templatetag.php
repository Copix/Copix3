<?php
/**
 * @package    copix
 * @subpackage taglib
 * @author     Guillaume Perréal
 * @copyright  CopixTeam
 * @link       http://www.copix.org
 * @license    http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Block de création d'une boîte de dialogue modale
 * @package		copix
 * @subpackage	taglib
 */
class TemplateTagJS_ModalBox extends CopixTemplateTag  {

	public function process ($pParams, $pContent=null){
		
		$id = $this->getParam('id', uniqid('modalbox'));
		$customContent = $this->getParam('customContent');
		if(!$customContent) {
			$title = $this->getParam('title', '');
			$icon = $this->getParam('icon');
		}
		$openTriggers = $this->getParam('openTriggers');
		$closeTriggers = $this->getParam('closeTriggers');
		$onOpen = $this->getParam('onOpen');
		$onClose = $this->getParam('onClose'); 
		$this->validateParams();

		CopixHTMLHeader::addJSFramework();
		
		$options = array(
			'id' => $id
		);
		if($customContent) {
			$options['contentId'] = $customContent;
		}
		if($openTriggers) {
			if(!is_array($openTriggers)) {
				$openTriggers = split(',', $openTriggers);
			};
			$options['openTriggers'] = $openTriggers;			
		}		
		if($closeTriggers) {
			if(!is_array($closeTriggers)) {
				$closeTriggers = split(',', $closeTriggers);
			};
			$options['closeTriggers'] = $closeTriggers;			
		}
		
		if($customContent) {
			$boxContent = $pContent;
			
		} else {
			$boxContent = '<td width="100%">'.$pContent.'</td>';
			if($icon) {
				$titleColspan = 2;
				$boxContent = '<td style="text-align: center"><img src="'._resource($icon).'"/></td>' . $boxContent;
			} else {
				$titleColspan = 1;
			}
			
			$boxContent = '<tbody><tr>'.$boxContent.'</tr></tbody>';
			
			if($title) {
				$boxContent = 
					'<thead><tr><th width="100%" colspan="'.$titleColspan.'">'.htmlEntities($title).'</th></tr></thead>'.
					$boxContent;
			}
			
			$boxContent = 
				'<table id="'.$id.'_content" class="CopixModalBox_Content CopixTable">'.
				$boxContent.
				'</table>';
				
		}
		
		CopixHTMLHeader::addCSSLink(_resource('styles/taglib/js_modalbox.css'), array('id'=>'taglib_js_modalbox_css'));
		CopixHTMLHeader::addJSLink(_resource('js/taglib/js_modalbox.js'), array('id'=>'taglib_js_modalbox_js'));
		
		$js = new CopixJSWidget();
		
		$js->Copix->ModalBox->register($options);
		
		$events = array();
		if($onOpen) {
			$events['open'] = $js->function_(null, null, $onOpen);
		}
		if($onClose) {
			$events['close'] = $js->function_(null, null, $onClose);
		}		
		if(count($events)) {
			$js->_($id)->addEvents($events);
		}		
		
		CopixHTMLHeader::addJSDOMReadyCode($js);
		
		return '<div id="'.$id.'" style="display:none">'.$boxContent.'</div>';
		
	}
	
}

?>