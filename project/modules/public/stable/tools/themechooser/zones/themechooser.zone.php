<?php
/**
* @package		tools
* @subpackage	themechooser
* @author   Salleyron Julien
* @copyright CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * zone permettant de choisir les thèmes grâce une combo
* @package		tools
 * @subpackage	themechooser
 */
class ZoneThemeChooser extends CopixZone {
    function _createContent (&$toReturn) {
        if (! CopixPluginRegistry::isRegistered ('themechooser|themechooser')) {
            $toReturn = "";
            return true;
        }

        $tpl = new CopixTpl ();
        $arThemes = CopixPluginRegistry::getConfig ('themechooser|themechooser')->getThemeList ();

        $arThemesToReturn = array();
        foreach($arThemes as $theme) {
            $arThemesToReturn[] = CopixTpl::getThemeInformations ($theme);
        }
        $selectedTheme = CopixSession::get ('themechooser|theme');

        $tpl->assign ('selectedTheme', $selectedTheme);
        $tpl->assign ('arThemes', $arThemesToReturn);
        $toReturn = $tpl->fetch ('combo.list.tpl');
        return true;
    }
}
?>