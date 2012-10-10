<?php
/**
* @package		tools 
 * @subpackage	themechooser
* @author		Salleyron Julien
* @copyright	CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Choix d'un thème par utilisateur
* @package		tools
 * @subpackage	themechooser
 */
class ActionGroupDefault extends CopixActionGroup {
    /**
     * Page de choix de thème
     */  
    function processDefault (){
        $ppo = new CopixPPO ();
        $ppo->TITLE_PAGE = _i18n ('admin|themes.titlePage.selectTheme');

        $arThemes = CopixPluginRegistry::getConfig ('themechooser|themechooser', true)->getThemeList();
        $ppo->arThemes = array();
        foreach ($arThemes as $theme) {
            $ppo->arThemes[] = CopixTpl::getThemeInformations ($theme);
        }
        $ppo->selectedTheme = CopixSession::get ('themechooser|theme');
		return _arPpo ($ppo, 'theme.list.tpl');
	}

	/**
	* Définition du thème à utiliser
	*/
	function processDoSelectTheme (){
		CopixSession::set ('themechooser|theme', CopixRequest::getInArray ('id_ctpt', CopixPluginRegistry::getConfig('themechooser|themechooser', true)->getThemeList(), true));
		return _arRedirect (_url ());
	}
}
?>