<?php
/**
* @package	 cms
* @subpackage menu_2
* @author	Croes Gérald
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* @package	cms
* @subpackage menu_2
* Listener for CopixHeadings and the CMS.
*/
class ListenerMenu extends CopixListener {
    /**
    * handle the admin browsing event.
    */
    function performHeadingAdminBrowsing ($event, $eventResponse){
      if (CopixUserProfile::valueOf (CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices')->getPath ($event->getParam ('id')), 'copixheadings') >= PROFILE_CCV_SHOW) {
         $eventResponse->add (array ("module"=>"menu_2", "icon"=>CopixUrl::getResource("img/modules/copixheadings/copixheadings.png"), 'shortDescription'=>CopixI18N::get ('menu_2|menu.description'), 'longDescription'=>CopixI18N::get ('menu_2|menu.longdescription')));
      }
    }
}
?>