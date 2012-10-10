<?php
/**
* @package	cms
* @author	Croes Gérald
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* @package cms 
* Listener for CopixHeadings and the CMS.
*/
class ListenerContribHeading extends CopixListener {
    /**
    * handles the admin of a given CopixHeading
    */
    function performHeadingAdminBrowsing ($event, & $eventResponse){
        $servicesHeading  = CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
        if (CopixUserProfile::valueOf ('cms', $servicesHeading->getPath ($event->getParam ('id'))) >= PROFILE_CCV_WRITE) {
            $eventResponse->add (array ("module"=>"cms", "icon"=>CopixUrl::getResource ("img/modules/cms/cms.png"), 'shortDescription'=>CopixI18N::get ('cms|cms.shortDescription'), 'longDescription'=>CopixI18N::get ('cms|cms.longDescription')));
        }
    }

    /**
    * handles the browse of a given CopixHeading
    */
    function performHeadingBrowsing ($event, & $eventResponse){
        CopixClassesFactory::fileInclude ('cms|ServicesCMSPage');
        $dao     = CopixDAOFactory::getInstanceOf ('copixheadings_cmspage|CMSIndexPageHeading');
        $content = '';
        if (($record = $dao->get ($event->getParam ('id'))) !== false) {
            $page = ServicesCMSPage::getOnline ($record->id_cmsp);

            if ($page !== null) {
            	$error = array ();//Création du tableau pour pouvoir recevoir les messages d'erreur
                $content = ServicesCMSPage::getPageContent ($page, $error);
            }
        }
        //asks the zone to get us the content, if any
        $eventResponse->add (array ('content'=>$content));
    }

    /**
    * handles when the heading moves
    */
    function performHeadingMoveContent ($event, & $eventResponse) {
        $daoPage  = CopixDAOFactory::getInstanceOf ('cms|CMSPage');
        $daoPage->updateHeading ($event->getParam ('from'), $event->getParam ('to'));
    }

    /**
    * handles when the headings moves.
    */
    function performHeadingMove ($event, & $eventResponse) {
        //nothing to do as the heading does not change itself. Only its parent.
    }

    /**
    * Deletes a heading.
    */
    function performDeleteHeading (){
        $what = $event->getParam ('id_head');
    }

    /**
    * Says if pages or drafts are attached to a given heading
    */
    function performHasContentRequest ($event, & $eventResponse){
        $daoPage  = CopixDAOFactory::getInstanceOf ('cms|CMSPage');
        $pages    = $daoPage->findByHeading ($event->getParam ('id'));
        if (count ($pages)){
            $eventResponse->add (array ('hasContent'=>true));
            return;
        }
        //no pages
        $eventResponse->add (array ('hasContent'=>false));
    }

    /**
    * people who can publish documents, will see all documents from all heading to publish
    */
    function performQuickAdminBrowsing ($event, & $eventResponse){
        //asks the zone to get us the content, if any
        $content = CopixZone::process ('cms|CmsQuickAdmin');
        if (strlen($content) > 0) {
            $eventResponse->add (array ('caption'=>CopixI18N::get('cms|cms.messages.titleForQuickAdmin'), 'module'=>'cms' ,'content'=>$content));
        }
    }
}
?>
