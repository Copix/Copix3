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
* DAOPicturesHeading
*/

class DAOPicturesHeadings {
   function findAllWithCaptionHead (){
      $arCat = $this->findAll();
      if (is_array($arCat)) {
         foreach ($arCat as $key=>$cat){
            $arCat[$key]->caption = $this->_getCaptionHeading($cat->id_head);
         }
      }
      return $arCat;
   }
   
   /**
   * get caption of the heading
   */
   function _getCaptionHeading ($id_head) {
      //check if the heading exists. In the mean time, getting its caption
      $dao = & CopixDAOFactory::getInstanceOf ('copixheadings|CopixHeadings');
      if ($heading = $dao->get ($id_head)) {
           $caption_head = $heading->caption_head;
      } else {
           if ($id_head == null){
              $caption_head = CopixI18N::get('copixheadings|headings.message.root');
           }else{
              return false;
           }
      }
      return $caption_head;
   }
}
?>
