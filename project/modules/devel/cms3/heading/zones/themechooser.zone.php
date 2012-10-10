<?php

class ZoneThemeChooser extends CopixZone {
	
	public function _createContent (&$toReturn){

		$public_id = $this->getParam('public_id', null);
		if ($public_id == null){
			return "";
		}
		
		$theme_inherited_from = false;
		$element_theme = _ioClass ('HeadingElementInformationServices')->getTheme ($public_id, $theme_inherited_from);
		$theme_template = _ioClass ('HeadingElementInformationServices')->getTemplate ($public_id);
		if (!$element_theme) {
			$element_theme = CopixConfig::get('default|publicTheme');
			$theme_template = 'default|main.php';
		}
		$theme = CopixTheme::getInformations ($element_theme);
		$templates = $theme->getTemplates ();
		if ($theme_inherited_from != null) {
			$theme_inherited_from = _ioClass ('HeadingElementInformationServices')->get ($theme_inherited_from);
		}
		$themes = array ();
		foreach (CopixTheme::getList () as $id => $caption) {
			$themes[] = CopixTheme::getInformations ($id);
		}
		$tpl = $this->getParam('tpl', new CopixTpl());
		$tpl->assign ('theme', $theme);
		$tpl->assign ('themes', $themes);
		$tpl->assign ('theme_inherited_from', $theme_inherited_from);
		$tpl->assign ('theme_template', $theme_template);
		$tpl->assign ('theme_template_caption', $templates[substr ($theme_template, strpos ($theme_template, '|') + 1)]);
		$toReturn = $tpl->fetch ($this->getParam("template", 'heading|informations/themes.php'));
	}
	
}