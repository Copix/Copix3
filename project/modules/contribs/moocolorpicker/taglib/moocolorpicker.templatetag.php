<?php
/**
 * @package		moocolorpicker
 * @author		Duboeuf Damien
 */

/**
 * Pour afficher un selectionneur de couleur
 * @package		moocolorpicker
 */
class TemplateTagMooColorPicker extends CopixTemplateTag  {
	
	public function process ($pContent = null){
		
		$id         = $this->getParam('id'  ,  uniqid());
		$value      = $this->getParam('value', '#000000');
		$template   = $this->getParam('template', 'moocolorpicker|moocolorpicker.tpl');
		
		// Ajout du template
		$ppo            = new CopixPPO ();
		$tpl            = new CopixTpl ();
		$ppo->id        = $id;
		$ppo->name      = $this->getParam('name', $id);
		$ppo->class     = $this->getParam('class');
		$ppo->style     = $this->getParam('style');
		$ppo->extra     = $this->getParam('extra');
		$ppo->value     = $value;
		$tpl->assign ('ppo', $ppo);
		$pContent = $tpl->fetch ($template);
		
		
		// Ajout des javascript
		_tag ('mootools');
		CopixHTMLHeader::addJSLink  (_resource ('moocolorpicker|js/colorpicker/nogray_core_vs1.js'));
		CopixHTMLHeader::addJSLink  (_resource ('moocolorpicker|js/colorpicker/nogray_color_picker_vs2.js'));
		
		
		$js = new CopixJSWidget();
		$js->Copix->register_colorpicker ($id, $value);
		CopixHTMLHeader::addJSDOMReadyCode($js);
		
		return $pContent;
	}
	
	
}