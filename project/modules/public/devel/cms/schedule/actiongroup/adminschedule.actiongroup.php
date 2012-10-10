<?php
/**
* @package cms
* @subpackage schedule
* @author	Bertrand Yan, Croes Gérald see copix.aston.fr for other contributors.
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @ignore
 */
CopixClassesFactory::fileInclude ('schedule|SearchType');

/**
* @package cms
* @subpackage schedule
* ActionGroupAdminSchedule
*/
class ActionGroupAdminSchedule extends CopixActionGroup{
   /**
    * says if we can paste the cutted element (if any) in the given heading (id)
    * @param int this->vars['level'] the heading where we wants to paste the cutted element into
    * @return bool
    */
    function canPaste (){
        if (!$this->_hasCut()){
            return false;
        }

        $dao = & CopixDAOFactory::create ('scheduleevents');

        if (!$toPaste = $dao->get ($this->_getCut())){
            $this->_clearCut ();
            return false;
        }

        //is there a given destination ?
        if ((! isset ($this->vars['level'])) || (strlen (trim ($this->vars['level'])) == 0)) {
            $this->vars['level'] = null;
        }

        //does the destination heading exists ?
        if ($this->vars['level'] !== null){
            $dao = & CopixDAOFactory::getInstanceOf ('copixheadings|CopixHeadings');
            if (($record = $dao->get ($this->vars['level'])) === false) {
                return false;
            }
        }

        //do we have write permissions on the destination ?
        $headingProfileServices = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
        if (CopixUserProfile::valueOf ($headingProfileServices->getPath($this->vars['level']),
        'schedule') < PROFILE_CCV_WRITE) {
            return false;
        }
        //do we have write permissions on the cutted element ?
        if (CopixUserProfile::valueOf ($headingProfileServices->getPath($toPaste->id_head),
        'schedule') < PROFILE_CCV_PUBLISH) {
            return false;
        }
        return true;
    }

    /**
    * paste the element, from the session.
    * we have to have an element in the pseudo clipboard.
    * we have to have write permissions on both destination level and "from" level
    * The document have to exists. We move all the version of the document.
    * The dest heading must exists
    */
    function doPaste () {
        //is there a given destination ?
        if ((! isset ($this->vars['level'])) || (strlen (trim ($this->vars['level'])) == 0)) {
            $this->vars['level'] = null;
        }

        //do we have an element in the clipboard ?
        if (!$this->_hasCut()){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('schedule.error.cannotFindCut'),
            'back'=>CopixUrl::get ('copixheadings|admin|', array ('browse'=>'schedule', 'level'=>$this->vars['level']))));
        }

        $dao = & CopixDAOFactory::create ('scheduleevents');
        if (!$toPaste = $dao->get ($this->_getCut())){
            $this->_clearCut ();
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('schedule.error.cannotGetEvntBack'),
            'back'=>CopixUrl::get ('copixheadings|admin|', array('browse'=>'schedule', 'level'=>$this->vars['level']))));
        }

        //does the destination heading exists ?
        if ($this->vars['level'] !== null){
            $daoHeading = & CopixDAOFactory::getInstanceOf ('copixheadings|CopixHeadings');
            if (($record = $daoHeading->get ($this->vars['level'])) === false) {
                $this->_clearCut ();
                return CopixActionGroup::process ('genericTools|Messages::getError',
                array ('message'=>CopixI18N::get ('copixheadings|admin.error.cannotFindHeading')));
            }
        }

        //do we have write permissions on the destination ?
        $headingProfileServices = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
        if (CopixUserProfile::valueOf ($headingProfileServices->getPath($this->vars['level']),
        'schedule') < PROFILE_CCV_WRITE) {
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('schedule.error.cannotPasteHere')));
        }

        //do we have write permissions on the cutted element ?
        if (CopixUserProfile::valueOf ($headingProfileServices->getPath($toPaste->id_head),
        'schedule') < PROFILE_CCV_PUBLISH) {
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('schedule.error.cannotMoveElement')));
        }

        $dao->moveHeading ($toPaste->id_evnt, $this->vars['level']);
        $this->_clearCut ();
        return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array ('browse'=>'schedule', 'level'=>$this->vars['level'])));
    }

   /**
    * cuts a document
    * We have to have the rights to write in the given heading the cutted element belongs to to be able to do so.
    * @param int id_doc the document we wants to cut
    */
    function doCut () {
        //No given element.
        if (! isset ($this->vars['id_evnt'])) {
            return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin', array('browse'=>'schedule')));
        }

        $dao = & CopixDAOFactory::create ('scheduleevents');
        if (!$toCut = $dao->get ($this->vars['id_evnt'])){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('schedule.error.cannotGetEvntBack'),
            'back'=>CopixUrl::get ('copixheadings|admin|', array('browse'=>'schedule'))));
        }
        //check if the user has the rights
        $servicesHeadings = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
        if (CopixUserProfile::valueOf ($servicesHeadings->getPath ($toCut->id_head), 'schedule') < PROFILE_CCV_WRITE) {
           return CopixActionGroup::process ('genericTools|Messages::getError',
           array ('message'=>CopixI18N::get ('schedule.error.notAnAuthorizedHead'),
           'back'=>CopixUrl::get ('copixheadings|admin|', array('browse'=>'schedule','level'=>$toCut->id_head))));
        }

        //ok, we can cut the element
        $this->_setCut ($this->vars['id_evnt']);
        return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array ('browse'=>'schedule', 'level'=>$toCut->id_head)));
    }

   /**
    * cuts the element
    */
    function _setCut ($id_forms) {
        $_SESSION['MODULE_SCHEDULE_CUT'] = $id_forms;
    }

    /**
    * gets the cutted heading
    */
    function _getCut () {
        if (isset ($_SESSION['MODULE_SCHEDULE_CUT'])){
            return $_SESSION['MODULE_SCHEDULE_CUT'];
        }else{
            return null;
        }
    }

    /**
    * says if there's a cutted element
    */
    function _hasCut (){
        return isset ($_SESSION['MODULE_SCHEDULE_CUT']);
    }

    /**
    * clear the pseudo clipboard
    */
    function _clearCut (){
        session_unregister('MODULE_SCHEDULE_CUT');
    }
    
   /**
   * doCreateEvnt
   * prepare a new document to edit
   * @param string id_head the heading to put the event in
   */
   function doCreateEvnt() {
      if (!isset($this->vars['id_head'])){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('schedule.error.missingParameters'),
            'back'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'schedule'))));
      }
      //check if the user has the rights to write pages into the given heading.
      $servicesHeadings = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
      if (CopixUserProfile::valueOf ($servicesHeadings->getPath ($this->vars['id_head']), 'schedule') < PROFILE_CCV_WRITE) {
         return CopixActionGroup::process ('genericTools|Messages::getError',
         array ('message'=>CopixI18N::get ('schedule.error.notAnAuthorizedHead'),
         'back'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'schedule', 'level'=>$this->vars['id_head']))));
      }
      
      // init a new document
      $evnt  = & CopixDAOFactory::createRecord ('ScheduleEvents');
      $evnt->editionkind_evnt = CopixConfig::get ('schedule|editionKind');
      $evnt->id_head          = strlen($this->vars['id_head'] > 0) ? $this->vars['id_head'] : null;
      $this->_setSessionEvnt($evnt);
      return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('schedule|admin|editEvnt'));
   
   }
   
   /**
    * doPrepareEditEvnt
    * prepare an existing evnt to edit
    * @param string id_evnt
    */
   function doPrepareEditEvnt() {
   	if (!isset ($this->vars['id_evnt'])){
         return CopixActionGroup::process ('genericTools|Messages::getError',
               array ('message'=>CopixI18N::get ('schedule.error.notAnAuthorizedHead'),
               'back'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'schedule'))));
      }
      $toEdit = $this->_loadEvnt($this->vars['id_evnt']);
      if (isset($this->vars['back']) && strlen($this->vars['back']) > 0) {
            $toEdit->__Copix_Internal_UrlBack = $this->vars['back'];
      }
      $this->_setSessionEvnt($toEdit);
      return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('schedule|admin|editEvnt'));
   }
   
   /**
    * getAdminEvent
    * display evnt form for edited evnt
    */
   function getEditEvnt(){
      if (!$toEdit = $this->_getSessionEvnt()){
         return CopixActionGroup::process ('genericTools|Messages::getError',
               array ('message'=>CopixI18N::get ('schedule.error.cannotGetEvntBack'),
                  'back'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'schedule'))));
      }
      $workflow         = & CopixClassesFactory::getInstanceOf ('copixheadings|workflow');
      $servicesHeadings = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
      $capability       = CopixUserProfile::valueOf ($servicesHeadings->getPath ($toEdit->id_head), 'schedule');

      if ($capability < PROFILE_CCV_WRITE ||
         ($capability < PROFILE_CCV_VALID && $toEdit->status_evnt == $workflow->getPropose()) ||
         ($capability < PROFILE_CCV_PUBLISH && $toEdit->status_evnt == $workflow->getValid()) ||
         ($capability < PROFILE_CCV_PUBLISH && $toEdit->status_evnt == $workflow->getPublish()) ||
          $toEdit->status_evnt == $workflow->getTrash()) {
         return CopixActionGroup::process ('genericTools|Messages::getError',
         array ('message'=>CopixI18N::get ('schedule.error.notAnAuthorizedHead'),
         'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'schedule','level'=>$toEdit->id_head))));
      }
      
      $tpl = & new CopixTpl();
      $tpl->assign('TITLE_PAGE',CopixI18N::get ('schedule.title.editevent'));
      $tpl->assign('MAIN',CopixZone::process('AdminEditEvent',array('toEdit'=>$toEdit,'e'=>CopixRequest::get ('e', null, true))));
      return new CopixActionReturn (CopixactionReturn::DISPLAY, $tpl);
   }
   
   /**
    * doValidEvnt
    * Store an edited evnt (created or updated)
    */
   function doValidEvnt () {
   	if (!$toValid = $this->_getSessionEvnt()){
         return CopixActionGroup::process ('genericTools|Messages::getError',
               array ('message'=>CopixI18N::get ('schedule.error.cannotGetEvntBack'),
                  'back'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'schedule'))));
      }
      
      $daoEvnt = & CopixDAOFactory::create ('ScheduleEvents');
      
      //check if the user has the rights to write pages into the given heading.
      $workflow         = & CopixClassesFactory::getInstanceOf ('copixheadings|workflow');
      $servicesHeadings = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
      $capability       = CopixUserProfile::valueOf ($servicesHeadings->getPath ($toValid->id_head), 'schedule');
      
      if ($capability < PROFILE_CCV_WRITE ||
         ($capability < PROFILE_CCV_VALID && $toValid->status_evnt == $workflow->getPropose()) ||
         ($capability < PROFILE_CCV_PUBLISH && $toValid->status_evnt == $workflow->getValid())) {
         return CopixActionGroup::process ('genericTools|Messages::getError',
         array ('message'=>CopixI18N::get ('schedule.error.notAnAuthorizedHead'),
         'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'schedule','level'=>$toValid->id_head))));
      }
   
      $login      = CopixUserProfile::getLogin ();   
        
      // Handle form values
      $this->_validFromForm($toValid);
      
      //creating or updating.
      if ($toValid->id_evnt !== null){ //update
         if (isset($this->vars['doBest']) && $this->vars['doBest']==1) {
            if (CopixConfig::get ('schedule|easyWorkflow') == 1){
               $toValid->status_evnt       = $workflow->getBest($toValid->id_head,'schedule');
            }else{
               $toValid->status_evnt       = $workflow->getNext($toValid->id_head,'schedule',$toValid->status_evnt);
            }
            $toValid->statusdate_evnt   = date('Ymd');
            $toValid->statuscomment_evnt= '';
         }else{
            //if status is refuse then we change to draft
            if ($toValid->status_evnt == $workflow->getRefuse ()) {
                $toValid->status_evnt       = $workflow->getDraft ();
                $toValid->statusdate_evnt   = date('Ymd');
                $toValid->statuscomment_evnt= '';
            }
         }
         // Check form values
         if ($daoEvnt->check($toValid) !== true) {
            $this->_setSessionEvnt($toValid);
            return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('schedule|admin|editEvnt', array('e'=>'1')));
         }
         $daoEvnt->update ($toValid);
      }else{ //create
         $toValid->author_evnt       = $login;
         $toValid->statusauthor_evnt = $login;
         $toValid->statusdate_evnt   = date('Ymd');
         if (isset($this->vars['doBest']) && $this->vars['doBest']==1) {
            if (CopixConfig::get ('schedule|easyWorkflow') == 1){
               $toValid->status_evnt       = $workflow->getBest($toValid->id_head,'schedule');
            }else{
               $toValid->status_evnt       = $workflow->getNext($toValid->id_head,'schedule',$toValid->status_evnt);
            }
         }else{
            $toValid->status_evnt       = $workflow->getDraft();
         }
         // Check form values
         if ($daoEvnt->check($toValid) !== true) {
            $this->_setSessionEvnt($toValid);
            return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('schedule|admin|editEvnt', array('e'=>'1')));
         }
         $daoEvnt->insert ($toValid);
      }
      $this->_setSessionEvnt (null);
      
      if (isset($toValid->__Copix_Internal_UrlBack) && strlen($toValid->__Copix_Internal_UrlBack) > 0) {
         return new CopixActionReturn (CopixactionReturn::REDIRECT,$toValid->__Copix_Internal_UrlBack);
      }else{
         return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('browse'=>'schedule', 'level'=>$toValid->id_head)));
      }
   }
   
   /**
    * doCancelEditEvnt
    */
   function doCancelEditEvnt() {
      if (!$toEdit = $this->_getSessionEvnt()){
         return CopixActionGroup::process ('genericTools|Messages::getError',
               array ('message'=>CopixI18N::get ('schedule.error.cannotGetEvntBack'),
                  'back'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'schedule'))));
      }
      $id_head=$toEdit->id_head;
   	$this->_setSessionEvnt (null);
      return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('browse'=>'schedule', 'level'=>$id_head)));
   }
   
   /**
    * doDeleteEvnt
    * @param id_evnt
    */
   function doDeleteEvnt() {
      if (!isset ($this->vars['id_evnt'])){
         return CopixActionGroup::process ('genericTools|Messages::getError',
               array ('message'=>CopixI18N::get ('schedule.error.notAnAuthorizedHead'),
               'back'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'schedule'))));
      }
      
      $toDelete = $this->_loadEvnt($this->vars['id_evnt']);
      
      //check if the user has the rights
      $servicesHeadings = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
      $capability       = CopixUserProfile::valueOf ($servicesHeadings->getPath ($toDelete->id_head), 'pictures');
      $workflow         = & CopixClassesFactory::getInstanceOf ('copixheadings|workflow');
      if ($capability < PROFILE_CCV_WRITE ||
         ($capability < PROFILE_CCV_PUBLISH && $toDelete->status_evnt == $workflow->getPublish())) {
         return CopixActionGroup::process ('genericTools|Messages::getError',
         array ('message'=>CopixI18N::get ('schedule.error.notAnAuthorizedHead'),
         'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'schedule','level'=>$toDelete->id_head))));
      }
      
      //Confirmation screen ?
      if ($toDelete->status_evnt != $workflow->getTrash() && !isset ($this->vars['confirm'])){
      	return CopixActionGroup::process ('genericTools|Messages::getConfirm',
      		array ('title'=>CopixI18N::get ('schedule.title.confirmdelevent'),
      		'message'=>CopixI18N::get ('schedule.message.confirmdelevent'),
      		'confirm'=>CopixUrl::get('schedule|admin|delete', array('id_evnt'=>$toDelete->id_evnt, 'confirm'=>'1')),
      		'cancel'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'schedule', 'level'=>$toDelete->id_head))));
      }
      
      //Delete Evnt
      $dao = & CopixDAOFactory::create ('ScheduleEvents');
      $dao->delete($toDelete->id_evnt);
      return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('browse'=>'schedule', 'level'=>$toDelete->id_head)));
   }
   
   /**
   * Set online a picture
   */
   function doStatusPublish () {
      $workflow = & CopixClassesFactory::getInstanceOf ('copixheadings|workflow');

      if (!isset ($this->vars['id_evnt'])){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('schedule.error.missingParameters'),
            'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'schedule'))));
      }
      
      $toEdit = $this->_loadEvnt($this->vars['id_evnt']);

      //check if the user has the rights
      $servicesHeadings = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
      if (CopixUserProfile::valueOf ($servicesHeadings->getPath ($toEdit->id_head), 'schedule') < PROFILE_CCV_PUBLISH) {
         return CopixActionGroup::process ('genericTools|Messages::getError',
         array ('message'=>CopixI18N::get ('schedule.error.notAnAuthorizedHead'),
         'back'=>CopixUrl::get ('copixheadings|admin|', array('browse'=>'schedule','level'=>$toEdit->id_head))));
      }
      
      $plugAuth  = & CopixController::instance ()->getPlugin ('auth|auth');
      $user      = & $plugAuth->getUser();
      $toEdit->status_evnt       = $workflow->getPublish ();
      $toEdit->statusauthor_evnt = $user->login;
      $toEdit->statusdate_evnt   = date('Ymd');
      $toEdit->statuscomment_evnt= isset($this->vars['statuscomment_evnt_'.$toEdit->id_evnt]) ? $this->vars['statuscomment_evnt_'.$toEdit->id_evnt] : null;
      $dao = & CopixDAOFactory::create ('ScheduleEvents');
      $dao->update($toEdit);

      if (isset($this->vars['back']) && strlen($this->vars['back']) > 0) {
         return new CopixActionReturn (CopixactionReturn::REDIRECT, $this->vars['back']);
      }else{
         return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('browse'=>'schedule', 'level'=>$toEdit->id_head)));
      }
   }

    /**
    * Set picture status to valid
    */
    function doStatusValid (){
        $workflow = & CopixClassesFactory::getInstanceOf ('copixheadings|workflow');

        if (!isset ($this->vars['id_evnt'])){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('schedule.error.missingParameters'),
            'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'schedule'))));
        }

        $toEdit = $this->_loadEvnt($this->vars['id_evnt']);
        
        $plugAuth  = & CopixController::instance ()->getPlugin ('auth|auth');
        $user      = & $plugAuth->getUser();
        //check if the user has the rights
        $servicesHeadings = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
        if (CopixUserProfile::valueOf ($servicesHeadings->getPath ($toEdit->id_head), 'schedule') < PROFILE_CCV_VALID &&
            $toEdit->status_evnt == $workflow->getPublish ()) {
           return CopixActionGroup::process ('genericTools|Messages::getError',
           array ('message'=>CopixI18N::get ('schedule.error.notAnAuthorizedHead'),
           'back'=>CopixUrl::get ('copixheadings|admin|', array('browse'=>'schedule','level'=>$toEdit->id_head))));
        }
        $toEdit->status_evnt       = $workflow->getValid ();
        $toEdit->statusauthor_evnt = $user->login;
        $toEdit->statusdate_evnt   = date('Ymd');
        $toEdit->statuscomment_evnt= isset($this->vars['statuscomment_evnt_'.$toEdit->id_evnt]) ? $this->vars['statuscomment_evnt_'.$toEdit->id_evnt] : null;
        $dao = & CopixDAOFactory::create ('ScheduleEvents');
        $dao->update($toEdit);
        
        //launch event
        CopixEventNotifier::notify (new CopixEvent ('EventValid',array ('event'=>$toEdit)));

        if (isset($this->vars['back']) && strlen($this->vars['back']) > 0) {
           return new CopixActionReturn (CopixactionReturn::REDIRECT, $this->vars['back']);
        }else{
           return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('browse'=>'schedule', 'level'=>$toEdit->id_head)));
        }
    }

     /**
    * Set picture status to draft from trash
    */
    function doStatusDraft (){
        $workflow = & CopixClassesFactory::getInstanceOf ('copixheadings|workflow');

        if (!isset ($this->vars['id_evnt'])){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('schedule.error.missingParameters'),
            'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'schedule'))));
        }

        $toEdit = $this->_loadEvnt($this->vars['id_evnt']);

        $plugAuth  = & CopixController::instance ()->getPlugin ('auth|auth');
        $user      = & $plugAuth->getUser();
        //check if the user has the rights
        $servicesHeadings = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
        if (CopixUserProfile::valueOf ($servicesHeadings->getPath ($toEdit->id_head), 'schedule') < PROFILE_CCV_VALID &&
            $toEdit->status_evnt != $workflow->getTrash () && $toEdit->statusauthor_evnt != $user->login) {
           return CopixActionGroup::process ('genericTools|Messages::getError',
           array ('message'=>CopixI18N::get ('schedule.error.notAnAuthorizedHead'),
           'back'=>CopixUrl::get ('copixheadings|admin|', array('browse'=>'schedule','level'=>$toEdit->id_head))));
        }
        $toEdit->status_evnt       = $workflow->getDraft ();
        $toEdit->statusauthor_evnt = $user->login;
        $toEdit->statusdate_evnt   = date('Ymd');
        $toEdit->statuscomment_evnt= isset($this->vars['statuscomment_evnt_'.$toEdit->id_evnt]) ? $this->vars['statuscomment_evnt_'.$toEdit->id_evnt] : null;
        $dao = & CopixDAOFactory::create ('ScheduleEvents');
        $dao->update($toEdit);

        if (isset($this->vars['back']) && strlen($this->vars['back']) > 0) {
           return new CopixActionReturn (CopixactionReturn::REDIRECT, $this->vars['back']);
        }else{
           return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('browse'=>'schedule', 'level'=>$toEdit->id_head)));
        }
    }

     /**
    * Set picture status to refuse
    */
    function doStatusRefuse (){
        $workflow = & CopixClassesFactory::getInstanceOf ('copixheadings|workflow');

        if (!isset ($this->vars['id_evnt'])){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('schedule.error.missingParameters'),
            'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'schedule'))));
        }

        $toEdit = $this->_loadEvnt($this->vars['id_evnt']);

        $plugAuth  = & CopixController::instance ()->getPlugin ('auth|auth');
        $user      = & $plugAuth->getUser();

        //check if the user has the rights
        $servicesHeadings = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
        $capability       = CopixUserProfile::valueOf ($servicesHeadings->getPath ($toEdit->id_head), 'schedule');
        if (($capability < PROFILE_CCV_VALID && $toEdit->status_evnt == $workflow->getPropose()) ||
           ($capability < PROFILE_CCV_PUBLISH && $toEdit->status_evnt == $workflow->getValid()) ||
           $toEdit->status_evnt == $workflow->getPublish()) {
           return CopixActionGroup::process ('genericTools|Messages::getError',
           array ('message'=>CopixI18N::get ('schedule.error.notAnAuthorizedHead'),
           'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'schedule','level'=>$toEdit->id_head))));
        }

        $toEdit->status_evnt       = $workflow->getRefuse ();
        $toEdit->statusauthor_evnt = $user->login;
        $toEdit->statusdate_evnt   = date('Ymd');
        $toEdit->statuscomment_evnt= isset($this->vars['statuscomment_evnt_'.$toEdit->id_evnt]) ? $this->vars['statuscomment_evnt_'.$toEdit->id_evnt] : null;
        $dao = & CopixDAOFactory::create ('ScheduleEvents');
        $dao->update($toEdit);
        
        //launch event
        CopixEventNotifier::notify (new CopixEvent ('EventRefuse',array ('event'=>$toEdit)));

        if (isset($this->vars['back']) && strlen($this->vars['back']) > 0) {
           return new CopixActionReturn (CopixactionReturn::REDIRECT, $this->vars['back']);
        }else{
           return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('browse'=>'schedule', 'level'=>$toEdit->id_head)));
        }
    }

    /**
    * Set picture status to propose
    */
    function doStatusPropose (){
        $workflow = & CopixClassesFactory::getInstanceOf ('copixheadings|workflow');

        if (!isset ($this->vars['id_evnt'])){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('schedule.error.missingParameters'),
            'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'schedule'))));
        }

        $toEdit = $this->_loadEvnt($this->vars['id_evnt']);

        $plugAuth  = & CopixController::instance ()->getPlugin ('auth|auth');
        $user      = & $plugAuth->getUser();

        //check if the user has the rights
        $servicesHeadings = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
        $capability       = CopixUserProfile::valueOf ($servicesHeadings->getPath ($toEdit->id_head), 'schedule');

        if ($capability < PROFILE_CCV_WRITE &&
            $toEdit->status_evnt == $workflow->getPublish ()) {
           return CopixActionGroup::process ('genericTools|Messages::getError',
           array ('message'=>CopixI18N::get ('schedule.error.notAnAuthorizedHead'),
           'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'schedule','level'=>$toEdit->id_head))));
        }

        $toEdit->status_evnt       = $workflow->getPropose ();
        //récuperation de l'etat proposer/créer dans le worflow
        if (CopixConfig::get ('schedule|easyWorkflow') == 1){
            $toEdit->status_evnt = $workflow->getBest ($toEdit->id_head, 'schedule');
        }
        $toEdit->statusauthor_evnt = $user->login;
        $toEdit->statusdate_evnt   = date('Ymd');
        $toEdit->statuscomment_evnt= isset($this->vars['statuscomment_evnt_'.$toEdit->id_evnt]) ? $this->vars['statuscomment_evnt_'.$toEdit->id_evnt] : null;
        $dao = & CopixDAOFactory::create ('ScheduleEvents');
        $dao->update($toEdit);
        
        //launch event
        if ($toEdit->status_doc == $workflow->getPublish ()) {
        }elseif($toEdit->status_doc == $workflow->getValid ()) {
            CopixEventNotifier::notify (new CopixEvent ('EventValid',array ('event'=>$toEdit)));
        }else{
            CopixEventNotifier::notify (new CopixEvent ('EventPropose',array ('event'=>$toEdit)));
        }

        if (isset($this->vars['back']) && strlen($this->vars['back']) > 0) {
           return new CopixActionReturn (CopixactionReturn::REDIRECT, $this->vars['back']);
        }else{
           return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('browse'=>'schedule', 'level'=>$toEdit->id_head)));
        }
    }

     /**
    * Set picture status to trash
    */
    function doStatusTrash (){
        $workflow = & CopixClassesFactory::getInstanceOf ('copixheadings|workflow');
        if (!isset ($this->vars['id_evnt'])){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('schedule.error.missingParameters'),
            'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'schedule'))));
        }

        $toEdit = $this->_loadEvnt($this->vars['id_evnt']);

        $plugAuth  = & CopixController::instance ()->getPlugin ('auth|auth');
        $user      = & $plugAuth->getUser();

        //check if the user has the rights
        $servicesHeadings = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
        $capability       = CopixUserProfile::valueOf ($servicesHeadings->getPath ($toEdit->id_head), 'schedule');

        if ($capability < PROFILE_CCV_WRITE ||
           ($capability < PROFILE_CCV_VALID && $toEdit->status_evnt == $workflow->getPropose()) ||
           ($capability < PROFILE_CCV_PUBLISH && $toEdit->status_evnt == $workflow->getValid()) ||
           $toEdit->status_evnt == $workflow->getPublish()) {
           return CopixActionGroup::process ('genericTools|Messages::getError',
           array ('message'=>CopixI18N::get ('schedule.error.notAnAuthorizedHead'),
           'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'schedule','level'=>$toEdit->id_head))));
        }

        $toEdit->status_evnt       = $workflow->getTrash ();
        $toEdit->statusauthor_evnt = $user->login;
        $toEdit->statusdate_evnt   = date('Ymd');
        $toEdit->statuscomment_evnt= isset($this->vars['statuscomment_evnt_'.$toEdit->id_evnt]) ? $this->vars['statuscomment_evnt_'.$toEdit->id_evnt] : null;
        $dao = & CopixDAOFactory::create ('ScheduleEvents');
        $dao->update($toEdit);

        if (isset($this->vars['back']) && strlen($this->vars['back']) > 0) {
           return new CopixActionReturn (CopixactionReturn::REDIRECT, $this->vars['back']);
        }else{
           return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('browse'=>'schedule', 'level'=>$toEdit->id_head)));
        }
    }
   
   
   /**
    * _validFromForm
    * @param Evnt $toValid The evnt that get the form
    */
   function _validFromForm (& $toEdit) {
   	$toEdit->title_evnt   = CopixRequest::get ('title_evnt');
      $toEdit->datedisplayfrom_evnt = CopixI18N::dateToBD (CopixRequest::get ('datedisplayfrom_evnt'));
      $toEdit->datedisplayto_evnt   = CopixI18N::dateToBD (CopixRequest::get ('datedisplayto_evnt'));
      $toEdit->datefrom_evnt        = CopixI18N::dateToBD (CopixRequest::get ('datefrom_evnt'));
      $toEdit->dateto_evnt          = CopixI18N::dateToBD (CopixRequest::get ('dateto_evnt'));
      $toEdit->content_evnt         = CopixRequest::get ('content_evnt', null);
      $toEdit->preview_evnt         = CopixRequest::get ('preview_evnt', null);
      $toEdit->subscribeenabled_evnt = CopixRequest::get ('subscribeenabled_evnt', 0);
   }
   
   /**
    * loadEvnt
    * @param id_evnt
    * @return Evnt
    */
   function _loadEvnt ($id_evnt) {
      $dao = & CopixDAOFactory::create ('ScheduleEvents');
      if (!$toLoad = $dao->get ($id_evnt)){
         return CopixActionGroup::process ('genericTools|Messages::getError',
               array ('message'=>CopixI18N::get ('schedule.error.cannotGetEvntBack'),
                     'back'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'schedule'))));
      }
      return $toLoad;
   }
   
   /**
    * _getSessionEvnt
    * get an event in session
    */
   function _getSessionEvnt(){
      CopixDAOFactory::fileInclude('ScheduleEvents');
      return isset ($_SESSION['MODULE_SCHEDULE_EVENT']) ?  unserialize($_SESSION['MODULE_SCHEDULE_EVENT']) : null;
   }
   /**
    * set an event in session
    */
   function _setSessionEvnt ($elem){
      $_SESSION ['MODULE_SCHEDULE_EVENT'] = serialize ($elem);
   }
}
?>
