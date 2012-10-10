<?php
/**
 * @package		cms_editor
 * @subpackage	cms3
 * @copyright	CopixTeam
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 * @author		Sylvain VUIDART
 * @link		http://www.copix.org
 */
class ActionGroupAjax extends CopixActionGroup {
	
	public function processGetTextPreview (){
		$text = _request('text');
		$newText = _class('cms_editor|cmswikiparser')->transform($text);
		
		$ppo = new CopixPPO ();
		$ppo->MAIN = $newText;
		return _arDirectPPO($ppo, 'generictools|blanknohead.tpl');
	}
	
	public function processGetWysiwygPreview (){
		$text = _request('text');
		$newText = _class('cms_editor|cmswysiwygparser')->transform($text);
		
		$ppo = new CopixPPO ();
		$ppo->MAIN = $newText;
		return _arDirectPPO($ppo, 'generictools|blanknohead.tpl');
	}
	
	public function processGetElementChooser (){
		$ppo = new CopixPPO ();
		$ppo->MAIN = CopixZone::process ('heading|headingelement/headingelementchooser', array('selectHeading'=>false, 'showAnchor'=>true, 'inputElement'=>'wysiwygEditor_'._request('name'), 'identifiantFormulaire'=>_request('name'), 'showSelection'=>true, 'mode'=>_request('mode'), 'multipleSelect'=>false));
		return _arDirectPPO($ppo, 'generictools|blank.tpl');
	}
	
	public function processGetEditor (){
		$ppo = _ppo();
		switch (_request('editor')){
			case PortletText::WIKI_EDITOR :
				$ppo->MAIN = CopixZone::process ('cms_editor|cmswikieditor', array('name'=>_request('name'), 'value'=>_request('value')));
				return _arDirectPPO($ppo, 'generictools|blank.tpl');
				
			case PortletText::WYSIWYG_EDITOR :
				$ppo->MAIN = CopixZone::process ('cms_editor|cmswysiwygeditor', array('name'=>_request('name'), 'value'=>_request('value')));
				return _arDirectPPO($ppo, 'generictools|blank.tpl');
		}	
	}
	
	public function processGetHelpWiki (){
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = "Guide d'utilisation du wiki";
		return _arPPO($ppo, array('template' => 'helpwiki.php', 'mainTemplate' => '|popup.php'));
	}
}