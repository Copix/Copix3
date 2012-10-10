<?php
/**
* @package   standard
* @subpackage plugin_theme_ajax
* @author   Salleyron Julien
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* Plugin qui permet de garder un theme changé dans tous les appels ajax
* @package   standard
* @subpackage plugin_theme_ajax
*/
class PluginTheme_Ajax extends CopixPlugin {
	public function beforeProcess (&$pAction){
		if (($theme = CopixAJAX::getSession ()->get ('currentTheme')) != null){
			CopixTpl::setTheme ($theme);
		
			//Ajout d'une gestion de tpl par thème
			$config=CopixConfig::instance();
			$theme=CopixTpl::getThemeInformations (CopixTpl::getTheme ());
			if ($theme->tpl!=null) {
	    		$config->mainTemplate   = $theme->tpl;
			}
		}
	}
	
	public function afterDisplay () {
		if (CopixTpl::getTheme () != CopixConfig::get ('admin|defaultThemeId')) {
			CopixAJAX::getSession ()->set ('currentTheme', CopixTpl::getTheme ());
		}
	}
}
?>