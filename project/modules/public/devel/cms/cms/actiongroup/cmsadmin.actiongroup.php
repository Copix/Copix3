<?php
/**
* @package		cms
* @author		Croës Gérald
* @copyright	2001-2006 CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @ignore
 */
CopixClassesFactory::fileInclude ('cms|ServicesCMSPage');

/**
* Administration du CMS
* @package cms
*/
class ActionGroupCMSAdmin extends CopixActionGroup {
    /**
    * Récupération d'un brouillon de page
    */
   function getDraft () {
      if (($draftId = CopixRequest::get ('id', null, true)) === null){
         return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('||'));
      }


      if (($page = ServicesCMSPage::getDraft ($draftId)) !== null) {
         $tpl = new CopixTpl ();

         CopixEventNotifier::notify (new CopixEvent ('HeadingThemeRequest', array ('id'=>$page->id_head)));
         //add metaTag description and keywords
         if (strlen ($page->summary_cmsp) > 0){
             CopixHTMLHeader::addOthers ('<meta name="description" content="'.$page->summary_cmsp.'"> ');
         }
         if (strlen ($page->keywords_cmsp) > 0){
             CopixHTMLHeader::addOthers ('<meta name="keywords" content="'.$page->keywords_cmsp.'"> ');
         }
         $error = array ();//Création du tableau pour recevoir les messages d'erreur
         $content = ServicesCMSPage::getPageContent ($page, $error, false);
         if ($error) {
            return $error;
         }
         $tpl->assign ('TITLE_PAGE', $page->title_cmsp);
         if (isset($page->subtitle_cmsp) && trim($page->subtitle_cmsp)!='') {
            $tpl->assign ('TITLE_BAR', $page->subtitle_cmsp);
         }
         $tpl->assign ('MAIN', $content);
         return new CopixActionReturn (CopixActionReturn::DISPLAY, $tpl);
      }else{
         return new CopixActionReturn (CopixActionReturn::REDIRECT,  CopixUrl::get ('||'));
      }
   }

    /**
    * Création d'une nouvelle page pour le CMS
    * 
    * @param	int CopixRequest::get ('id_head')	la rubrique dans laquelle on souhaites créer la page
    */
    function doCreate () {
    	CopixClassesFactory::fileInclude ('cms|CMSAuth');
    	$user    = CMSAuth::getUser ();
    	$id_head = CopixRequest::get ('id_head', null, true);

    	if (! CMSAuth::canWrite ($id_head)){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('admin.error.notAnAuthorizedHead'),
            'back'=>CopixUrl::get ('copixheadings|admin|', array ('id_head'=>$this->vars['id_head']))));
    	}

        $page = ServicesCMSPage::create ($user->login, $id_head);
        $this->_setSession ($page);
        return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('cms|admin|edit'));
    }
    
    /**
     * Récupération d'une page en cours de modification
     * @param	CMSPage	$data	la page à renseigner
     * @return	mixed	true si ok, un retour de message d'erreur sinon	
     */
    private function _getPageFromSession (& $data){
        if (($data = $this->_getSession ()) === null){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('admin.error.cannotFoundEditedPage'),
            'back'=>CopixUrl::get ('copixheadings|admin|')));
        }
        return true;
    }

    /**
    * Formulaire de modification d'une page
    */
    function getEdit (){
    	$data = null;
    	if (($return = $this->_getPageFromSession ($data)) !== true){
    		return $return;
    	}

    	//check the wanted edition kind
        $kind = intval(CopixRequest::get ('kind', 0, true));

        $tpl = new CopixTpl ();
        $tpl->assign ('TITLE_PAGE', $data->version_cmsp === 0 ? CopixI18N::get ('page.title.new') : CopixI18N::get ('page.title.edit'));
        $tpl->assign ('MAIN', CopixZone::process ('PageEdit',
                      array ('toEdit'=>$data,
                             'kind'=>$kind,
                             'errors'=>CopixRequest::get ('errors', 0, true))));
        return new CopixActionReturn (CopixActionReturn::DISPLAY, $tpl);
    }

    /**
    * page de liste des templates, permettant le choix de ces derniers.
    */
    function getTemplateChoice (){
        $this->_validFromPost  ();
        $tpl = new CopixTpl ();
        $tpl->assign ('TITLE_PAGE', CopixI18N::get ('page.titlePage.selectModel'));

        //appel le traitement de la zone,
        //  en passant l'url de base ou valider le choix du template.
        $tpl->assign ('MAIN', CopixZone::process ('TemplateChoice',
        array ('url'=>CopixUrl::get ('cms|admin|validedit'),
        'back_url'=>CopixUrl::get ('cms|admin|edit', array ('kind'=>0)))));

        return new CopixActionReturn (CopixActionReturn::DISPLAY, $tpl);
    }

    /**
    * valid data from post.
    */
    function _validFromPost () {
        $toCheck = array ('titlebar_cmsp', 'title_cmsp', 'summary_cmsp', 'content_wikp', 'keywords_cmsp');
        $data = $this->_getSession ();
        foreach ($toCheck as $name){
            if (isset ($this->vars[$name])){
                $data->$name = $this->vars[$name];
            }
        }

        //max publication date
        if (isset ($this->vars['datemax_cmsp'])){
            $data->datemax_cmsp = CopixI18N::dateToBD ($this->vars['datemax_cmsp']);
        }
        //min publication date
        if (isset ($this->vars['datemin_cmsp'])){
            $data->datemin_cmsp = CopixI18N::dateToBD ($this->vars['datemin_cmsp']);
        }

        //check for the template.
        if (isset ($this->vars['templateId'])){
           $data->setTemplate ($this->vars['templateId']);
        }
        $this->_setSession ($data);
    }

    /**
    * Affiche l'historique de modification des pages
    * The user has to be able to write in the heading.
    * The page has to exists
    * @param int $this->vars['id'] the page id we wants the history of.
    */
    function getShowHistory (){
        //check if we have a given page id
        if (!isset ($this->vars['id'])){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('admin.error.noGivenPage'),
            'back'=>CopixUrl::get ('copixheadings|admin|')));
        }

        //check if we can retrieve the page.
        if (($page = ServicesCMSPage::getOnline ($this->vars['id'])) === null) {
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('admin.error.cannotLoadPage'),
            'back'=>CopixUrl::get ('copixheadings|admin')));
        }

        //check if we can write in the given heading
        CopixClassesFactory::fileInclude ('cms|CmsAuth');
        if (!CMSAuth::canWrite ($page->id_head)) {
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('admin.error.notAnAuthorizedHead'),
            'back'=>CopixUrl::get ('copixheadings|admin|', array ('id_head'=>$page->id_head))));
        }

        //let's then
        $tpl = new CopixTpl ();
        $tpl->assign ('TITLE_PAGE', CopixI18N::get ('admin.title.pageHistory'));
        $tpl->assign ('MAIN', CopixZone::process ('PageHistory', array ('id'=>$this->vars['id'])));
        return new CopixActionReturn (CopixActionReturn::DISPLAY, $tpl);
    }

    /**
    * Show the version of the given page.
    * @param int $this->vars['id'] the page id
    * @param int $this->vars['version'] the version

    */
    function getShowVersion (){
        //Check if we have the version and id given
        if (! (isset ($this->vars['id']) && isset ($this->vars['version']))){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('admin.error.noGivenPage'),
            'back'=>CopixUrl::get ('copixheadings|admin|')));
        }

        //check if we can retrive the page
        if (($page = ServicesCMSPage::getVersion ($this->vars['id'], $this->vars['version'])) === null){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('admin.error.cannotLoadPage'),
            'back'=>CopixUrl::get ('copixheadings|admin|')));
        }

        //check if we can contribute in the given heading. (required to see history of a given page)
        $servicesHeadings = CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
        if (CopixUserProfile::valueOf ('cms', $servicesHeadings->getPath ($page->id_head)) < PROFILE_CCV_WRITE) {
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('admin.error.notAnAuthorizedHead'),
            'back'=>CopixUrl::get ('copixheadings|admin|', array ('id_head'=>$page->id_head))));
        }

        CopixEventNotifier::notify (new CopixEvent ('HeadingThemeRequest', array ('id'=>$page->id_head)));
        CopixEventNotifier::notify (new CopixEvent ('HeadingFrontBrowsing',
        array ('id'=>$page->id_head)));

        //gets the model page if any
        $response = CopixEventNotifier::notify (new CopixEvent ('HeadingModelRequest',
        array ('id'=>$page->id_head)));

        $modelPage = null;
        foreach ($response->getResponse () as $element) {
            //we will override if two model pages are found.
            if ($element['model'] !== null){
                $modelPage = $element['model'];
            }
        }

        $toShow = '';
        if ($modelPage !== null && ($modelPage->id_cmsp !== $page->id_cmsp)){
            $modelPage->addPortletMessage ('ModelPage', $page->getParsed (CMSParseContext::front));
            $toShow = $modelPage->getParsed (CMSParseContext::front);
        }else{
            $toShow = $page->getParsed (CMSParseContext::front);
        }

        $tpl = new CopixTpl ();
        $tpl->assign ('TITLE_PAGE', $page->title_cmsp);
        $tpl->assign ('MAIN', $toShow);

        return new CopixActionReturn (CopixActionReturn::DISPLAY, $tpl);
    }

    /**
    * prepare a page (draft) to be updated
    * @param int $this->vars['id'] the id of the page we wants to edit.
    * NOTE: We can only edit drafts

    */
    function doPrepareEdit () {
        $page = ServicesCMSPage::getDraft ($this->vars['id']);
        if ($page === null) {
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get('error|error.editPageNotFound'),
            'back'=>CopixUrl::get ('copixheadings|admin|')));
        }
        if (isset($this->vars['back']) && strlen($this->vars['back']) > 0) {
            $_SESSION['MODULE_CMS']['EDIT_URLBACK']=$this->vars['back'];
        }
        $this->_setSession ($page);
        $extraParams = array ('kind'=>1);
        return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('cms|admin|edit', $extraParams));
    }

    /**
    * Creates a new page from a given page
    * The user has to be able to write in the given heading
    * The page must exists
    *
    * @param boolean $this->vars['update'] if given, we want to update the page,
    *   if not, we want a copy of the page.
    * @param int $this->vars['id'] the page id

    */
    function doNewFromPage () {
    	CopixClassesFactory::create ('cms|cmsauth');
    	$user = CMSAuth::getUser();
        //check if we got a page id
        if (! isset ($this->vars['id'])){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('admin.error.noGivenPage'),
            'back'=>CopixUrl::get ('||')));
        }

        if (! isset ($this->vars['version'])){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('admin.error.noGivenPage'),
            'back'=>CopixUrl::get ('||')));
        }

        //check if the page exists.
        if (($page = ServicesCMSPage::getVersion ($this->vars['id'], $this->vars['version'])) === null){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('admin.error.cannotLoadPage'),
            'back'=>CopixUrl::get ('||')));
        }

        //check if the users has the rights to write into the given heading.
        if (!CMSAuth::canWrite ($page->id_head)) {
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('admin.error.notAnAuthorizedHead'),
            'back'=>CopixUrl::get ('copixheadings|admin|', array ('id_head'=>$page->id_head))));
        }

        //Check if there already is a draft
        if ($draft = ServicesCMSPage::getDraft ($this->vars['id'])){
        	//a draft exists
        	//if its the user's draft or a moderator, going to edit it
        	if (($user->login == $draft->author_cmsp) || CMSAuth::canModerate ($draft->id_head)){
        		return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixURL::get ('cms|admin|prepareEdit', array ('id'=>$draft->publicid_cmsp)));
        	}else{
        		return CopixActionGroup::process ('genericTools|Messages::getError',
    		        array ('message'=>CopixI18N::get ('admin.error.alreadyADraft', array ('author_cmsp'=>$draft->statusauthor_cmsp)),
            		'back'=>CopixUrl::get ('copixheadings|admin|', array ('id_head'=>$page->id_head))));
        	}
        }

        //No existing draft for the given page
        if ($draftId = ServicesCMSPage::newDraftFromPage ($this->vars['id'], $this->vars['version'], isset ($this->vars['update']), $user->login)){
            return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixURL::get ('cms|admin|prepareEdit', array ('id'=>$draftId)));
        }

        //Error, we were not able to create a draft for the page. 
        return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('admin.error.cannotLoadPage'),
            'back'=>CopixUrl::get ('||')));
    }

    /**
    * Redirect the user to the correct location after a page validation
    */
    function doAfterValid (){
        if (($page = ServicesCMSPage::getDraft ($this->vars['id'])) === null){
            //ok, we can't get the page back..... we just go to the main heading admin screen
            return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixURL::get ('copixheadings|admin|'));
        }
        if (isset($_SESSION['MODULE_CMS']['EDIT_URLBACK']) && strlen($_SESSION['MODULE_CMS']['EDIT_URLBACK'])) {
           $urlBack = $_SESSION['MODULE_CMS']['EDIT_URLBACK'];
           unset($_SESSION['MODULE_CMS']['EDIT_URLBACK']);
            return new CopixActionReturn (CopixActionReturn::REDIRECT,$urlBack);
        }else{
           return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixURL::get ('copixheadings|admin|', array ('browse'=>'cms','id_head'=>$page->id_head)));
        }
    }

    /**
    * redirect the user to the correct location after a cancellation.
    */
    function doCancel () {
        $extraParams['browse'] = 'cms';
        if (isset ($_SESSION['MODULE_CMS']['CMSPAGE'])){
            $page = unserialize ($_SESSION['MODULE_CMS']['CMSPAGE']);

            //hack, because we cannot read page->id_head..... for a weird reason.
            $vars = get_object_vars($page);

            //echo serialize ($page);
            if (isset ($vars['id_head'])){
                $extraParams['id_head'] = $vars['id_head'];
            }
        }

        $clearRecord = null;
        $this->_setSession ($clearRecord);
        if (isset($_SESSION['MODULE_CMS']['EDIT_URLBACK']) && strlen($_SESSION['MODULE_CMS']['EDIT_URLBACK'])) {
           $urlBack = $_SESSION['MODULE_CMS']['EDIT_URLBACK'];
           unset($_SESSION['MODULE_CMS']['EDIT_URLBACK']);
           return new CopixActionReturn (CopixActionReturn::REDIRECT,$urlBack);
        }else{
           return new CopixActionReturn(CopixActionReturn::REDIRECT, CopixURL::get ('copixheadings|admin|', $extraParams));
        }
    }

    /**
    * says if we can paste the cutted element (if any) in the given heading (id)
    * @param int this->vars['id_head'] the heading where we wants to paste the cutted element into
    * @return bool
    */
    function canPaste (){
        if (!$this->_hasCut()){
            return false;
        }

        //does the element still exists ?
        if (($page = ServicesCMSPage::getOnline ($this->_getCut())) === null){
            $this->_clearCut ();
            return false;
        }

        //is there a given destination ?
        if ((! isset ($this->vars['id_head'])) || (strlen (trim ($this->vars['id_head'])) == 0)) {
            $this->vars['id_head'] = null;
        }

        //does the destination heading exists ?
        if ($this->vars['id_head'] !== null){
            $dao = CopixDAOFactory::getInstanceOf ('copixheadings|CopixHeadings');
            if (($record = $dao->get ($this->vars['id_head'])) === false) {
                return false;
            }
        }

        //do we have write permissions on the destination ?
        $headingProfileServices = CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
        if (CopixUserProfile::valueOf ('cms', $headingProfileServices->getPath($this->vars['id_head'])) < PROFILE_CCV_WRITE) {
            return false;
        }
        //do we have write permissions on the cutted element ?
        if (CopixUserProfile::valueOf ('cms', $headingProfileServices->getPath($page->id_head)) < PROFILE_CCV_WRITE) {
            return false;
        }
        return true;
    }

    /**
    * paste the element, from the session.
    * we have to have an element in the pseudo clipboard.
    * we have to have write permissions on both destination id_head and "from" id_head
    * The page have to exists. We move all the version of the page.
    * The dest heading must exists
    */
    function doPaste () {
        //is there a given destination ?
        if ((! isset ($this->vars['id_head'])) || (strlen (trim ($this->vars['id_head'])) == 0)) {
            $this->vars['id_head'] = null;
        }

        //do we have an element in the clipboard ?
        if (!$this->_hasCut()){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('admin.error.cannotFindCut'),
            'back'=>CopixUrl::get ('copixheadings|admin|', array ('browse'=>'cms', 'id_head'=>$this->vars['id_head']))));
        }

        //does the element still exists ?
        if (($page = ServicesCMSPage::getOnline ($this->_getCut())) === null){
            $this->_clearCut ();
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('admin.error.cannotFindPage'),
            'back'=>CopixUrl::get ('copixheadings|admin|', array ('browse'=>'cms', 'id_head'=>$this->vars['id_head']))));
        }

        //does the destination heading exists ?
        if ($this->vars['id_head'] !== null){
            $dao = CopixDAOFactory::getInstanceOf ('copixheadings|CopixHeadings');
            if (($record = $dao->get ($this->vars['id_head'])) === false) {
                $this->_clearCut ();
                return CopixActionGroup::process ('genericTools|Messages::getError',
                array ('message'=>CopixI18N::get ('copixheadings|admin.error.cannotFindHeading')));
            }
        }

        //do we have write permissions on the destination ?
        $headingProfileServices = CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
        if (CopixUserProfile::valueOf ('cms', $headingProfileServices->getPath($this->vars['id_head'])) < PROFILE_CCV_WRITE) {
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('admin.error.cannotPasteHere')));
        }

        //do we have write permissions on the cutted element ?
        if (CopixUserProfile::valueOf ('cms', $headingProfileServices->getPath($page->id_head)) < PROFILE_CCV_WRITE) {
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('admin.error.cannotMoveElement')));
        }

        ServicesCMSPage::moveHeading ($page->id_cmsp, $this->vars['id_head']);
        $this->_clearCut ();
        return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array ('browse'=>'cms', 'id_head'=>$this->vars['id_head'])));
    }

    /**
    * cuts a menu element.
    * We have to have the rights to write in the given heading the cutted element belongs to to be able to do so.
    * @param int id the page we wants to cut
    */
    function doCut () {
        //No given element.
        if (! isset ($this->vars['id'])) {
            return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin'));
        }

        //now that we know there's a given page, try to get it back (to check its existence)
        if (($page = ServicesCMSPage::getOnline ($this->vars['id'])) === null){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('admin.error.cannotFindPage')));

        }

        //we know there's an existing page, and we want to cut it to the clipboard.
        //we'll check our rights.
        $headingProfileServices = CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
        if (CopixUserProfile::valueOf ('cms', $headingProfileServices->getPath($page->id_head)) < PROFILE_CCV_WRITE){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('admin.error.cannotMoveElement')));
        }

        //ok, we can cut the element
        $this->_setCut ($this->vars['id']);
        return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array ('browse'=>'cms', 'id_head'=>$page->id_head)));
    }

    /**
    * cuts the element
    */
    function _setCut ($id_head) {
        $_SESSION['MODULE_CMS']['CUT'] = $id_head;
    }

    /**
    * gets the cutted heading
    */
    function _getCut () {
        if (isset ($_SESSION['MODULE_CMS']['CUT'])){
            return $_SESSION['MODULE_CMS']['CUT'];
        }else{
            return null;
        }
    }

    /**
    * says if there's a cutted element
    */
    function _hasCut (){
        return isset ($_SESSION['MODULE_CMS']['CUT']);
    }

    /**
    * clear the pseudo clipboard
    */
    function _clearCut (){
        unset ($_SESSION['MODULE_CMS']['CUT']);
    }

    /**
    * Gets the page from the session.
    * @return CMSPage

    */
    function _getSession () {
    	CopixClassesFactory::fileInclude ('cms|CMSPage');
        return unserialize ($_SESSION['MODULE_CMS']['CMSPAGE']);
    }

    /**
    * Sets the page in session
    * @param object page the page to set in session

    */
    function _setSession ($page){
    	if ($page === null){
           $_SESSION['MODULE_CMS']['CMSPAGE'] = null;
           return;    		
    	}
        //AutoSave feature.
        if (intval (CopixConfig::get ('cms|portletAutoSave')) == 1){
        	if (($errors = $page->check ()) === true){
               if ($page->id_cmsp === null){
                   ServicesCMSPage::insert ($page);
               }else{
                   ServicesCMSPage::update ($page);
               }
        	}
        }
        //saving in the session
        $_SESSION['MODULE_CMS']['CMSPAGE'] = serialize ($page);
    }

    /**
    * validation temporaire des éléments saisis.
    * @param int $this->vars['kind'] The tab we wants to be focused on. (0 general, 1 Content, 2 preview)
    */
    function doValidEdit (){
        $this->_validFromPost ();
        return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('cms|admin|edit', array ('kind'=>CopixRequest::get ('kind', 1))));
    }
    /**
    * validation de la page en base.
    * On ne peut valider que des brouillons.
    */
    function doValid (){
        $this->_validFromPost ();
        $page = $this->_getSession ();

        if (($errors = $page->check ()) !== true){
        	return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('cms|admin|edit', array ('kind'=>0, 'errors'=>1)));
        }

        //On ne peut insérer ou modifier qu'un brouillon.
        if ($page->id_cmsp === null) {
            ServicesCMSPage::insert ($page);
        }else{
            ServicesCMSPage::update ($page);
        }
        //si demande de proposition
        if (isset($this->vars['doBest'])) {
            $workflow = CopixClassesFactory::getInstanceOf ('copixheadings|workflow');
            if (CopixConfig::get ('cms|easyWorkflow') == 1){
               return CopixActionGroup::process ('cms|cmspageworkflow::doBest', 
                     array ('id'=>$page->publicid_cmsp,
                           'urlRedirect'=>CopixUrl::get ('cms|admin|afterValid', array ('id'=>$page->publicid_cmsp))));
            }else{
               return CopixActionGroup::process ('cms|cmspageworkflow::doNext', 
                     array ('id'=>$page->publicid_cmsp,
                           'urlRedirect'=>CopixUrl::get ('cms|admin|afterValid', array ('id'=>$page->publicid_cmsp))));
            }
        }else{
            return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('cms|admin|afterValid', array ('id'=>$page->publicid_cmsp)));
        }
    }

    /**
    * Adds the portlet to the given page
    */
    function doValidPortlet (){
        $page     = $this->_getSession ();
        $portlet  = CMSPortletTools::getSessionPortlet ();
        $position = CMSPortletTools::getWishedPosition ();

        if ($page->findPortletById ($portlet->id) === null){
            $page->addPortlet ($portlet, $position);
        }else{
            $page->updatePortlet ($portlet);
        }
        $this->_setSession ($page);
        return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('cms|admin|edit', array ('kind'=>1)));
    }

    /**
    * Création d'une portlet du bon type, redirection vers sa page d'édition.
    */
    function doNewPortlet () {
        if (! isset ($this->vars['templateVar'])) {
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get('cmspage.error.haveToGivePlace'),
            'back'=>CopixUrl::get ('cms|admin|edit', array (), true)));
        }
        if (($data = $this->_getSession ()) !== null){
           CMSPortletTools::setWishedPosition ($this->vars['templateVar']);
           CMSPortletTools::setSessionPortlet (PortletFactory::create ($this->vars['portlet']));
        }
        return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('cms_portlet_'.$this->vars['portlet'].'||edit'));
    }

    /**
    * Prepare a portlet to be edited.
    */
    function doPreparePortletEdit (){
        $page    = $this->_getSession ();
        $portlet = $page->findPortletById ($this->vars['id']);
        CMSPortletTools::setSessionPortlet ($portlet);
        return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('cms_portlet_'.$portlet->getAddOnName ().'||edit'));
    }

    /**
    * Moves the portlet down
    */
    function doMovePortletDown (){
        $page    = $this->_getSession ();
        $page->movePortletDown ($this->vars['id']);
        $this->_setSession ($page);
        return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('cms|admin|edit', array ('kind'=>1)));
    }

    /**
    * Moves the portlet up
    */
    function doMovePortletUp (){
        $page    = $this->_getSession ();
        $page->movePortletUp ($this->vars['id']);
        $this->_setSession ($page);
        return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('cms|admin|edit', array ('kind'=>1)));
    }

    /**
    * Deletes the portlet from the page.
    */
    function doDeletePortlet (){
        $page    = $this->_getSession ();
        //now we asks for the modules to know if we can delete portlet
        $response = CopixEventNotifier::notify(new CopixEvent ('DeletePortlet', array ('id'=>$this->vars['id'])));
        $who = array ();
        if ($response->inResponse ('canDelete', false, $who)){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('admin.error.cannotDeletePortlet')));
        }
        $page->deletePortlet ($this->vars['id']);
        $this->_setSession ($page);

        return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get('cms|admin|edit', array ('kind'=>1)));
    }

    /**
    * cancel the portlet
    */
    function doCancelPortlet (){
        CMSPortletTools::setSessionPortlet (null);
        return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('cms|admin|edit', array ('kind'=>1)));
    }    
    /**
    * Do copy the portlet to the Clipboard (session)
    */
    function doCopyPortlet (){
        $page    = $this->_getSession ();
        $portlet = $page->findPortletById ($this->vars['id']);
        CMSPortletTools::setClipboardPortlet ($portlet);

        return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('cms|admin|edit', array ('kind'=>1)));
    }

    /**
    * Cuts the portlet to the session
    */
    function doCutPortlet (){
        $page    = $this->_getSession ();
        $portlet = $page->findPortletById ($this->vars['id']);
        CMSPortletTools::setClipboardPortlet ($portlet);
        $page->deletePortlet ($this->vars['id']);
        $this->_setSession ($page);

        return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('cms|admin|edit', array ('kind'=>1)));
    }

    /**
    * paste the portlet from the clipboard (session)
    */
    function doPastePortlet (){
        $page    = $this->_getSession ();
        $portlet = CMSPortletTools::getClipboardPortlet ();
        $portlet->id = uniqId ('p_');//new id....
        $page->addPortlet ($portlet, $this->vars['templateVar']);
        $this->_setSession ($page);

        //delete the portlet from session
        CMSPortletTools::setClipboardPortlet (null);
        return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('cms|admin|edit', array ('kind'=>1)));
    }

    /**
    * Affiche une liste de sélection pour l'ajout d'un élément.
    */
    function getPortletChoice (){
        if (!isset ($this->vars['templateVar'])){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get('cmspage.error.noPlaceForElem'),
            'back'=>CopixURL::get ('cms|admin|edit')));
        }

        //Création du template.
        $tpl = new CopixTpl ();
        $toPaste = CMSPortletTools::getClipboardPortlet ();
        $tpl->assign ('TITLE_PAGE', CopixI18N::get ('cms.title.addPortlet'));

        $tpl->assign ('MAIN', CopixZone::process   ('PortletChoice',
        array ('templateVar'=>$this->vars['templateVar'],
        'pasteEnable'=>($toPaste !== null))));
        return new CopixActionReturn (CopixActionReturn::DISPLAY, $tpl);
    }
}
?>
