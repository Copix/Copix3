<?php
/**
* @package	 cms
* @subpackage copixheadings
* @author	Croes Gérald
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* Listener for CopixHeadings and the CMS.
* @package cms
* @subpackage copixheadings
*/
class ListenerCMS extends CopixListener {
    /**
    * Indique s'il existe un contenu dans la rubrique donnée
    */
    function performHasContentRequest ($event, & $eventResponse){
        $class = CopixClassesFactory::getInstanceOf ('copixheadings|CopixHeadingsServices');
        if (count ($class->getLevel ($event->getParam ('id')))){
            $eventResponse->add (array ('hasContent'=>true));
            return;
        }
    }

    /**
    * Indique au module d'administration par rubrique que nous sommes la
    */
    function performHeadingAdminBrowsing ($event, & $eventResponse){
      $servicesHeading  = CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
      if (CopixUserProfile::valueOf ($servicesHeading->getPath ($event->getParam ('id')), 'copixheadings') >= PROFILE_CCV_SHOW) {
         $eventResponse->add (array ("module"=>"copixheadings", "icon"=>CopixUrl::getResource ("img/modules/copixheadings/copixheadings.png"), 'shortDescription'=>CopixI18N::get ('copixheadings|copixheadings.shortDescription'), 'longDescription'=>CopixI18N::get ('copixheadings|copixheadings.longDescription')));
      }
    }
}
?>