<?php
/**
 * @package		spinbutton
 * @author		Duboeuf Damien
 */

/**
 * Pour afficher un spinbutton
 * @package		spinbutton
 */
class TemplateTagSpinButton extends CopixTemplateTag  {
	
	public function process ($pContent = null){
		
		$id         = $this->getParam('id'  ,  'spin_'.uniqid());
		$value      = $this->getParam('value', 0);
		$template   = $this->getParam('template', 'spinbutton|spinbutton.tpl');
		
		// Ajout du template
		$ppo            = new CopixPPO ();
		$tpl            = new CopixTpl ();
		$ppo->id        = $id;
		$ppo->name      = $this->getParam('name', $id);
		$ppo->class     = $this->getParam('class');
		$ppo->integer   = $this->getParam('integer', false);
		$ppo->style     = $this->getParam('style');
		$ppo->extra     = $this->getParam('extra');
		$ppo->step      = $this->getParam('step', 1);
		$ppo->min       = $this->getParam('min', NULL);
		$ppo->useMin    = ($ppo->min !== NULL);
		$ppo->max       = $this->getParam('max', NULL);
		$ppo->useMax    = ($ppo->max !== NULL);
		$ppo->value     = $value;
		
		$ppo->onchange  = $this->getParam('onchange');
		$ppo->onkeyup   = $this->getParam('onkeyup');
		$ppo->onkeydown = $this->getParam('onkeydown');
		
		$tpl->assign ('ppo', $ppo);
		$pContent = $tpl->fetch ($template);
		
		
		
		// Ajout des javascript
		_tag ('mootools');
		
		return $pContent;
	}
	
	
}
