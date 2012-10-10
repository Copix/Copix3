<?php
/**
* @package	cms
* @author	Croes Gérald
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @ignore
 */
CopixClassesFactory::fileInclude ('cms|TemplateFinder');

/**
* @package cms 
* affiche une liste de template pour afficher un choix.
*/
class ZoneTemplateChoice extends CopixZone {
    function _createContent (&$toReturn){
        $tpl    = new CopixTpl ();

        $tpl->assign ('possibleKinds', CopixTpl::find ('cms','*.portlet.*tpl'));

        $tpl->assign ('list', CopixTpl::find ('cms','*.portlet.*tpl'));
        $tpl->assign ('url',  $this->_params['url']);
        $tpl->assign ('back_url',  $this->_params['back_url']);

        $toReturn = $tpl->fetch ('cms|templatechoice.tpl');
        return true;
    }
}
?>