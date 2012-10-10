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
 * ListenerNews
 */
class ListenerNews extends CopixListener {

   /**
   * Handles the admin event of a given News.
   * People will have to be moderators of the given heading to be enabled to do this.
   */
   function performHeadingAdminBrowsing ($event, & $eventResponse) {
      $servicesHeading  = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
      if (CopixUserProfile::valueOf ($servicesHeading->getPath ($event->getParam ('id')), 'news') >= PROFILE_CCV_WRITE) {
         $eventResponse->add (array ("module"=>"news", "icon"=>CopixUrl::getResource("img/modules/news/news.png"), 'shortDescription'=>CopixI18N::get ('news|news.shortDescription'), 'longDescription'=>CopixI18N::get ('news|news.longDescription')));
      }
   } 
	/**
   * Says if subheadings exists in the given heading
   */
   function performHasContentRequest ($event, & $eventResponse){
       $class = & CopixClassesFactory::getInstanceOf ('news|NewsService');
       if ($class->getLevel ($event->getParam ('id'))){
           $eventResponse->add (array ('hasContent'=>true));
           return;
       }
   }
   
   /**
   * people who can validate or publish news, will see all news from all heading to publish/validate
   */
   function performQuickAdminBrowsing ($event, & $eventResponse){
       //asks the zone to get us the content, if any
       $content = CopixZone::process ('news|NewsQuickAdmin');
       if (strlen($content) > 0) {
         $eventResponse->add (array ('caption'=>CopixI18N::get('news|news.messages.titleForQuickAdmin'), 'module'=>'news' , 'content'=>$content));
       }
   }
}
?>