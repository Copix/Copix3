<?php
/**
* @package	cms
* @subpackage schedule
* @author	Bertrand Yan, Croes Gérald see copix.aston.fr for other contributors.
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* @package cms
* @subpackage schedule
* ListenerSchedule
*/
class ListenerSchedule extends CopixListener {
   /**
   * Handles the admin event of a given Schedule.
   * People will have to be moderators of the given heading to be enabled to do this.
   */
   function performHeadingAdminBrowsing ($event, & $eventResponse) {
		$servicesHeading  = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
      if (CopixUserProfile::valueOf ($servicesHeading->getPath ($event->getParam ('id')), 'schedule') >= PROFILE_CCV_WRITE) {
         $eventResponse->add (array ("module"=>"schedule", "icon"=>CopixUrl::getResource("img/modules/schedule/schedule.png"), 'shortDescription'=>CopixI18N::get ('schedule|schedule.shortDescription'), 'longDescription'=>CopixI18N::get ('schedule|schedule.longDescription')));
      }
   } 
	
	/**
   * Says if subheadings exists in the given heading
   */
   function performHasContentRequest ($event, & $eventResponse){
       $class = & CopixClassesFactory::getInstanceOf ('schedule|scheduleservice');
		 //echo $class->getLevel ($event->getParam ('id')).'<br>';
       if ($class->getLevel ($event->getParam ('id'))){
           $eventResponse->add (array ('hasContent'=>true));
           return;
       }
   }
   
   /**
   * people who can publish documents, will see all documents from all heading to publish
   */
   function performQuickAdminBrowsing ($event, & $eventResponse){
       //asks the zone to get us the content, if any
      $content = CopixZone::process ('schedule|ScheduleQuickAdmin');
      if (strlen($content) > 0) {
         $eventResponse->add (array ('caption'=>CopixI18N::get('schedule|schedule.messages.titleForQuickAdmin'), 'module'=>'schedule' ,'content'=>$content));
      }
   }
}
?>
