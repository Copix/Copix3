<?php
/**
 * @package    moocolorpicker
 * @author     Duboeuf Damien
 */

/**
 * @package		moocolorpicker
 */
class ActionGroupMooColorPicker extends CopixActionGroup {
	
	/**
	 * Renvoi le color picker
	 */
	public function processDefault () {
		
		// Ajout des javascript
		_tag ('mootools');
		CopixHTMLHeader::addCSSLink (_resource ('moocolorpicker|styles/colorpicker.css'));
		CopixHTMLHeader::addJSLink  (_resource ('moocolorpicker|js/colorpicker/nogray_core_vs1.js'));
		CopixHTMLHeader::addJSLink  (_resource ('moocolorpicker|js/colorpicker/nogray_color_picker_vs2.js'));
		
		$ppo = new CopixPPO ();
		return _arDirectPPO ($ppo, 'moocolorpicker|moocolorpicker_selector.tpl');
	}
}
?>