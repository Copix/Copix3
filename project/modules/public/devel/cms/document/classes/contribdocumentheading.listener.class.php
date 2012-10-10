<?php
/**
* @package	 cms
* @subpackage document
* @author	Bertrand Yan, Croes Gérald
* @copyright 2001-2005 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* @package	 cms
* @subpackage document
* Listener for CopixHeadings and the document.
*/
class ListenerContribDocumentHeading extends CopixListener {
   /**
   * handles the admin of a given CopixHeading
   */
   function performHeadingAdminBrowsing ($event, & $eventResponse){
      $servicesHeading  = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
      if (CopixUserProfile::valueOf ($servicesHeading->getPath ($event->getParam ('id')), 'document') >= PROFILE_CCV_WRITE) {
         $eventResponse->add (array ("module"=>"document", "icon"=>CopixUrl::getResource("img/modules/document/document.png"), 'shortDescription'=>CopixI18N::get ('document|document.shortDescription'), 'longDescription'=>CopixI18N::get ('document|document.longDescription')));
      }
   }

   /**
   * Says if documents are attached to a given heading
   */
   function performHasContentRequest ($event, & $eventResponse){
       $dao  = & CopixDAOFactory::getInstanceOf ('document|document');
       $sp   = CopixDAOFactory::createSearchParams ();
       $sp->addCondition ('id_head', '=',$event->getParam ('id'));
       $documents = $dao->findBy ($sp);
       if (count ($documents)){
           $eventResponse->add (array ('hasContent'=>true));
           return;
       }

       //no documents
       $eventResponse->add (array ('hasContent'=>false));
   }
   
   /**
   * people who can publish documents, will see all documents from all heading to publish
   */
   function performQuickAdminBrowsing ($event, & $eventResponse){
       //asks the zone to get us the content, if any
       $content = CopixZone::process ('document|DocumentQuickAdmin');
       if (strlen($content) > 0) {
         $eventResponse->add (array ('caption'=>CopixI18N::get('document|document.messages.titleForQuickAdmin'), 'module'=>'document' ,'content'=>$content));
       }
   }
   
   /**
   * sets the current heading.

   */
   function _setSessionHeading ($toSet){
      $_SESSION['MODULE_DOCUMENT_CURRENT_HEADING'] = $toSet !== null ? serialize($toSet) : null;
   }
}
?>
