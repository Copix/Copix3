<?php
/**
* @package   standard
* @subpackage plugin_theme_module
* @author   Croes Gérald, Salleyron Julien
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* Plugin qui permet d'associer un thème à un module
* @package   standard
* @subpackage plugin_theme_module
*/
class PluginTheme_Module extends CopixPlugin implements ICopixBeforeProcessPlugin {
	/**
	 * Retourne le libellé
	 *
	 * @return string
	 */
	public function getCaption () {
		return 'Change le thème pour tout un module';
	}

	/**
	 * Retourne la description
	 *
	 * @return string
	 */
	public function getDescription () {
		return 'La configuration du plugin permet de spécifier un thème par module.';
	}

	public function beforeProcess (& $pExecParams){
		if ($theme = $this->config->getThemeFor (CopixRequest::get ('module'))){
			CopixTpl::setTheme ($theme);
		}
		//Ajout d'une gestion de tpl par thème
		$config=CopixConfig::instance();
		$theme=CopixTpl::getThemeInformations (CopixTpl::getTheme ());
		if ($theme->tpl!=null) {
    		$config->mainTemplate   = $theme->tpl;
		}
		
	}
}