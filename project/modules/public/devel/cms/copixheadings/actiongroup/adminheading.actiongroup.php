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
* Administration des / par rubriques
* @package cms
* @subpackage copixheadings
*/
class ActionGroupAdminHeading extends CopixActionGroup {
    /**
    * Le template de sortie
    */
    var $tpl = null;

    /**
    * Constructor
    */
    function __construct () {
        $this->tpl = new CopixTpl ();
    }

    /**
    * Gets the headings admin screen.
    * @param $this->vars['id_head'] the id of the browsed heading
    */
    function getAdmin () {
//        CopixEventNotifier::notify (new CopixEvent ('HeadingThemeRequest', array ('id'=>$this->vars['id_head'])));

        //On vérifie que la rubrique existe
        if (CopixRequest::get ('id_head', null, true) !== null){
            $dao = CopixDAOFactory::getInstanceOf ('CopixHeadings');
            if (($heading = $dao->get (CopixRequest::get ('id_head', null))) === false){
                return CopixActionGroup::process ('genericTools|Messages::getError',
                array ('message'=>CopixI18N::get ('admin.error.cannotFindHeading'),
                'back'=>CopixUrl::get('copixheadings|admin|', array ('browse'=>'copixheadings'))));
            }
        }

        //On vérifie que l'utilisateur à les privilèges pour voir la rubrique
        $headingProfileServices = CopixClassesFactory::getInstanceOf ('CopixProfileForHeadingServices');
        if (CopixUserProfile::valueOf ($headingProfileServices->getPath(CopixRequest::get ('id_head', null)),
        'copixheadings') < PROFILE_CCV_SHOW) {
            return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|quickAdmin'));
        }

        //On lance l'événement pour demander aux modules de nous dire s'ils peuvent être
        //administrés dans cette rubrique
        $response = CopixEventNotifier::notify (new CopixEvent ('HeadingAdminBrowsing', array ('id'=>CopixRequest::get ('id_head', null))));
        foreach ($response->getResponse () as $element) {
            if (isset ($element['module']) && isset ($element['icon'])){
                $moduleDesc = new StdClass ();
                $moduleDesc->icon = $element['icon'];
                $moduleDesc->name = $element['module'];
                $moduleDesc->shortDescription = $element['shortDescription'];
                $moduleDesc->longDescription  = $element['longDescription'];
                $modules[$element['module']] = $moduleDesc;
            }
        }

        //check if we've been asked for a given module, and if it exists for the current user
	    if (! in_array (CopixRequest::get ('browse', null), array_keys($modules))){
            $zoneParams['browse'] = $this->vars['browse'] = count ($tmpModule = array_keys ($modules)) ? $tmpModule[0] : null;
	    }

        $zoneParams = $this->vars;
        $zoneParams['modules']      = $modules;
        $zoneParams['id_head']      = CopixRequest::get ('id_head', null, true);

        //assigning dynamic content.
        $this->tpl->assign ('TITLE_PAGE', CopixI18N::get ('admin.titlePage.main'));
        $this->tpl->assign ('MAIN',       CopixZone::process ('AdminHeading', $zoneParams));

        return new CopixActionReturn (CopixActionReturn::DISPLAY, $this->tpl);
    }

    /**
    * Prepare a new CopixHeading for editing
    * @param int $this->vars['id_head'] the level the heading will belong to.
    */
    function doCreate (){
        $headingProfileServices = & CopixClassesFactory::getInstanceOf ('CopixProfileForHeadingServices');
        //We assume the default value is root.
        if ((!isset ($this->vars['id_head'])) || (strlen ($this->vars['id_head']) == 0)) {
            $this->vars['id_head'] = null;
        }

        //check if the heading exists, if given
        if ($this->vars['id_head'] !== null){
            $dao = CopixDAOFactory::getInstanceOf ('CopixHeadings');
            if (($heading = $dao->get ($this->vars['id_head'])) === false){
                return CopixActionGroup::process ('genericTools|Messages::getError',
                array ('message'=>CopixI18N::get ('admin.error.cannotFindHeading'),
                'back'=>CopixUrl::get ('copixheadings|admin|')));
            }
        }

        //check if we have the needed rights to create an element in this level
        if (CopixUserProfile::valueOf ($headingProfileServices->getPath ($this->vars['id_head']), 'copixheadings') < PROFILE_CCV_WRITE){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('admin.error.cannotCreateHeading'),
            'back'=>CopixUrl::get ('copixheadings|admin|', array ('id_head'=>$this->vars['id_head'], 'browse'=>'copixheadings'))));
        }

        //do what we're here for
        $record = & CopixDAOFactory::createRecord ('CopixHeadings');
        $record->father_head = $this->vars['id_head'];

        $this->_setSession ($record);
        return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|edit'));
    }

    /**
    * Show a form to create / update the CopixHeading
    * We assume this page is protected by the profile plugin
    * @param boolean (1 or unset) errors if we want to display the errors

    */
    function getEdit () {
        //check if we can bring the heading back
        if (($toEdit = $this->_getSession()) === null){
            return $this->_retrieveError ();
        }

        //now editing.
        $this->tpl->assign ('TITLE_PAGE', $toEdit->isNew () ? CopixI18N::get ('admin.titlePage.new') : CopixI18N::get ('admin.titlePage.update'));
        $this->tpl->assign ('MAIN', CopixZone::process ('EditCopixHeading', array ('toEdit'=>$toEdit, 'displayErrors'=>isset ($this->vars['errors']))));

        return new CopixActionReturn (CopixActionReturn::DISPLAY, $this->tpl);
    }
    
   /**
    * Display all the headings in a tree in order to select one of them
    */
   function getSelect () {
      $this->tpl->assign ('TITLE_PAGE', CopixI18N::get ('admin.titlePage.getSelect'));
      $this->tpl->assign ('MAIN', CopixZone::process ('SelectHeading', array ('select'=>$this->vars['select'],'back'=>$this->vars['back'])));
      return new CopixActionReturn (CopixActionReturn::DISPLAY, $this->tpl);
   }

    /**
    * Try to valid the currently edited element to the datebase.

    */
    function doValid (){
        $this->_validFromPost ();
        if (($toEdit = $this->_getSession()) === null){
            return $this->_retrieveError ();
        }

        //do we have errors ?
        $dao = CopixDAOFactory::getInstanceOf ('CopixHeadings');
        if (($errors = $dao->check ($toEdit)) !== true){
            return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|edit', array ('errors'=>1)));
        }

        //check if the heading exists, if given
        if ($toEdit->father_head !== null){
            $dao = CopixDAOFactory::getInstanceOf ('CopixHeadings');
            if (($heading = $dao->get ($toEdit->father_head)) === false){
                return CopixActionGroup::process ('genericTools|Messages::getError',
                array ('message'=>CopixI18N::get ('admin.error.cannotFindHeading'),
                'back'=>CopixUrl::get ('copixheadings|admin|', array ('browse'=>'copixheadings'))));
            }
        }

        //check that the user have the rights to save the object
        $headingProfileServices = & CopixClassesFactory::getInstanceOf ('CopixProfileForHeadingServices');
        if (CopixUserProfile::valueOf ($headingProfileServices->getPath($toEdit->father_head),
        'copixheadings') < PROFILE_CCV_WRITE){
            //go back to the edit screen.
            return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|edit'));
        }
        
        if (CopixConfig::get ('copixheadings|useCache') == '1') {
           CopixCache::clear ();
        }

        //save the object
        CopixDB::begin ();
        if ($toEdit->isNew ()){
            if ($result = $dao->insert ($toEdit)){
                if ($result  = $result && CopixProfileTools::createCapabilityPath ($headingProfileServices->getPath ($toEdit->father_head).'|'.$toEdit->id_head, CopixI18N::get ('copixheadings.message.heading', $toEdit->caption_head))){
                    CopixEventNotifier::notify (new CopixEvent ('HeadingCreated', array ('id_head'=>$toEdit->id_head)));
                }
            }
        } else {
            if ($result = $dao->update ($toEdit)){
                $result =  $result && CopixProfileTools::updateCapabilityPathDescription ($headingProfileServices->getPath ($toEdit->id_head), CopixI18N::get ('copixheadings.message.heading', $toEdit->caption_head));
            }
        }

        //did we succeed ?
        if ($result) {
            CopixDB::commit ();
            $this->_setSession (null);
            return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array ('id_head'=>$toEdit->father_head, 'browse'=>'copixheadings')));
        } else {
            CopixDB::rollback ();
            //unknow error, still, an error occured
            return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|edit', array ('errors'=>1)));
        }
    }

    /**
    * Set the current edited element to null and go back to the admin
    */
    function doCancel (){
        //get the currently edited element.
        if (($toEdit = $this->_getSession()) === null){
            //can't get the element back, go to the root level
            return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|'));
        }

        //where we're going to
        $to = $toEdit->father_head;

        //session will be empty
        $this->_setSession(null);

        //back to the admin screen, on the current level
        return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array ('id_head'=>$to, 'browse'=>'copixheadings')));
    }

    /**
    * Prepare an element to be edited.
    * @param int $this->vars['id_head'] the id of the element we want to update
    */
    function doPrepareEdit () {
        //check if we can edit the element.
        $headingProfileServices = & CopixClassesFactory::getInstanceOf ('CopixProfileForHeadingServices');
        if (CopixUserProfile::valueOf ($headingProfileServices->getPath($this->vars['id_head']),
        'copixheadings') < PROFILE_CCV_WRITE) {
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('admin.error.cannotUpdateHeading')));
        }

        //get the element.
        $dao = & CopixDAOFactory::getInstanceOf ('CopixHeadings');
        if (($record = $dao->get ($this->vars['id_head'])) === false){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('admin.error.cannotFindHeading')));
        }

        $recordDao = CopixDAOFactory::createRecord ('CopixHeadings'); 
        $recordDao->initFromDBObject ($record);

        //everything is ok, go to the editing form
        $this->_setSession ($recordDao);
        return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|edit'));
    }

    /**
    * paste the element, from the session.
    * we have to have an element in the pseudo clipboard.
    * we have to have write permissions on both destination level and "from" level
    */
    function doPaste (){
        //do we have an element in the clipboard ?
        if (!$this->_hasCut()){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('admin.error.cannotFindCut'),
            'back'=>CopixUrl::get ('copixheadings|admin|', array ('browse'=>'copixheadings'))));
        }

        //does the cutted element still exists ?
        $dao = & CopixDAOFactory::getInstanceOf ('CopixHeadings');
        if (($recordCut = $dao->get ($this->_getCut())) === false){
            $this->_clearCut ();
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('admin.error.cannotFindHeading')));
        }

        //is there a given destination ?
        if (!isset ($this->vars['id_head'])){
            return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array ('browse'=>'copixheadings')));
        }

        //do we want to paste in the root element ?
        if (strlen (trim ($this->vars['id_head'])) === 0){
            $this->vars['id_head'] = null;
        }

        //does the destination exists ?
        if (($this->vars['id_head'] !== null) && (($record = $dao->get ($this->vars['id_head'])) === false)){
            $this->_clearCut ();
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('admin.error.cannotFindHeading')));
        }

        //check if the destination is not one of the childs of the cutted element, and that the destination
        //is not the element itself
        $headingServices = CopixClassesFactory::getInstanceOf ('copixheadings|CopixHeadingsServices');

        if (in_array ($this->_getCut (), $headingServices->getFathers ($this->vars['id_head']))){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('admin.error.cannotPasteInElement')));
        }

        if ($this->vars['id_head'] == $this->_getCut ()){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('admin.error.cannotPasteInElement')));
        }

        //do we have write permissions on the element in the clipboard ?
        $headingProfileServices = & CopixClassesFactory::getInstanceOf ('CopixProfileForHeadingServices');
        if (CopixUserProfile::valueOf ($headingProfileServices->getPath($this->vars['id_head']),
        'copixheadings') < PROFILE_CCV_WRITE) {
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('admin.error.cannotUpdateHeading')));
        }

        //do we have write permissions on the destination element ?
        if (CopixUserProfile::valueOf ($beforePath = $headingProfileServices->getPath($this->_getCut ()),
        'copixheadings') < PROFILE_CCV_WRITE) {
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('admin.error.cannotUpdateHeading')));
        }

        //Now we know we can do it..... at last.
        //$ct = & CopixDB::getConnection ();
        $from = $recordCut->father_head;
        $recordCut->father_head = $this->vars['id_head'];
        $dao->update ($recordCut);

        if (CopixConfig::get ('copixheadings|useCache') == '1') {
           CopixCache::clear ();
        }

        //launch an event to notify listeners we moved a heading
        $response = & CopixEventNotifier::notify (new CopixEvent ('HeadingMove', array ('from'=>$from, 'to'=>$recordCut->father_head, 'id'=>$recordCut->id_head)));
        $rollBack = false;
        foreach ($response->getResponse () as $element){
            if ($element['failed'] === true) {
                $rollBack = true;
            }
        }
        if ($rollBack){
            $ct->rollback ();
        }else{
            //moves the path in the profile system
            CopixProfileTools::moveCapabilityPath($beforePath, $headingProfileServices->getPath ($recordCut->father_head).'|'.$recordCut->id_head);
            $this->_clearCut ();
        }

        return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array ('id_head'=>$recordCut->father_head, 'browse'=>'copixheadings')));
    }

    /**
    * Deletes a given heading
    * @param int $this->vars['id'] the heading id
    */
    function doDelete (){
        //No given element.
        if (!isset ($this->vars['id'])) {
            return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array ('browse'=>'copixheadings')));
        }

        //we cannot delete the root level.
        if (strlen ($this->vars['id']) == 0){
            return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array ('browse'=>'copixheadings')));
        }

        //now that we know there's a given heading, try to get it back (to check its existence)
        $dao = & CopixDAOFactory::getInstanceOf ('CopixHeadings');
        if (($record = $dao->get ($this->vars['id'])) === false){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('admin.error.cannotFindHeading')));
        }

        //we know there's an existing heading, and we want to cut it to delete it
        $headingProfileServices = & CopixClassesFactory::getInstanceOf ('CopixProfileForHeadingServices');
        if (CopixUserProfile::valueOf ($headingProfileServices->getPath($this->vars['id']),
        'copixheadings') < PROFILE_CCV_WRITE){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('admin.error.cannotUpdateHeading')));
        }

        //now we asks for the modules to know if we can delete it
        $response = CopixEventNotifier::notify(new CopixEvent ('HasContentRequest', array ('id'=>$this->vars['id'])));
        $who = array ();
        if ($response->inResponse ('hasContent', true, $who)){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('admin.error.cannotUpdateHeading')));
        }
        
        if (CopixConfig::get ('copixheadings|useCache') == '1') {
           CopixCache::clear ();
        }

        $dao->delete ($this->vars['id']);
        CopixEventNotifier::notify(new CopixEvent ('HeadingDeleted', array ('id_head'=>$this->vars['id'])));

        $headingProfileServices = & CopixClassesFactory::getInstanceOf ('CopixProfileForHeadingServices');
        CopixProfileTools::deleteCapabilityPath ($headingProfileServices->getPath ($record->father_head).'|'.$record->id_head);

        return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array ('browse'=>'copixheadings', 'id_head'=>$record->father_head)));
    }

    /**
    * cuts a menu element.
    * We have to have the rights to write in the given cutted element to be able to do so.
    * @param int level the level we want to cut
    */
    function doCut () {
        //No given element.
        if (!isset ($this->vars['id_head'])) {
            return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array ('browse'=>'copixheadings')));
        }

        //we cannot cut the root level.
        if (strlen ($this->vars['id_head']) == 0){
            return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array ('browse'=>'copixheadings')));
        }

        //now that we know there's a given heading, try to get it back (to check its existence)
        $dao = & CopixDAOFactory::getInstanceOf ('CopixHeadings');
        if (($record = $dao->get ($this->vars['id_head'])) === false){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('admin.error.cannotFindHeading')));
        }

        //we know there's an existing heading, and we want to cut it to the clipboard.
        //we'll check our rights.
        $headingProfileServices = & CopixClassesFactory::getInstanceOf ('CopixProfileForHeadingServices');
        if (CopixUserProfile::valueOf ($headingProfileServices->getPath($this->vars['id_head']),
        'copixheadings') < PROFILE_CCV_WRITE){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('admin.error.cannotUpdateHeading')));
        }

        $this->_setCut ($this->vars['id_head']);
        return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array ('browse'=>'copixheadings', 'id_head'=>$record->father_head)));
    }

    /**
    * cuts the element
    */
    function _setCut ($level) {
        $_SESSION['MODULE_COPIXHEADINGS_CUT'] = $level;
    }

    /**
    * gets the cutted heading
    */
    function _getCut () {
        if (isset ($_SESSION['MODULE_COPIXHEADINGS_CUT'])){
            return $_SESSION['MODULE_COPIXHEADINGS_CUT'];
        }else{
            return null;
        }
    }

    /**
    * says if there's a cutted element
    */
    function _hasCut (){
        return isset ($_SESSION['MODULE_COPIXHEADINGS_CUT']);
    }

    /**
    * clear the pseudo clipboard
    */
    function _clearCut (){
        session_unregister('MODULE_COPIXHEADINGS_CUT');
    }

    /**
    * Validation of the object with the given vars (get & post)
    *   is using the currenlty edited object
    * @return boolean wether or not we update the heading

    */
    function _validFromPost (){
        if (($toEdit = $this->_getSession()) === null){
            return false;
        }

        //updates simple fields.
        $simpleUpdate = array ('caption_head', 'description_head', 'url_head');
        foreach ($simpleUpdate as $toUpdate){
            if (isset ($this->vars[$toUpdate])){
                $toEdit->$toUpdate = $this->vars[$toUpdate];
            }
        }

        //now we update the father level if the user has the rights to write into
        //the given level
        if ($toEdit->isNew ()){
            if (isset ($this->vars['father_head'])){
                if (strlen ($this->vars['father_head']) === 0){
                    //Null if empty is given.
                    $this->vars['father_head'] = null;
                }

                $headingProfileServices = CopixClassesFactory::getInstanceOf ('CopixProfileForHeadingServices');
                if (CopixUserProfile::valueOf ($headingProfileServices->getPath ($this->vars['father_head']), 'copixheadings') < PROFILE_CCV_WRITE){
                    return false;//we don't care if the user lost his previous changed data,
                    //this error should'nt happen except if he tries to hack things
                }
            }
            $toEdit->father_head = $this->vars['father_head'];
        }

        //saves the changes.
        $this->_setSession($toEdit);
        return true;
    }

    /**
    * Put a heading in session (editing purposes)
    * @param object copixHeading the heading we wants to set in session
    * @return void

    */
    function _setSession ($copixHeading) {
        $_SESSION['MODULE_COPIXHEADING_EDIT'] = serialize ($copixHeading);
    }

    /**
    * gets the element back from the session
    * @return object the record previously set in session.
    *         Null if cannot bring it back

    */
    function _getSession () {
        if (isset ($_SESSION['MODULE_COPIXHEADING_EDIT'])) {
            CopixDAOFactory::fileInclude ('CopixHeadings');
            $unserialized = unserialize ($_SESSION['MODULE_COPIXHEADING_EDIT']);
            if (is_a ($unserialized, 'CompiledDAORecordCopixHeadings')){
                return $unserialized;
            }
        }

        return null;//not able to bring it back, then its null
    }

    /**
    * Error message to say we cannot bring the currently edited element back

    */
    function _retrieveError () {
        return CopixActionGroup::process ('genericTools|Messages::getError',
        array ('message'=>CopixI18N::get ('admin.error.noSession')));
    }
    
    /**
    * Says if we can paste something
    * @return boolean
    */
    function canPaste (){
        //check if the cutted element (if there is) still exists.
        if (ActionGroupAdminHeading::_hasCut()){
            $dao = CopixDAOFactory::getInstanceOf ('CopixHeadings');
            if (($record = $dao->get ($this->_getCut())) === false){
                $this->_clearCut ();
            }
            $pasteEnabled = true;

            //check if we can paste here.
            if ($record->father_head == CopixRequest::get('id_head',null,true)){
                //cannot paste in the same heading (it does not make any sense)
                $pasteEnabled = false;
            }
            //check if're not trying to paste in a child of the cutted heading (recursion)
            $headingServices = CopixClassesFactory::getInstanceOf ('copixheadings|CopixHeadingsServices');
            if (in_array ($this->_getCut (), $headingServices->getFathers (CopixRequest::get('id_head',null,true)))){
                $pasteEnabled = false;
            }
            //cannot paste the heading in the heading itself (recursion)
            if (CopixRequest::get('id_head',null,true) == $this->_getCut ()){
                $pasteEnabled = false;
            }
        }else{
            $pasteEnabled = false;
        }
        return $pasteEnabled;
    }
}
?>