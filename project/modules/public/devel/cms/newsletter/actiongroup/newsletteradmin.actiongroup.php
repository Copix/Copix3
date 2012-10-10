<?php
/**
* @package	cms
* @subpackage newsletter
* @author	Bertrand Yan
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @ignore
 */
require_once (CopixModule::getPath('newsletter').'newsletter/'.COPIX_CLASSES_DIR.'newsletter.services.class.php');
CopixClassesFactory::fileInclude ('cms|ServicesCMSPage');
require_once (COPIX_UTILS_PATH.'CopixEMailer.class.php');

/**
* @package	cms
* @subpackage newsletter
* Admin services for the newsletter.
*/
class ActionGroupNewsletterAdmin extends CopixActionGroup {

    /**
    * Préparation d'envoie d'un mail de test d'une newsletter
    * Params : id > id de la newsletter
    */
    function getPrepareSendTest () {
        if (!isset($this->vars['id'])){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('newsletter.error.missingParameters'),
            'back'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'newsletter'))));
        }

        $tpl = & new CopixTpl ();
        $tpl->assign ('TITLE_PAGE', CopixI18N::get ('newsletter.titlePage.sendTest'));
        $tpl->assign ('MAIN', CopixZone::process ('SendTest', array ('id'=>CopixRequest::get ('id'), 'error'=>CopixRequest::get('error'))));

        return new CopixActionReturn (CopixactionReturn::DISPLAY, $tpl);
    }

    /**
    * .réparation d'envoie d'un mail à un groupe d'une newsletter
    * Params : id > id de la newsletter
    */
    function getPrepareSendToGroup () {
        if (!isset($this->vars['id'])){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('newsletter.error.missingParameters'),
            'back'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'newsletter'))));
        }

        $tpl = & new CopixTpl ();
        $tpl->assign ('TITLE_PAGE', CopixI18N::get ('newsletter.titlePage.sendGroup'));
        $tpl->assign ('MAIN', CopixZone::process ('SendGroup', array('id'=>$this->vars['id'],
                      'error'=>CopixRequest::get('error'), 
                      'online'=>CopixRequest::get ('online'))));

        return new CopixActionReturn (CopixactionReturn::DISPLAY, $tpl);
    }

    /**
    * envoie d'un mail de test d'une newsletter
    * Params : id > id de la newsletter
    */
    function doSendTest () {
        if (!isset($this->vars['id'])){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('newsletter.error.missingParameters'),
            'back'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'newsletter'))));
        }

        if (CopixRequest::get ('test_mail', null, true) <> null) {
            if (!ereg(".+(@.+)(.[[:alpha:]]{2}([[:alpha:]]?))$",$this->vars['test_mail'])){
                $error = CopixI18N::get ('newsletter.error.invalidMail');
                return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get('newsletter|admin|prepareSendTest', array('id'=>$this->vars['id'],'error'=>$error)));
            }
        }else{
            $error = CopixI18N::get ('newsletter.error.missingMail');
            return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get('newsletter|admin|prepareSendTest', array('id'=>$this->vars['id'],'error'=>$error)));
        }

        Copixcontext::push('cms');
        $page = ServicesCMSPage::getOnline($this->vars['id']);
        Copixcontext::pop();

        $service = & new ServicesNewsletter($this->vars['id']);
        $service->sendTest($page, CopixRequest::get ('test_mail'));
        return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get('copixheadings|admin|', array('level'=>$page->id_head, 'browse'=>'newsletter')));
    }
    
    /**
    * envoie d'un mail à un groupe d'une newsletter
    * Params : id > id de la newsletter
    */
    function doSendToGroup () {
        set_time_limit(0);
        if (isset ($this->vars['id'])){
            $idPage = $this->vars['id'];
            $this->_setSessionNewsletterSendingPage ($idPage);
            $this->_setSessionNewsletterSendingGroups (CopixRequest::get('id_nlg'));
            $this->_setSessionNewsletterSendingCopixGroups (CopixRequest::get('id_cgrp'));
            $first = true;
        }else{
            $first = false;
        }
        $idPage        = $this->_getSessionNewsletterSendingPage ();
        $idGroups      = $this->_getSessionNewsletterSendingGroups ();
        $idCopixGroups = $this->_getSessionNewsletterSendingCopixGroups ();
        
        if (!count($idGroups) && !count($idCopixGroups)) {
            $error = CopixI18N::get ('newsletter.error.missingGroup');
            return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get('newsletter|admin|prepareSendToGroup', array('id'=>$idPage, 'error'=>$error)));
        }
        $service = & new ServicesNewsletter();
        //récupération de la pag newsletter à envoyer
        Copixcontext::push('cms');
        $page = ServicesCMSPage::getOnline($idPage);
        Copixcontext::pop();
        //selon si il reste des nes mails à envoyer ou pas...
        if ($service->sendGroup($page,$idGroups,$idCopixGroups,$first, date('Ymd'))){
            $tpl = & new CopixTpl ();
            $tpl->assign ('TITLE_PAGE', CopixI18N::get ('newsletter.titlePage.sendingNewsletter'));
            $tpl->assign ('MAIN', CopixZone::process ('Sending',array('title'=>$page->title_cmsp)));
            CopixHTMLHeader::addOthers ('<meta http-equiv="refresh" content="1;URL='.CopixUrl::get('newsletter|admin|sendToGroup').'" >');
            return new CopixActionReturn (CopixactionReturn::DISPLAY, $tpl);
        }else{
            $this->_updateNewsletterHistory ($idCopixGroups, $idGroups, $idPage, $page);
            return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get('copixheadings|admin|', array('browse'=>'newsletter', 'kind'=>'3', 'level'=>$page->id_head)));
        }
    }
    
    
    /**
    * Update or insert in newletter history table
    */
    function _updateNewsletterHistory ($idCopixGroups, $idGroups, $idPage, $page) {
         $daoSend = & CopixDAOFactory::getInstanceOf ('NewsletterSend');
         $insert  = false;
         //insert or update
         if (!($record = $daoSend->get($idPage, date('Ymd')))) {
            $record  = & CopixDAOFactory::createRecord ('NewsletterSend');
            $insert  = true;

            //store copixgreoup and newslettergroup for history
            $record->id_nlg   = '';
            $record->id_cgrp  = '';
            //copixgroups
            $first            = true;
            foreach ((array)$idCopixGroups as $groupId){
               if (!$first) {
                  $record->id_cgrp .= ';';
               }
               $first = false;
               $record->id_cgrp .= $groupId;
            }
            //newslettergroups
            $first            = true;
            foreach ((array)$idGroups as $groupId){
               if (!$first) {
                  $record->id_nlg .= ';';
               }
               $first = false;
               $record->id_nlg .= $groupId;
            }
         }else{
            $tabGroup      = explode(';', $record->id_nlg);
            $tabCopixGroup = explode(';', $record->id_cgrp);
            
            //update copixgroups array
            $toAdd         = array();
            foreach ((array)$idCopixGroups as $groupId){
               if (!in_array($groupId, $tabCopixGroup)) {
                  $toAdd[] = $groupId;
               }
            }
            $first = count($tabCopixGroup) > 0 ? false : true;
            foreach ((array)$toAdd as $groupId){
               if (!$first) {
                  $record->id_cgrp .= ';';
               }
               $first = false;
               $record->id_cgrp .= $groupId;
            }
            
            //update newslettergroups array
            $toAdd         = array();
            foreach ((array)$idGroups as $groupId){
               if (!in_array($groupId, $tabGroup)) {
                  $toAdd[] = $groupId;
               }
            }
            $first = count($tabGroup) > 0 ? false : true;
            foreach ((array)$toAdd as $groupId){
               if (!$first) {
                  $record->id_nlg .= ';';
               }
               $first = false;
               $record->id_nlg .= $groupId;
            }
         }
         
         $record->id_cmsp         = $idPage;
         $record->date_nls        = date('Ymd');
         $errors = array ();
         $record->htmlcontent_nls = ServicesCMSPage::getPageContent($page, $errors, false);
         $record->title_nls       = $page->title_cmsp;
         if ($insert) {
            $daoSend->insert($record);
         }else{
            $daoSend->update($record);
         }
    }

    /**
    * gets the current sending page.

    */
    function _getSessionNewsletterSendingPage () {
        return isset ($_SESSION['MODULE_NEWSLETTER_SENDING_PAGE']) ? unserialize ($_SESSION['MODULE_NEWSLETTER_SENDING_PAGE']) : null;
    }

    /**
    * sets the current sending page.

    */
    function _setSessionNewsletterSendingPage ($toSet){
        $_SESSION['MODULE_NEWSLETTER_SENDING_PAGE'] = ($toSet !== null ? serialize($toSet) : null);
    }

    /**
    * gets the current newsletter groups.

    */
    function _getSessionNewsletterSendingGroups () {
        return isset ($_SESSION['MODULE_NEWSLETTER_SENDING_GROUPS']) ? unserialize ($_SESSION['MODULE_NEWSLETTER_SENDING_GROUPS']) : null;
    }

    /**
    * sets the current newsletter groups.

    */
    function _setSessionNewsletterSendingGroups ($toSet){
        $_SESSION['MODULE_NEWSLETTER_SENDING_GROUPS'] = ($toSet !== null ? serialize($toSet) : null);
    }
    
    /**
    * gets the current copix groups.

    */
    function _getSessionNewsletterSendingCopixGroups () {
        return isset ($_SESSION['MODULE_NEWSLETTER_SENDING_COPIX_GROUPS']) ? unserialize ($_SESSION['MODULE_NEWSLETTER_SENDING_COPIX_GROUPS']) : null;
    }

    /**
    * sets the current copix groups.

    */
    function _setSessionNewsletterSendingCopixGroups ($toSet){
        $_SESSION['MODULE_NEWSLETTER_SENDING_COPIX_GROUPS'] = ($toSet !== null ? serialize($toSet) : null);
    }

}
?>
