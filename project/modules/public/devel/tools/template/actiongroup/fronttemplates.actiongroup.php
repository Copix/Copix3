<?php
/**
* @package	template
* @author	Croes Gérald
* @copyright 2001-2006 CopixTeam
* @link		http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.htmlGNU Leser General Public Licence, see LICENCE file
*/
class ActionGroupFrontTemplates extends CopixActionGroup {
    /**
    * Gets the template list
    */
	function getTemplateList() {
		$tpl = & new CopixTpl ();
		$tpl->assign ('TITLE_PAGE', CopixI18N::get ('template.titlePage.list'));
		$tpl->assign ('MAIN', CopixZone::process ('templateList',array('selectedQualifier'=>CopixRequest::get ('selectedQualifier', null, true), 
		                                                               'selectedTheme'=>CopixRequest::get ('selectedTheme', null, true))));
		return new CopixActionReturn (CopixActionReturn::DISPLAY, $tpl);
	}
	
	/**
	* Gets the theme list
	*/
	function getThemeList (){
		$tpl = new CopixTpl ();
		$tpl->assign ('TITLE_PAGE', CopixI18N::get ('template.titlePage.selectTheme'));
		$tpl->assign ('MAIN', CopixZone::process ('ThemeList', array ('selectedTheme'=>CopixRequest::get ('selectedTheme', CopixConfig::get('defaultThemeId'), true), 
	                                                              'validUrl'=>CopixRequest::get ('validUrl', CopixUrl::get ('template||setTheme'), true))));
		return new CopixActionReturn (CopixActionReturn::DISPLAY, $tpl);
	}

	/**
	* Sets the theme as the default theme
	*/
	function doSelectTheme (){
		$theme = CopixRequest::get ('id_ctpt', null, true);
		CopixConfig::set ('defaultThemeId', $theme);
		
		return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('template||themeList'));
	}
}
?>