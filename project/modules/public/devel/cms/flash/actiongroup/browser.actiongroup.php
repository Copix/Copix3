<?php
/**
* @package	cms
* @subpackage pictures
* @author	Bertrand Yan
* @copyright 2001-2005 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
/**
* @package	cms
* @subpackage pictures
* Implémentation des actions standards sur la Phototèque
*/
/**
 * @ignore
 */
CopixClassesFactory::fileInclude ('pictures|PicturesSearchParams');
/**
* @package	cms
* @subpackage pictures
* ActionGroupBrowser
*/

class ActionGroupFlashBrowser extends CopixActionGroup {

   /**
   * Affichage de la fen^etre de sélection de la phototèque.
   */
   function getBrowser () {
      $tpl = new CopixTpl ();
      $tpl->assign ('TITLE_BAR', CopixI18N::get ('browser.title'));      
      $tpl->assign ('MAIN', CopixZone::process ('FlashBrowser', array ('popup'=>CopixRequest::get ('popup', '', true), 'select'=>CopixRequest::get('select'), 
            'id_head'=>CopixRequest::get ('id_head', '', true), 'back'=>CopixRequest::get('back'))));

      if (CopixRequest::get ('popup', false, true)) {
         return new CopixActionReturn (CopixactionReturn::DISPLAY_IN, $tpl, '|blank.tpl');
      }else{
         return new CopixActionReturn (CopixactionReturn::DISPLAY, $tpl);
      }
   }
}
?>
