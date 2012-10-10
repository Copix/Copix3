<?php
/**
* @package		standard 
 * @subpackage	htmleditor
* @author	Bertrand Yan
* @copyright CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* Actions pour les barres d'outils du CMS
* @package		standard 
 * @subpackage	htmleditor
*/
class ActionGroupCMSHtmlAreaTools extends CopixActionGroup {
   function processSelectPage (){
      $tpl = new CopixTpl ();
      $tpl->assign ('TITLE_PAGE', CopixI18N::get ('htmleditor.title.pageSelect'));
      $tpl->assignZone ('MAIN', 'htmleditor|SelectPage', array ('onlyLastVersion'=>1, 'editorName'=>CopixRequest::get ('editorName', null, true), 'popup'=>CopixRequest::get ('popup', null, true)));
      return new CopixActionReturn (CopixActionReturn::DISPLAY_IN, $tpl, '|blank.tpl');
   }
}
?>