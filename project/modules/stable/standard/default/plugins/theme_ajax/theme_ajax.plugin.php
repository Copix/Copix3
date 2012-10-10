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
class PluginTheme_Ajax extends CopixPlugin implements ICopixBeforeProcessPlugin, ICopixAfterDisplayPlugin {
	public function getCaption () {
		return 'Conserve le thème dans les appels ajax';
	}

	public function getDescription () {
		return 'Si l\'action a changé le thème, les appels ajax de cette action utiliseront le thème définit par l\'action.';
	}
	
	public function beforeProcess (&$pAction) {
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
		$theme = (CopixPage::get ()->isAdmin ()) ? CopixConfig::get ('default|adminTheme') : CopixConfig::get ('default|publicTheme');
        if (CopixTpl::getTheme () != $theme) {
			CopixAJAX::getSession ()->set ('currentTheme', CopixTpl::getTheme ());
		}
	}
}