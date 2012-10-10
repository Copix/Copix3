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
 *
 */
class TemplateTagTabGroup extends CopixTemplateTag  {
	public function process ($pContent=null){
		$tabGroupId = $this->getParam('id', uniqid('tab'));
		$tabs = $this->requireParam('tabs', null, 'array');		
		$groupClass = $this->getParam('groupClass', '');
		$tabClass = $this->getParam('tabClass', '');
		$selectedClass = $this->getParam('selectedClass', 'CopixTabSelected');
		$onSelect = $this->getParam('onSelect', null, 'string');
		$onUnselect = $this->getParam('onUnselect', null, 'string');
		$default = $this->getParam('default');

		if($default && !isset($tabs[$default])) { 
			_log('[tag tabgroup] invalid default for tabgroup '.$tabGroupId.': '.$default, 'errors');
			$default = null;
		}
		
		$toReturn = array();
		
		$toReturn[] = sprintf('<div class="CopixTabGroup %s" id="%s">', $groupClass, $tabGroupId);
		
		$tabIds = array();
		$tabKeys = array();
		$i=0;
		$tabIndexes = array();
		foreach($tabs as $key=>$caption) {			
			$tabId = $tabGroupId.'_tab'.$i;//preg_replace('/[^\w]/', '_', $key);
			//$tabIds[$tabId] = $key;
			$elementIds[$key] = $tabId;
			$tabIndexes[$key] = $i++;
			$toReturn[] = sprintf(
				'<span class="CopixTab %s %s" id="%s">%s</span>', 
				$tabClass,
				($key == $default) ? $selectedClass : '',
				$tabId,
				_copix_utf8_htmlentities($caption)
			);
		}
		
		$toReturn[] = '</div>';
		
		CopixHTMLHeader::addJSLink(_resource('js/taglib/tabgroup.js'), array('id' => 'taglib_tabgroup_js'));
		
		$params = array(
			'id' => $tabGroupId,
		    'selectedClass' => $selectedClass,
			'tabs' => array_keys($tabs) //$tabIds, 
		);
		if($default) {
			$params['defaultTab'] = $tabIndexes[$default];
		}

		$js = new CopixJSWidget();
		if($onSelect) {
			$params['onSelect'] = $js->function_(null, 'tabId', $onSelect);
		}
		if($onUnselect) {
			$params['onUnselect'] = $js->function_(null, 'tabId', $onUnselect);
		}
		$js->Copix->registerTabGroup($params);
		
		CopixHTMLHeader::addJSDOMReadyCode($js);
		
		return implode("\n", $toReturn);
		
	}
}