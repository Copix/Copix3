<?php
/**
* @package	cms
* @subpackage news
* @author	Bertrand Yan, Croes Gérald
* @copyright 2001-2005 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @package	cms
 * @subpackage news
 * ServicesProfileNews
 */
class ServicesProfileNews {
	function getEnabledIdCategories ($status) {
      $services     = & new NewsService ();
      $arCategories = $services->getCategoriesIdArray ();
      $left = array ();
      foreach ($arCategories as $name) {
         if (CopixUserProfile::valueOf ('site|modules|news|'.$name) >= $status){
            $left [] = $name;
         }
      }
      return $left;
   }
   
   function getEnabledCategories ($status){
      $daoCategories = CopixDAOFactory::getInstanceOf ('NewsCategory');
      $arCategories = $daoCategories->findAll ();
      $left = array ();
      foreach ($arCategories as $obj) {
         if (CopixUserProfile::valueOf ('site|modules|news|'.$obj->id_newc) >= $status){
            $left[] = $obj;
         }
      }
      return $left;
   }
}
?>