<?php
/**
* @package	cms
* @subpackage survey
* @author	Bertrand Yan
* @copyright 2001-2005 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
/**
* @package	cms
* @subpackage survey
* Listener for CopixHeadings and the surveyt.
*/
class ListenerContribSurveyHeading extends CopixListener {
   /**
   * handles the admin of a given CopixHeading
   */
   function performHeadingAdminBrowsing ($event, & $eventResponse){
      //asks the zone to get us the content, if any
      $servicesHeading  = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
      if (CopixUserProfile::valueOf ($servicesHeading->getPath ($event->getParam ('id')), 'survey') >= PROFILE_CCV_SHOW) {
         $eventResponse->add (array ("module"=>"survey", "icon"=>CopixUrl::getResource("img/modules/survey/survey.png"), 
                                      'shortDescription'=>CopixI18N::get ('survey|survey.shortDescription'), 'longDescription'=>CopixI18N::get ('survey|survey.longDescription')));
      }
   }

   /**
   * Says if documents are attached to a given heading
   */
   function performHasContentRequest ($event, & $eventResponse){
       $dao  = & CopixDAOFactory::getInstanceOf ('survey|survey');
       $sp   = CopixDAOFactory::createSearchParams ();
       $sp->addCondition ('id_head', '=',$event->getParam ('id'));
       $arSurvey = $dao->findBy ($sp);
       if (count ($arSurvey)){
           $eventResponse->add (array ('hasContent'=>true));
           return;
       }

       //no documents
       $eventResponse->add (array ('hasContent'=>false));
   }
}
?>