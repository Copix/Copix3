<?php
/**
* @package	cms
* @subpackage news
* @author	Bertrand Yan, Croes Gérald
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @package	cms
 * @subpackage news
 * NewsWorkflow
 */
class NewsWorkflow {
   function getCreate (){
      return 0;
   }

   function getValid (){
      return 1;
   }

   function getPublish (){
      return 2;
   }
   
   function getTrash (){
      return 3;
   }
   
   function getBest ($id_newc){
      $value = CopixUserProfile::valueOf ('site|modules|news|'.$id_newc);
      switch ($value){
         case PROFILE_CCV_NONE:
         case PROFILE_CCV_SHOW:
         case PROFILE_CCV_READ:
         case PROFILE_CCV_WRITE:
            return 0;
            break;

         case PROFILE_CCV_VALID:
            return 1;
            break;

         case PROFILE_CCV_PUBLISH:
         case PROFILE_CCV_MODERATE:
         case PROFILE_CCV_ADMIN:
            return 1;
            break;
      }
   }
}
?>