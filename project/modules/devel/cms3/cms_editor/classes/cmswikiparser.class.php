<?php
/**
 * @package		cms_editor
 * @subpackage	cms3
 * @copyright	CopixTeam
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 * @author		Sylvain VUIDART
 * @link		http://www.copix.org
 */
class CmsWikiParser {
	
	public function transform ($pText){
		
		//$text = $this->_wikiRendererTransform($pText);
		$text = _ioClass('cms_editor|MarkdownExtra_Parser')->transform($pText);
		
		$arAlign = array ('left', 'center', 'right', 'justify');
		
		foreach ($arAlign as $alignement){
			//on remplace les <p> par des <p style=""> et pas par des div : pas possible de mettre des <div> dans <p>
			$text = str_replace('<p>{{align-'.$alignement.'}}', '<p style="text-align:'.$alignement.';">', $text);
			$text = str_replace('{{/align-'.$alignement.'}}</p>', '</p>', $text);	
			//si on n'estpas dans un paragraphe
			$text = str_replace('{{align-'.$alignement.'}}', '<p style="text-align:'.$alignement.';">', $text);
			$text = str_replace('{{/align-'.$alignement.'}}', '</p>', $text);	
		}
		
		$blocks = array ('left', 'right');
		foreach ($blocks as $block){
			$text = str_replace('<p>{{block-'.$block.'}}', '<p class="imgcenter" style="float:'.$block.';">', $text);
			$text = str_replace('{{/block-'.$block.'}}</p>', '</p>', $text);
		}
		
		// Remplacement des blocs mentions légales (italique et en plus petit)
		$text = str_replace('{{block-legal}}', '<div class="legal">', $text);
		$text = str_replace('{{/block-legal}}', '</div>', $text);

		$text = str_replace('[br]', '<br />', $text);
        
		preg_match_all('%\(cms:(\d*)(#?[^)]*)\)%', $text, $matches, PREG_SET_ORDER);
		foreach ($matches as $itemToReplace) {
			$url = _url('heading||', array ('public_id' => $itemToReplace[1])) . $itemToReplace[2];
			$text = str_replace ($itemToReplace[0], $url, $text);
		}
		
		preg_match_all('%\(image:(\d*)\)%', $text, $matches, PREG_SET_ORDER);
		foreach ($matches as $itemToReplace){
			$text = str_replace($itemToReplace[0], _url('heading||', array('public_id'=>$itemToReplace[1] )), $text);
		}
		
		$text = str_replace('<img', '<img class="center" ', $text);

        // styles
		preg_match_all('%\{\{block-style class=\'(\w*)\'\}\}%', $text, $matches, PREG_SET_ORDER);
        foreach ($matches as $itemToReplace){
            $text = str_replace($itemToReplace[0], '<span class="'.$itemToReplace[1].'">', $text);
		}
		$text = str_replace('{{/block-style}}', '</span>', $text);
        
		return $text;
	}
	
	private function _wikiRendererTransform ($pText){
		_classInclude('wikirenderer|componentparsehandler');
		$components = $this->_getCmsComponents();
		$tokenizer = _class ('wikirenderer|tokenizer');
		$tokens = $tokenizer->getTokens ($pText, $components, false);
		
		$renderer = _ioClass('wikirenderer|tokenrenderer');
		$data = $renderer->render($tokens, 'HTML', false, false);
		$tpl = new CopixTpl ();
		$errors = '';
		if ($tokenizer->getErrors()->countErrors() > 0) {
			$errors = '<br />Erreurs : <ul><li>'.$tokenizer->getErrors()->asString('</li><li>').'</li></ul>';
		}
		return $errors . $data;
	}
	
	private function _getCmsComponents () {
		$cms_components = array (
		  0 => 'cms_editor|cmscomponenttitle',
		  1 => 'cms_editor|cmscomponentbolditalic',
		  2 => 'cms_editor|cmscomponentbr',
		  3 => 'cms_editor|cmscomponenthr',	  
		  4 => 'cms_editor|cmscomponentlink',
		  5 => 'cms_editor|cmscomponentimage',
	  	  6 => 'cms_editor|cmscomponentulli',
	  	  7 => 'cms_editor|cmscomponentstyle'
		);
		$arComponentToOrder = array ();
		$arComponentNoOrder = array ();
		$arLength = array ();
		foreach ($cms_components as $component) {
			$currentComponent = _class ($component);
			if (($length = $currentComponent->getLength ()) != null) {
				$arComponentToOrder[] = $currentComponent;
				$arLength[] = $length;
			} else {
				$arComponentNoOrder[] = $currentComponent;
			}
		}
		if (count ($arComponentToOrder) >0) {
			array_multisort($arLength, SORT_DESC, $arComponentToOrder);
		}
		return array_merge ($arComponentNoOrder, $arComponentToOrder);
	}
	
}
?>