<?php
/**
* @package   cms
* @author   Croes Gérald
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @ignore
 */
CopixClassesFactory::fileInclude ('cms|ServicesCMSPage');

/**
 * ActionGroupCMSPageWorkflow
 * @package cms
 */
class ActionGroupCMSPageWorkflow extends CopixActionGroup {
    /**
   * Sends the given page to the trash.
   * we can only sends item in the trash if it's our document and we still have
   *  the write right on the given heading, or if we're a moderator of the heading
   * @param $this->vars['id'] the id of the page we wants to sends in the trash.

   */
    function doTrash () {
    	CopixClassesFactory::fileInclude ('cms|CMSAuth');
        $draft           = ServicesCMSPage::getDraft ($this->vars['id']);

        //cannot find the given draft
        if ($draft === null){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('admin.error.cannotFindDraft'),
            'back'=>CopixUrl::get ('copixheadings|admin|')));
        }

        //check if we *can* do it
        if (CopixUserProfile::getLogin () == $draft->author_cmsp){
            //our document, we just have to still have write enabled here.
            if (! CMSAuth::canWrite($draft->id_head)){
                return CopixActionGroup::process ('genericTools|Messages::getError',
                array ('message'=>CopixI18N::get ('admin.error.cannotSendToTrash'),
                'back'=>CopixUrl::get ('copixheadings|admin|', array ('id_head'=>$draft->id_head))));
            }
        }else{
            //not our document, we have to be a moderator.
            if (! CMSAuth::canModerate ($draft->id_head)){
                return CopixActionGroup::process ('genericTools|Messages::getError',
                array ('message'=>CopixI18N::get ('admin.error.cannotSendToTrash'),
                'back'=>CopixUrl::get ('copixheadings|admin|', array ('id_head'=>$draft->id_head))));
            }
        }

        $statuscomment = isset($this->vars['statuscomment_cmsp_'.$this->vars['id']]) ? $this->vars['statuscomment_cmsp_'.$this->vars['id']] : null;
        ServicesCMSPage::setTrash ($this->vars['id'], $statuscomment);
        if (isset($this->vars['back']) && strlen($this->vars['back']) > 0) {//Redirect with a special url
           return new CopixActionReturn (CopixActionReturn::REDIRECT, $this->vars['back']);
        }else{
           return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array ('browse'=>'cms', 'id_head'=>$draft->id_head)));
        }
    }

    /**
    * Definitely deletes the document.
    * We can only deletes documents that are in the trash.
    * We can only deletes document we wrote, in a heading we still have write rights.
    * If we're not the author of the document, we have to be a moderator.
    * @param int $this->vars['id']
    */
    function doDelete () {
        $draft           = ServicesCMSPage::getDraft ($this->vars['id']);
        $servicesHeading = CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
        $workflow         = CopixClassesFactory::getInstanceOf ('copixheadings|workflow');

        //cannot find the given draft
        if ($draft === null) {
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('admin.error.cannotFindDraft'),
            'back'=>CopixUrl::get ('copixheadings|admin')));
        }

        if ($draft->status_cmsp != $workflow->getTrash ()){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('admin.error.cannotDeleteNonTrash'),
            'back'=>CopixUrl::get ('copixheadings|admin|', array ('id_head'=>$draft->id_head))));
        }

        //check if we *can* do it
        if (CopixUserProfile::getLogin () == $draft->author_cmsp){
            //our document, we just have to still have write enabled here.
            if (CopixUserProfile::valueOf ('cms', $servicesHeading->getPath ($draft->id_head)) < PROFILE_CCV_WRITE){
                return CopixActionGroup::process ('genericTools|Messages::getError',
                array ('message'=>CopixI18N::get ('admin.error.cannotDelete'),
                'back'=>CopixUrl::get ('copixheadings|admin|', array ('id_head'=>$draft->id_head))));
            }
        }else{
            //not our document, we have to be a moderator.
            if (CopixUserProfile::valueOf ('cms', $servicesHeading->getPath ($draft->id_head)) < PROFILE_CCV_MODERATE){
                return CopixActionGroup::process ('genericTools|Messages::getError',
                array ('message'=>CopixI18N::get ('admin.error.cannotDelete'),
                'back'=>CopixUrl::get ('copixheadings|admin|', array ('id_head'=>$draft->id_head))));
            }
        }

        $statuscomment = isset($this->vars['statuscomment_cmsp_'.$this->vars['id']]) ? $this->vars['statuscomment_cmsp_'.$this->vars['id']] : null;

        ServicesCMSPage::setDelete ($this->vars['id'],$statuscomment);
        if (isset($this->vars['back']) && strlen($this->vars['back']) > 0) {//Redirect with a special url
           return new CopixActionReturn (CopixActionReturn::REDIRECT, $this->vars['back']);
        }else{
           return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array ('browse'=>'cms', 'id_head'=>$draft->id_head)));
        }
    }

    /**
   * We can only restore documents that are in the trash.
   * We can only restores documents we're the author of, in heading we still have write permissions on
   * If we're not the author of the document, we have to be a moderator of the heading.
   * @param int $this->vars['id'] the page id

   */
    function doRestore () {
        $draft           = ServicesCMSPage::getDraft ($this->vars['id']);
        $servicesHeading = CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
        $workflow         = CopixClassesFactory::getInstanceOf ('copixheadings|workflow');

        //cannot find the given draft
        if ($draft === null) {
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('admin.error.cannotFindDraft'),
            'back'=>CopixUrl::get ('copixheadings|admin|')));
        }
        if (($draft->status_cmsp != $workflow->getTrash ()) && ($draft->status_cmsp != $workflow->getRefuse())){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('admin.error.cannotRestoreNonTrash'),
            'back'=>CopixUrl::get ('copixheadings|admin|', array ('id_head'=>$draft->id_head))));
        }

        //check if we *can* do it
        if (CopixUserProfile::getLogin () == $draft->author_cmsp) {
            //our document, we just have to still have write enabled here.
            if (CopixUserProfile::valueOf ('cms', $servicesHeading->getPath ($draft->id_head)) < PROFILE_CCV_WRITE) {
                return CopixActionGroup::process ('genericTools|Messages::getError',
                array ('message'=>CopixI18N::get ('admin.error.cannotRestore'),
                'back'=>CopixUrl::get ('copixheadings|admin|', array ('id_head'=>$draft->id_head))));
            }
        }else{
            //not our document, we have to be a moderator.
            if (CopixUserProfile::valueOf ('cms', $servicesHeading->getPath ($draft->id_head)) < PROFILE_CCV_MODERATE){
                return CopixActionGroup::process ('genericTools|Messages::getError',
                array ('message'=>CopixI18N::get ('admin.error.cannotRestore'),
                'back'=>CopixUrl::get ('copixheadings|admin|', array ('id_head'=>$draft->id_head))));
            }
        }

        $statuscomment = isset($this->vars['statuscomment_cmsp_'.$this->vars['id']]) ? $this->vars['statuscomment_cmsp_'.$this->vars['id']] : null;

        ServicesCMSPage::setCreate ($this->vars['id'],$statuscomment);
        return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array ('browse'=>'cms', 'id_head'=>$draft->id_head)));
    }

    /**
   * We can propose a document only if we have write permissions on the given heading, and if we are the author of it.
   * We can propose documents only if they are in the created status (not in the trash)

   */
    function doPropose () {
        $draft           = ServicesCMSPage::getDraft ($this->vars['id']);
        $servicesHeading = CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
        $workflow         = CopixClassesFactory::getInstanceOf ('copixheadings|workflow');

        //cannot find the given draft
        if ($draft === null) {
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('admin.error.cannotFindDraft'),
            'back'=>CopixUrl::get ('copixheadings|admin|')));
        }
        //if ($draft->status_cmsp != $workflow->getCreate ()){
        if ($draft->status_cmsp != $workflow->getDraft ()){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('admin.error.cannotProposeNonDraft'),
            'back'=>CopixUrl::get ('copixheadings|admin|', array ('id_head'=>$draft->id_head))));
        }

        //check if we *can* do it
        if (CopixUserProfile::getLogin () == $draft->author_cmsp) {
            //our document, we just have to still have write enabled here.
            if (CopixUserProfile::valueOf ('cms', $servicesHeading->getPath ($draft->id_head)) < PROFILE_CCV_WRITE) {
                return CopixActionGroup::process ('genericTools|Messages::getError',
                array ('message'=>CopixI18N::get ('admin.error.cannotPropose'),
                'back'=>CopixUrl::get ('copixheadings|admin|', array ('id_head'=>$draft->id_head))));
            }
        } else {
            //not our document, we have to be a moderator.
            if (CopixUserProfile::valueOf ('cms', $servicesHeading->getPath ($draft->id_head)) < PROFILE_CCV_MODERATE){
                return CopixActionGroup::process ('genericTools|Messages::getError',
                array ('message'=>CopixI18N::get ('admin.error.cannotPropose'),
                'back'=>CopixUrl::get ('copixheadings|admin|', array ('id_head'=>$draft->id_head))));
            }
        }

        if (CopixConfig::get ('cms|easyWorkflow') == 1){
            $this->doBest ();
            return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array ('browse'=>'cms', 'id_head'=>$draft->id_head)));
        }

        $statuscomment = isset($this->vars['statuscomment_cmsp_'.$this->vars['id']]) ? $this->vars['statuscomment_cmsp_'.$this->vars['id']] : null;

        ServicesCMSPage::setPropose ($this->vars['id'],$statuscomment);
        return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array ('browse'=>'cms', 'id_head'=>$draft->id_head)));
    }

    /**
   * we can only validate documents that are proposed.
   * we can only validate if we have valid rights on the headings.
   */
    function doValid (){
        $draft           = ServicesCMSPage::getDraft ($this->vars['id']);
        $servicesHeading = CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
        $workflow         = CopixClassesFactory::getInstanceOf ('copixheadings|workflow');

        //cannot find the given draft
        if ($draft === null) {
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('admin.error.cannotFindDraft'),
            'back'=>CopixUrl::get ('copixheadings|admin|')));
        }
        if ($draft->status_cmsp != $workflow->getPropose ()){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('admin.error.cannotValidNonPropose'),
            'back'=>CopixUrl::get ('copixheadings|admin|', array ('id_head'=>$draft->id_head))));
        }

        //check if we *can* do it
        if (CopixUserProfile::valueOf ('cms', $servicesHeading->getPath ($draft->id_head)) < PROFILE_CCV_VALID) {
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('admin.error.cannotValid'),
            'back'=>CopixUrl::get ('copixheadings|admin|', array ('id_head'=>$draft->id_head))));
        }

        if (CopixConfig::get ('cms|easyWorkflow') == 1){
            return $this->doBest ();
        }

        $statuscomment = isset($this->vars['statuscomment_cmsp_'.$this->vars['id']]) ? $this->vars['statuscomment_cmsp_'.$this->vars['id']] : null;

        ServicesCMSPage::setValid ($this->vars['id'],$statuscomment);
        if (isset($this->vars['back']) && strlen($this->vars['back']) > 0) {//Redirect with a special url
           return new CopixActionReturn (CopixActionReturn::REDIRECT, $this->vars['back']);
        }else{
           return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array ('browse'=>'cms', 'id_head'=>$draft->id_head)));
        }
    }

    /**
   * Publish the given document.
   */
    function doPublish (){
        $draft           = ServicesCMSPage::getDraft ($this->vars['id']);
        $servicesHeading = CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
        $workflow         = CopixClassesFactory::getInstanceOf ('copixheadings|workflow');

        //cannot find the given draft
        if ($draft === null) {
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('admin.error.cannotFindDraft'),
            'back'=>CopixUrl::get ('copixheadings|admin|')));
        }

        if ($draft->status_cmsp != $workflow->getValid ()){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('admin.error.cannotPublishNonValid'),
            'back'=>CopixUrl::get ('copixheadings|admin|', array ('id_head'=>$draft->id_head))));
        }

        //check if we *can* do it
        if (CopixUserProfile::valueOf ('cms', $servicesHeading->getPath ($draft->id_head)) < PROFILE_CCV_PUBLISH) {
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('admin.error.cannotPublish'),
            'back'=>CopixUrl::get ('copixheadings|admin|', array ('id_head'=>$draft->id_head))));
        }

        $statuscomment = isset($this->vars['statuscomment_cmsp_'.$this->vars['id']]) ? $this->vars['statuscomment_cmsp_'.$this->vars['id']] : null;

        ServicesCMSPage::setPublish ($this->vars['id'], $statuscomment);
        if (isset($this->vars['back']) && strlen($this->vars['back']) > 0) {//Redirect with a special url
           return new CopixActionReturn (CopixActionReturn::REDIRECT, $this->vars['back']);
        }else{
           return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array ('browse'=>'cms', 'id_head'=>$draft->id_head)));
        }
    }

    /**
   * Refuse a given document.
   */
    function doRefuse (){
        $draft           = ServicesCMSPage::getDraft ($this->vars['id']);
        $servicesHeading = CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
        $workflow         = CopixClassesFactory::getInstanceOf ('copixheadings|workflow');

        //cannot find the given draft
        if ($draft === null) {
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('admin.error.cannotFindDraft'),
            'back'=>CopixUrl::get ('copixheadings|admin|')));
        }
        if ($draft->status_cmsp != $workflow->getPropose () && $draft->status_cmsp != $workflow->getValid ()){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('admin.error.cannotValidNonPropose'),
            'back'=>CopixUrl::get ('copixheadings|admin|', array ('id_head'=>$draft->id_head))));
        }

        //check if we *can* do it
        if (CopixUserProfile::valueOf ('cms', $servicesHeading->getPath ($draft->id_head)) < PROFILE_CCV_VALID) {
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('admin.error.cannotValid'),
            'back'=>CopixUrl::get ('copixheadings|admin|', array ('id_head'=>$draft->id_head))));
        }

        $statuscomment = isset($this->vars['statuscomment_cmsp_'.$this->vars['id']]) ? $this->vars['statuscomment_cmsp_'.$this->vars['id']] : null;

        ServicesCMSPage::setRefuse ($this->vars['id'],$statuscomment);
        if (isset($this->vars['back']) && strlen($this->vars['back']) > 0) {//Redirect with a special url
           return new CopixActionReturn (CopixActionReturn::REDIRECT, $this->vars['back']);
        }else{
           return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array ('browse'=>'cms', 'id_head'=>$draft->id_head)));
        }
    }

    /**
   * gets a refused document back in the drafts.
   * we can only restore drafts we're the author of, or if we are a moderator of the given heading.
   */
    function doDraft () {
        $draft           = ServicesCMSPage::getDraft ($this->vars['id']);
        $servicesHeading = CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
        $workflow         = CopixClassesFactory::getInstanceOf ('copixheadings|workflow');

        //cannot find the given draft
        if ($draft === null) {
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('admin.error.cannotFindDraft'),
            'back'=>CopixUrl::get ('copixheadings|admin|')));
        }
        if ($draft->status_cmsp != $workflow->getRefuse ()){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('admin.error.cannotValidNonPropose'),
            'back'=>CopixUrl::get ('copixheadings|admin|', array ('id_head'=>$draft->id_head))));
        }

        //check if we *can* do it
        if (CopixUserProfile::getLogin() == $draft->author_cmsp){
            //we're the author, we can do it if we still have the rights to write in the headings
            if (CopixUserProfile::valueOf ('cms', $servicesHeading->getPath ($draft->id_head)) < PROFILE_CCV_WRITE) {
                return CopixActionGroup::process ('genericTools|Messages::getError',
                array ('message'=>CopixI18N::get ('admin.error.cannotDraftBack'),
                'back'=>CopixUrl::get ('copixheadings|admin|', array ('id_head'=>$draft->id_head))));
            }
        }else{
            //we're not the author, we then have to be a moderator.
            if (CopixUserProfile::valueOf ('cms', $servicesHeading->getPath ($draft->id_head)) < PROFILE_CCV_MODERATE){
                return CopixActionGroup::process ('genericTools|Messages::getError',
                array ('message'=>CopixI18N::get ('admin.error.cannotDraftBack'),
                'back'=>CopixUrl::get ('copixheadings|admin|', array ('id_head'=>$draft->id_head))));
            }
        }

        $statuscomment = isset($this->vars['statuscomment_cmsp_'.$this->vars['id']]) ? $this->vars['statuscomment_cmsp_'.$this->vars['id']] : null;

        ServicesCMSPage::setRefuse ($this->vars['id'],$statuscomment);
        if (isset($this->vars['back']) && strlen($this->vars['back']) > 0) {//Redirect with a special url
           return new CopixActionReturn (CopixActionReturn::REDIRECT, $this->vars['back']);
        }else{
           return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array ('browse'=>'cms', 'id_head'=>$draft->id_head)));
        }
    }

   /**
    * Best status, depends on profile.
    * we'll check if we can at least write here.
    */
   function doBest ($params=array()){
     $servicesHeading = CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
     
     $id = isset($params['id']) ? $params['id'] : $this->vars['id'];
   
     $draft = ServicesCMSPage::getDraft ($id);
     if ($draft === null){
         return CopixActionGroup::process ('genericTools|Messages::getError',
         array ('message'=>CopixI18N::get ('admin.error.cannotFindDraft'),
         'back'=>CopixUrl::get ('copixheadings|admin|')));
     }
   
     $value = CopixUserProfile::valueOf ('cms', $servicesHeading->getPath ($draft->id_head));
   
     if ($value < PROFILE_CCV_WRITE){
         return CopixActionGroup::process ('genericTools|Messages::getError',
         array ('message'=>CopixI18N::get ('admin.error.notAnAuthorizedHead'),
         'back'=>CopixUrl::get ('copixheadings|admin|')));
     }
   
     $statuscomment = isset($this->vars['statuscomment_cmsp_'.$id]) ? $this->vars['statuscomment_cmsp_'.$id] : null;
   
     $method = 'setPropose';
     if ($value >= PROFILE_CCV_VALID){
         $method = 'setValid';
     }
     if ($value >= PROFILE_CCV_PUBLISH){
         $method = 'setPublish';
     }
     ServicesCMSPage::$method ($id,$statuscomment);
     
     if (isset($params->urlRedirect)) {
        return new CopixActionReturn (CopixActionReturn::REDIRECT, $params->urlRedirect);
     }
     
     if (isset($this->vars['back']) && strlen($this->vars['back']) > 0) {//Redirect with a special url
        return new CopixActionReturn (CopixActionReturn::REDIRECT, $this->vars['back']);
     }else{
        return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array ('browse'=>'cms', 'id_head'=>$draft->id_head)));
     }
   }
   
   /**
    * doNext
    * nextStatus
    */
   function doNext ($params=array()) {
   	$servicesHeading = CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
      
      $id = isset($params['id']) ? $params['id'] : $this->vars['id'];
      
     $draft = ServicesCMSPage::getDraft ($id);
     if ($draft === null){
         return CopixActionGroup::process ('genericTools|Messages::getError',
         array ('message'=>CopixI18N::get ('admin.error.cannotFindDraft'),
         'back'=>CopixUrl::get ('copixheadings|admin|')));
     }
   
     $value = CopixUserProfile::valueOf ('cms', $servicesHeading->getPath ($draft->id_head));
   
     if ($value < PROFILE_CCV_WRITE){
         return CopixActionGroup::process ('genericTools|Messages::getError',
         array ('message'=>CopixI18N::get ('admin.error.notAnAuthorizedHead'),
                'back'=>CopixUrl::get ('copixheadings|admin|')));
     }
   
     $statuscomment = isset($this->vars['statuscomment_cmsp_'.$id]) ? $this->vars['statuscomment_cmsp_'.$id] : null;
   
     $workflow = CopixClassesFactory::getInstanceOf ('copixheadings|workflow');
     $nextid_head = $workflow->getNext($draft->id_head,'cms',$draft->status_cmsp);
     //print_r($draft);exit;
     //echo $draft->status_cmsp;
     //echo $nextid_head;exit;
     $method=null;
     switch ($nextid_head) {
     case $workflow->getPropose() :
        $method = 'setPropose';
        break;
     case $workflow->getValid() :
        $method = 'setValid';
        break;
     case $workflow->getPublish() :
        $method = 'setPublish';
        break;
     }
     
     if ($method) {
        ServicesCMSPage::$method ($id,$statuscomment);
     }
     
     if (isset($params->urlRedirect)) {
        return new CopixActionReturn (CopixActionReturn::REDIRECT, $params->urlRedirect);
     }
     if (isset($this->vars['back']) && strlen($this->vars['back']) > 0) {//Redirect with a special url
        return new CopixActionReturn (CopixActionReturn::REDIRECT, $this->vars['back']);
     }else{
        return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array ('browse'=>'cms', 'id_head'=>$draft->id_head)));
     }
   }

    /**
   * Asks for deletion of an online page
   */
    function doDeleteOnline () {
        $page            = ServicesCMSPage::getOnline ($this->vars['id']);
        $servicesHeading = CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');

        //cannot find the given draft
        if ($page === null){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('admin.error.cannotFindDraft'),
            'back'=>CopixUrl::get ('copixheadings|admin|')));
        }

        //Confirmation screen ?
        if (!isset ($this->vars['confirm'])){
            return CopixActionGroup::process ('genericTools|Messages::getConfirm',
            array ('title'=>CopixI18N::get ('admin.titlePage.confirmDelete'),
            'message'=>CopixI18N::get ('admin.message.confirmDelete'),
            'confirm'=>CopixUrl::get ('cms|workflow|deleteOnline', array ('confirm'=>1, 'id'=>$this->vars['id'])),
            'cancel'=>CopixUrl::get ('copixheadings|admin|', array ('id_head'=>$page->id_head,'browse'=>'cms'))));
        }

        if (CopixUserProfile::valueOf ('cms', $servicesHeading->getPath ($page->id_head)) < PROFILE_CCV_MODERATE){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('admin.error.cannotDelete'),
            'back'=>CopixUrl::get ('copixheadings|admin|', array ('id_head'=>$page->id_head))));
        }

        ServicesCMSPage::deleteOnline ($this->vars['id']);
        return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array ('browse'=>'cms', 'id_head'=>$page->id_head)));
    }
}
?>