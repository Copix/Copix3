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
* Listener for QuickAdmin and the pictures.
*/
class ListenerContribPictures extends CopixListener {

   /**
   * Publicator will see all picture they could publish
   */
   function performQuickAdminBrowsing ($event, & $eventResponse){
        $content = CopixZone::process ('pictures|PicturesQuickAdmin');
         if (strlen($content) > 0) {
            $eventResponse->add (array ('shortDescription'=>CopixI18N::get('pictures|pictures.shortDescription'), 'longDescription'=>CopixI18N::get('pictures|pictures.longDescription'), 'module'=>'pictures' ,'content'=>$content));
         }
   }
   
   /**
   * Create a picture category when a heading is created
   */
   function performHeadingCreated ($event, & $eventResponse){
      $daoCat     = & CopixDAOFactory::getInstanceOf ('pictures|picturesheadings');
      $daoheading = & CopixDAOFactory::getInstanceOf ('copixheadings|copixheadings');
      //get father id
      $heading    = $daoheading->get($event->getParam ('id_head'));
      //set current cat with fathercat
      try {
          $fatherCat  = $daoCat->get($heading->father_head);
      } catch (CopixDBException $e) {
      	  echo $e->getMessage();
      }
      $toAdd      = $fatherCat;
      $toAdd->id_head = $event->getParam ('id_head');
      $daoCat->insert($toAdd);
   }
   
   /**
   * Delete picture category when heading is deleted
   */
   function performHeadingDeleted ($event, & $eventResponse){
      $daoCat     = & CopixDAOFactory::getInstanceOf ('pictures|picturesheadings');
      $daoCat->delete($event->getParam ('id_head'));
   }
   
   /**
   * handles the admin of a given CopixHeading
   */
   function performHeadingAdminBrowsing ($event, & $eventResponse){
      $servicesHeading  = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
      if (CopixUserProfile::valueOf ($servicesHeading->getPath ($event->getParam ('id')), 'pictures') >= PROFILE_CCV_WRITE) {
         $eventResponse->add (array ("module"=>"pictures", "icon"=>CopixUrl::getResource ("img/modules/pictures/pictures.png"), 'shortDescription'=>CopixI18N::get ('pictures|pictures.shortDescription'), 'longDescription'=>CopixI18N::get ('pictures|pictures.longDescription')));
      }
   }

   /**
   * Says if pictures are attached to a given heading
   */
   function performHasContentRequest ($event, & $eventResponse){
       //exception of root with id_head eq null but eq -1 in browser
       $id_head = ($event->getParam ('id') == null) ? -1 : $event->getParam ('id');
       $dao  = & CopixDAOFactory::getInstanceOf ('pictures|pictures');
       $sp   = CopixDAOFactory::createSearchParams ();
       $sp->addCondition ('id_head', '=',$id_head);
       $pictures = $dao->findBy ($sp);
       if (count ($pictures)){
           $eventResponse->add (array ('hasContent'=>true));
           return;
       }

       //no pictures
       $eventResponse->add (array ('hasContent'=>false));
   }
}
?>