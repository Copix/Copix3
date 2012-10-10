<?php
/**
* @package   cms
* @subpackage menu_2
* @author   Croes Gérald
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* @package   cms
* @subpackage menu_2
* PluginMenu_2
*/
class PluginMenu_2 extends CopixPlugin {
   function PluginMenu_2 ($config){
     parent::CopixPlugin ($config);
   }
   function beforeProcess(){
      if (isset($_GET['selectedMenu'])){
         $_SESSION['MODULE_MENU_2_SELECTEDMENU'] = $_GET['selectedMenu'];
      }
   }
   function getPath(){
      if (isset($_SESSION['MODULE_MENU_2_SELECTEDMENU'])){
         CopixContext::push ('menu_2');
         $dao = & CopixDAOFactory::getInstanceOf ('menu_2|menu');
         $arMenu = $dao->getPath($_SESSION['MODULE_MENU_2_SELECTEDMENU']);
         CopixContext::pop ();
         $arToReturn=array();
         foreach ($arMenu as $key=>$elem) {
         	$arToReturn[]=$elem->id_menu;
         }
         return $arToReturn;
      }else{
         return array(1);
      }
   }
}
?>
