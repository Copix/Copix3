<?php
/**
* @package	 cms
* @subpackage document
* @author	Bertrand Yan, Croes Gérald
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* @package	 cms
* @subpackage document
* handle the administration of the documents
*/
class ActionGroupDocumentAdmin extends CopixActionGroup {
	/**
    * says if we can paste the cutted element (if any) in the given heading (id)
    * @param int this->vars['level'] the heading where we wants to paste the cutted element into
    * @return bool
    */
	function canPaste (){
		if (! $this->_hasCut()){
			return false;
		}

		$dao = CopixDAOFactory::create ('Document');
		//récupération de la dernière version du document
		$version = $dao->getLastVersion ($this->_getCut());
		if (!$document = $dao->get ($this->_getCut(), $version)){
			$this->_clearCut ();
			return false;
		}

		//does the destination heading exists ?
		if (($heading = CopixRequest::get ('id_head', null, true)) !== null){
			$dao = CopixDAOFactory::getInstanceOf ('copixheadings|CopixHeadings');
			if (($record = $dao->get ($heading)) === false) {
				return false;
			}
		}

		//do we have write permissions on the destination ?
		$headingProfileServices = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
		if (CopixUserProfile::valueOf ('document', $headingProfileServices->getPath($heading)) < PROFILE_CCV_WRITE) {
			return false;
		}

		//do we have write permissions on the cutted element ?
		if (CopixUserProfile::valueOf ('document', $headingProfileServices->getPath($document->id_head)) < PROFILE_CCV_PUBLISH) {
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
		if ((! isset ($this->vars['id_head'])) || (strlen (trim ($this->vars['id_head'])) == 0)) {
			$this->vars['id_head'] = null;
		}

		//do we have an element in the clipboard ?
		if (! $this->_hasCut()){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('document.error.cannotFindCut'),
			'back'=>CopixUrl::get ('copixheadings|admin|', array ('browse'=>'document', 'id_head'=>$this->vars['id_head']))));
		}

		$dao = & CopixDAOFactory::create ('Document');
		//get last version if no version specify
		$version = $dao->getLastVersion ($this->_getCut());
		if (!$document = $dao->get ($this->_getCut(), $version)){
			$this->_clearCut ();
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('document.error.cannotFindDocument'),
			'back'=>CopixUrl::get ('copixheadings|admin|', array('browse'=>'document', 'id_head'=>$this->vars['id_head']))));
		}

		//does the destination heading exists ?
		if ($this->vars['id_head'] !== null){
			$daoHeading = & CopixDAOFactory::getInstanceOf ('copixheadings|CopixHeadings');
			if (($record = $daoHeading->get ($this->vars['id_head'])) === false) {
				$this->_clearCut ();
				return CopixActionGroup::process ('genericTools|Messages::getError',
				array ('message'=>CopixI18N::get ('copixheadings|admin.error.cannotFindHeading')));
			}
		}

		//do we have write permissions on the destination ?
		$headingProfileServices = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
		if (CopixUserProfile::valueOf ('document', $headingProfileServices->getPath($this->vars['id_head'])) < PROFILE_CCV_WRITE) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('document.error.cannotPasteHere')));
		}

		//do we have write permissions on the cutted element ?
		if (CopixUserProfile::valueOf ('document', $headingProfileServices->getPath($document->id_head)) < PROFILE_CCV_PUBLISH) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('document.error.cannotMoveElement')));
		}

		$dao->moveHeading ($document->id_doc, $this->vars['id_head']);
		$this->_clearCut ();
		return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array ('browse'=>'document', 'id_head'=>$this->vars['id_head'])));
	}

	/**
    * cuts a document
    * We have to have the rights to write in the given heading the cutted element belongs to to be able to do so.
    * @param int id_doc the document we wants to cut
    */
	function doCut () {
		//No given element.
		if (! isset ($this->vars['id_doc'])) {
			return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin', array('browse'=>'document')));
		}

		$dao = & CopixDAOFactory::create ('Document');
		//get last version if no version specify
		$version = isset($this->vars['version']) ? $this->vars['version'] : $dao->getLastVersion ($this->vars['id_doc']);
		if (!$toCut = $dao->get ($this->vars['id_doc'], $version)){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('document.error.cannotFindDocument'),
			'back'=>CopixUrl::get ('copixheadings|admin|', array('browse'=>'document'))));
		}
		//check if the user has the rights
		$servicesHeadings = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
		if (CopixUserProfile::valueOf ('document', $servicesHeadings->getPath ($toCut->id_head)) < PROFILE_CCV_WRITE) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('document.error.notAnAuthorizedHead'),
			'back'=>CopixUrl::get ('copixheadings|admin|', array('browse'=>'document','id_head'=>$toCut->id_head))));
		}

		//ok, we can cut the element
		$this->_setCut ($this->vars['id_doc']);
		return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array ('browse'=>'document', 'id_head'=>$toCut->id_head)));
	}

	/**
    * cuts the element
    */
	function _setCut ($id_doc) {
		$_SESSION['MODULE_DOCUMENT_CUT'] = $id_doc;
	}

	/**
    * gets the cutted heading
    */
	function _getCut () {
		if (isset ($_SESSION['MODULE_DOCUMENT_CUT'])){
			return $_SESSION['MODULE_DOCUMENT_CUT'];
		}else{
			return null;
		}
	}

	/**
    * says if there's a cutted element
    */
	function _hasCut (){
		return isset ($_SESSION['MODULE_DOCUMENT_CUT']);
	}

	/**
    * clear the pseudo clipboard
    */
	function _clearCut (){
		session_unregister('MODULE_DOCUMENT_CUT');
	}

	function getViewVersion () {
		if (!isset ($this->vars['id_doc'])){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('document.error.mustSpecifyId'),
			'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'document'))));
		}

		$dao = & CopixDAOFactory::create ('Document');
		//get last version if no version specify
		$version = isset($this->vars['version']) ? $this->vars['version'] : $dao->getLastVersion ($this->vars['id_doc']);
		if (!$document = $dao->get ($this->vars['id_doc'], $version)){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('document.error.cannotFindDocument'),
			'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'document'))));
		}
		//check if the user has the rights
		$servicesHeadings = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
		if (CopixUserProfile::valueOf ('document', $servicesHeadings->getPath ($document->id_head)) < PROFILE_CCV_WRITE) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('document.error.notAnAuthorizedHead'),
			'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'document','id_head'=>$document->id_head))));
		}
		$tpl = & new CopixTpl ();
		$tpl->assign ('TITLE_PAGE', CopixI18N::get ('document.titlePage.versionDocument'));

		$tpl->assign ('MAIN', CopixZone::process ('ViewVersion', array ('id_head'=>$document->id_head,'id_doc'=>$this->vars['id_doc'])));
		return new CopixActionReturn (CopixactionReturn::DISPLAY, $tpl);
	}

	function getOnlineDocument () {
		if (!isset ($this->vars['id_head'])){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('document.error.mustSpecifyId'),
			'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'document'))));
		}
		//check if the user has the rights
		$servicesHeadings = CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
		if (CopixUserProfile::valueOf ('document', $servicesHeadings->getPath ($this->vars['id_head'])) < PROFILE_CCV_WRITE) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('document.error.notAnAuthorizedHead'),
			'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'document','id_head'=>$this->vars['id_head']))));
		}
		$tpl = new CopixTpl ();
		$tpl->assign ('TITLE_PAGE', CopixI18N::get ('document.titlePage.onlineDocument'));

		$tpl->assign ('MAIN', CopixZone::process ('OnlineDocument', array ('id_head'=>$this->vars['id_head'])));
		return new CopixActionReturn (CopixactionReturn::DISPLAY, $tpl);
	}

	/**
    * apply updates on the edited document.
    * save to datebase if ok and save file.
    */
	function doValid (){
		if (!$toValid = $this->_getSessionDocument()){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('document.error.cannotGetDocumentBack'),
			'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'document'))));
		}

		$plugAuth         = & CopixController::instance ()->getPlugin ('auth|auth');
		$user             = & $plugAuth->getUser();
		$login            = $user->login;
		$dao              = & CopixDAOFactory::create ('Document');
		$workflow         = & CopixClassesFactory::getInstanceOf ('copixheadings|workflow');

		//check if the user has the rights
		$servicesHeadings = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
		$capability       = CopixUserProfile::valueOf ('document', $servicesHeadings->getPath ($toValid->id_head));

		if ($capability < PROFILE_CCV_WRITE ||
		($capability < PROFILE_CCV_VALID && $toValid->status_doc == $workflow->getPropose()) ||
		($capability < PROFILE_CCV_PUBLISH && $toValid->status_doc == $workflow->getValid())) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('document.error.notAnAuthorizedHead'),
			'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'document','id_head'=>$toValid->id_head))));
		}

		$this->_validFromForm($toValid);
		//inserting or updating.
		if ($toValid->id_doc !== null){
			if ($dao->check ($toValid) !== true){
				$this->_setSessionDocument($toValid);
				return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('document|admin|edit',array('e'=>1)));
			}
			//check if it's a new version or not
			if ($toValid->newVersion) {
				$toValid->version_doc = $dao->getLastVersion ($toValid->id_doc) + 1;
			}

			if (isset($this->vars['doBest']) && $this->vars['doBest']==1) {
				if (CopixConfig::get ('document|easyWorkflow') == 1){
					$toValid->status_doc       = $workflow->getBest($toValid->id_head,'document');
				}else{
					$toValid->status_doc       = $workflow->getNext($toValid->id_head,'document',$toValid->status_doc);
				}
				$toValid->statusdate_doc   = date('Ymd');
				$toValid->statuscomment_doc= '';
			}else{
				//if status is refuse then we change to draft
				if ($toValid->status_doc == $workflow->getRefuse ()) {
					$toValid->status_doc       = $workflow->getDraft ();
					$toValid->statusdate_doc   = date('Ymd');
					$toValid->statuscomment_doc= '';
				}
			}

			//check if there is new doc
			if (is_uploaded_file($_FILES['docFile']['tmp_name'])) {
				$toValid->oldextension  = $toValid->extension_doc;
				$toValid->extension_doc = strrchr($_FILES['docFile']['name'],'.');
				$toValid->extension_doc = substr($toValid->extension_doc, 1);
				if (!(move_uploaded_file ($_FILES['docFile']['tmp_name'], CopixConfig::get ('document|path').$toValid->id_doc.'_v'.$toValid->version_doc.'.'.$toValid->extension_doc))){
					return CopixActionGroup::process ('genericTools|Messages::getError',
					array ('message'=>CopixI18N::get ('document.error.cannotMoveUploadedFile'),
					'back'=>CopixUrl::get ('document|admin|edit')));
				}
			}
			//check if it's a new version or not to insert or update
			if ($toValid->newVersion) {
				$dao->insert ($toValid);
			}else{
				$dao->update ($toValid);
			}
		}else{
			//test if the upload
			if (is_uploaded_file($_FILES['docFile']['tmp_name'])) {
				//getFile extension
				$toValid->author_doc       = $login;
				$toValid->statusauthor_doc = $login;
				$toValid->statusdate_doc   = date('Ymd');
				$toValid->extension_doc    = strrchr($_FILES['docFile']['name'],'.');
				$toValid->extension_doc    = substr($toValid->extension_doc,1) ;
				if (isset($this->vars['doBest']) && $this->vars['doBest']==1) {
					if (CopixConfig::get ('document|easyWorkflow') == 1){
						$toValid->status_doc       = $workflow->getBest($toValid->id_head,'document');
					}else{
						$toValid->status_doc       = $workflow->getNext($toValid->id_head,'document',$toValid->status_doc);
					}
				}else{
					$toValid->status_doc       = $workflow->getDraft ();
				}
				$toValid->version_doc      = 0;
				$toValid->id_doc           = date("YmdHis").rand(0,100);
				$toValid->weight_doc       = (intval($_FILES['docFile']['size'])/1000);

				if ($dao->check ($toValid) !== true){
					$toValid->id_doc = null;
					$this->_setSessionDocument($toValid);
					return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('document|admin|edit', array('e'=>1)));
				}else{
					if (!((move_uploaded_file ($_FILES['docFile']['tmp_name'], CopixConfig::get ('document|path').$toValid->id_doc.'_v0.'.$toValid->extension_doc))&&
					($dao->insert ($toValid)))){
						return CopixActionGroup::process ('genericTools|Messages::getError',
						array ('message'=>CopixI18N::get ('document.error.cannotMoveUploadedFile'),
						'back'=>CopixUrl::get ('document|admin|edit')));
					}
				}
			}else{
				$toValid->errors   = array ();
				$toValid->errors[] = CopixI18N::get('document.error.upload') ;
				$this->_setSessionDocument($toValid);
				return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('document|admin|edit', array('e'=>1)));
			}
		}

		//launch event
		if ($toValid->status_doc == $workflow->getValid ()){
			CopixEventNotifier::notify (new CopixEvent ('DocumentValid',array ('document'=>$toValid)));
		}elseif($toValid->status_doc == $workflow->getPublish ()) {
			CopixEventNotifier::notify (new CopixEvent ('PublishedContent',array ('id'=>$toValid->id_doc,
           'summary'=>$toValid->desc_doc, 'title'=>$toValid->title_doc, 'keywords'=>'', 'url'=>CopixUrl::get ('document|default|download', array ('id_doc'=>$toValid->id_doc)),
           'filename'=>CopixConfig::get ('document|path').$toValid->id_doc.'_v'.$toValid->version_doc.'.'.$toValid->extension_doc, 'isNew'=>($toValid->version_doc == 0))));
		}

		$this->_setSessionDocument (null);
      return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('id_head'=>$toValid->id_head, 'browse'=>'document')));
	}

	/**
    * gets the edit page for the document.
    */
	function getEdit (){
		if (!$toEdit = $this->_getSessionDocument ()){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('document.error.cannotGetDocumentBack'),
			'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'document'))));
		}

		$workflow         = & CopixClassesFactory::getInstanceOf ('copixheadings|workflow');
		$servicesHeadings = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
		$capability       = CopixUserProfile::valueOf ('document', $servicesHeadings->getPath ($toEdit->id_head));

		if ($capability < PROFILE_CCV_WRITE ||
		($capability < PROFILE_CCV_VALID && $toEdit->status_doc == $workflow->getPropose()) ||
		($capability < PROFILE_CCV_PUBLISH && $toEdit->status_doc == $workflow->getValid()) ||
		$toEdit->status_doc == $workflow->getPublish() || $toEdit->status_doc == $workflow->getTrash()) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('document.error.notAnAuthorizedHead'),
			'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'document','id_head'=>$toEdit->id_head))));
		}

		//check if the heading exists. In the mean time, getting its caption
		$dao = & CopixDAOFactory::create ('copixheadings|CopixHeadings');
		if ($heading = $dao->get ($toEdit->id_head)) {
			$toEdit->caption_head = $heading->caption_head;
		} else {
			if ($toEdit->id_head === null){
				$toEdit->caption_head = CopixI18N::get('copixheadings|headings.message.root');
			}else{
				return CopixActionGroup::process ('genericTools|Messages::getError',
				array ('message'=>CopixI18N::get ('copixheadings|admin.error.cannotFindHeading'),
				'back'=>CopixUrl::get ('copixheadings|admin|', array('browse'=>'document','id_head'=>$toEdit->id_head))));
			}
		}

		$tpl = & new CopixTpl ();
		$tpl->assign ('TITLE_PAGE', strlen ($toEdit->id_doc) >= 1 ? CopixI18N::get ('document.titlePage.update') : CopixI18N::get ('document.titlePage.create'));

		$tpl->assign ('MAIN', CopixZone::process ('DocumentEdit', array ('toEdit'=>$toEdit,'e'=>isset ($this->vars['e']))));
		return new CopixActionReturn (CopixactionReturn::DISPLAY, $tpl);
	}

	/**
    * prepare a new document to edit.
    * @param string $this->vars['id_head'] the heading we're gonna put the document in
    */
	function doCreate (){
		// init a new document
		if (!isset ($this->vars['id_head'])){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('document.error.mustSpecifyId'),
			'back'=>CopixUrl::get ('copixheadings|admin|', array('browse'=>'document'))));
		}
		$document  = & CopixDAOFactory::createRecord ('Document');
		$document->id_head = strlen($this->vars['id_head']) > 0 ? $this->vars['id_head'] : null;
		//check if the user has the rights
		$servicesHeadings = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
		if (CopixUserProfile::valueOf ('document', $servicesHeadings->getPath ($document->id_head)) < PROFILE_CCV_WRITE) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('document.error.notAnAuthorizedHead'),
			'back'=>CopixUrl::get ('copixheadings|admin|', array('browse'=>'document','id_head'=>$document->id_head))));
		}
		$toEdit->newVersion = false;
		$this->_setSessionDocument($document);
		return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('document|admin|edit'));
	}

	/**
    * prepare the document to edit.
    * check if we were given the news id to edit, then try to get it.
    */
	function doPrepareEdit (){
		if (!isset ($this->vars['id_doc'])){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('document.error.mustSpecifyId'),
			'back'=>CopixUrl::get ('copixheadings|admin|', array('browse'=>'document'))));
		}

		$dao = & CopixDAOFactory::create ('Document');
		//get last version if no version specify
		$version = isset($this->vars['version']) ? $this->vars['version'] : $dao->getLastVersion ($this->vars['id_doc']);
		if (!$toEdit = $dao->get ($this->vars['id_doc'], $version)){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('document.error.cannotFindDocument'),
			'back'=>CopixUrl::get ('copixheadings|admin|', array('browse'=>'document'))));
		}
		//check if the user has the rights
		$servicesHeadings = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
		if (CopixUserProfile::valueOf ('document', $servicesHeadings->getPath ($toEdit->id_head)) < PROFILE_CCV_WRITE) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('document.error.notAnAuthorizedHead'),
			'back'=>CopixUrl::get ('copixheadings|admin|', array('browse'=>'document','id_head'=>$toEdit->id_head))));
		}

		$workflow  = & CopixClassesFactory::getInstanceOf ('copixheadings|workflow');
		//if doc is publish, then it's a new version
		if ($toEdit->status_doc == $workflow->getPublish ()) {
			$toEdit->newVersion = true;
			$toEdit->status_doc = $workflow->getDraft ();
		}else{
			$toEdit->newVersion = false;
		}

		if (isset($this->vars['back']) && strlen($this->vars['back']) > 0) {
			$toEdit->__Copix_Internal_UrlBack = $this->vars['back'];
		}
		$this->_setSessionDocument($toEdit);
		return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('document|admin|edit'));
	}

	/**
    * Cancel the edition...... empty the session data
    */
	function doCancelEdit (){
		$level=($document = $this->_getSessionDocument()) ? $document->id_head : Null;
		$this->_setSessionDocument(null);
		return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('browse'=>'document','id_head'=>$level)));
	}

	/**
    * validation temporaire des éléments saisis.
    */
	function doValidEdit (){
		$toEdit = $this->_getSessionDocument ();
		$this->_validFromForm  ($toEdit);
		$this->_setSessionDocument ($toEdit);

		return new CopixActionReturn (CopixactionReturn::REDIRECT, '?module=document&action=edit&desc=admin');
	}

	/**
    * updates informations on a single document object from the vars.

    */
	function _validFromForm (& $toUpdate){
		$toCheck = array ('title_doc', 'desc_doc');
		foreach ($toCheck as $elem){
			if (isset ($this->vars[$elem])){
				$toUpdate->$elem = $this->vars[$elem];
			}
		}
	}

	/**
    * gets the current edited document.

    */
	function _getSessionDocument () {
		CopixDAOFactory::fileInclude ('Document');
		return isset ($_SESSION['MODULE_DOCUMENT_EDITED_DOCUMENT']) ? unserialize ($_SESSION['MODULE_DOCUMENT_EDITED_DOCUMENT']) : null;
	}

	/**
    * sets the current edited document.

    */
	function _setSessionDocument ($toSet){
		$_SESSION['MODULE_DOCUMENT_EDITED_DOCUMENT'] = $toSet !== null ? serialize($toSet) : null;
	}

	/**
    * Set document status to publish
    */
	function doStatusPublish (){
		$workflow = & CopixClassesFactory::getInstanceOf ('copixheadings|workflow');

		if (!isset ($this->vars['id_doc'])){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('document.error.mustSpecifyId'),
			'back'=>CopixUrl::get ('copixheadings|admin|', array('browse'=>'document'))));
		}

		$dao       = & CopixDAOFactory::create ('Document');
		$plugAuth  = & CopixController::getInstance()->getPlugin ('auth|auth');
		$user      = & $plugAuth->getUser();
		$version   = $dao->getLastVersion ($this->vars['id_doc']);
		if (!$document = $dao->get ($this->vars['id_doc'], $version)){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('document.error.cannotGetDocumentBack'),
			'back'=>CopixUrl::get ('copixheadings|admin|', array('browse'=>'document'))));
		}
		//check if the user has the rights
		$servicesHeadings = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
		if (CopixUserProfile::valueOf ('document', $servicesHeadings->getPath ($document->id_head)) < PROFILE_CCV_PUBLISH) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('document.error.notAnAuthorizedHead'),
			'back'=>CopixUrl::get ('copixheadings|admin|', array('browse'=>'document','id_head'=>$document->id_head))));
		}
		$document->status_doc       = $workflow->getPublish ();
		$document->statusauthor_doc = $user->login;
		$document->statusdate_doc   = date('Ymd');
		$document->statuscomment_doc= isset($this->vars['statuscomment_doc_'.$document->id_doc]) ? $this->vars['statuscomment_doc_'.$document->id_doc] : null;
		$dao->update ($document);

		//launch event
		CopixEventNotifier::notify (new CopixEvent ('DocumentPublish',array ('document'=>$document)));

		if (isset($this->vars['back']) && strlen($this->vars['back']) > 0) {
			return new CopixActionReturn (CopixactionReturn::REDIRECT, $this->vars['back']);
		}else{
			return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('browse'=>'document', 'id_head'=>$document->id_head)));
		}
	}

	/**
    * Set document status to publish
    */
	function doStatusValid (){
		$workflow = & CopixClassesFactory::getInstanceOf ('copixheadings|workflow');

		if (!isset ($this->vars['id_doc'])){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('document.error.mustSpecifyId'),
			'back'=>CopixUrl::get ('copixheadings|admin|', array('browse'=>'document'))));
		}

		$dao       = & CopixDAOFactory::create ('Document');
		$plugAuth  = & CopixController::instance ()->getPlugin ('auth|auth');
		$user      = & $plugAuth->getUser();
		$version   = $dao->getLastVersion ($this->vars['id_doc']);
		if (!$document = $dao->get ($this->vars['id_doc'], $version)){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('document.error.cannotGetDocumentBack'),
			'back'=>CopixUrl::get ('copixheadings|admin|', array('browse'=>'document'))));
		}
		//check if the user has the rights
		$servicesHeadings = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
		if (CopixUserProfile::valueOf ('document', $servicesHeadings->getPath ($document->id_head)) < PROFILE_CCV_VALID &&
		$document->status_doc == $workflow->getPublish ()) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('document.error.notAnAuthorizedHead'),
			'back'=>CopixUrl::get ('copixheadings|admin|', array('browse'=>'document','id_head'=>$document->id_head))));
		}
		$document->status_doc       = $workflow->getValid ();
		$document->statusauthor_doc = $user->login;
		$document->statusdate_doc   = date('Ymd');
		$document->statuscomment_doc= isset($this->vars['statuscomment_doc_'.$document->id_doc]) ? $this->vars['statuscomment_doc_'.$document->id_doc] : null;
		$dao->update ($document);

		//launch event
		CopixEventNotifier::notify (new CopixEvent ('DocumentValid',array ('document'=>$document)));

		if (isset($this->vars['back']) && strlen($this->vars['back']) > 0) {
			return new CopixActionReturn (CopixactionReturn::REDIRECT, $this->vars['back']);
		}else{
			return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('browse'=>'document', 'id_head'=>$document->id_head)));
		}
	}

	/**
    * Set document status to draft from trash
    */
	function doStatusDraft (){
		$workflow = & CopixClassesFactory::getInstanceOf ('copixheadings|workflow');

		if (!isset ($this->vars['id_doc'])){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('document.error.mustSpecifyId'),
			'back'=>CopixUrl::get ('copixheadings|admin|', array('browse'=>'document'))));
		}

		$dao       = & CopixDAOFactory::create ('Document');
		$plugAuth  = & CopixController::instance ()->getPlugin ('auth|auth');
		$user      = & $plugAuth->getUser();
		$version   = $dao->getLastVersion ($this->vars['id_doc']);
		if (!$document = $dao->get ($this->vars['id_doc'], $version)){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('document.error.cannotGetDocumentBack'),
			'back'=>CopixUrl::get ('copixheadings|admin|', array('browse'=>'document'))));
		}
		//check if the user has the rights
		$servicesHeadings = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
		if (CopixUserProfile::valueOf ('document', $servicesHeadings->getPath ($document->id_head)) < PROFILE_CCV_VALID &&
		$document->status_doc != $workflow->getTrash () && $document->statusauthor_doc != $user->login) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('document.error.notAnAuthorizedHead'),
			'back'=>CopixUrl::get ('copixheadings|admin|', array('browse'=>'document','id_head'=>$document->id_head))));
		}
		$document->status_doc       = $workflow->getDraft ();
		$document->statusauthor_doc = $user->login;
		$document->statusdate_doc   = date('Ymd');
		$document->statuscomment_doc= isset($this->vars['statuscomment_doc_'.$document->id_doc]) ? $this->vars['statuscomment_doc_'.$document->id_doc] : null;
		$dao->update ($document);

		if (isset($this->vars['back']) && strlen($this->vars['back']) > 0) {
			return new CopixActionReturn (CopixactionReturn::REDIRECT, $this->vars['back']);
		}else{
			return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('browse'=>'document', 'id_head'=>$document->id_head)));
		}
	}

	/**
    * Set document status to publish
    */
	function doStatusRefuse (){
		$workflow = & CopixClassesFactory::getInstanceOf ('copixheadings|workflow');

		if (!isset ($this->vars['id_doc'])){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('document.error.mustSpecifyId'),
			'back'=>CopixUrl::get ('copixheadings|admin|', array('browse'=>'document'))));
		}

		$dao       = & CopixDAOFactory::create ('Document');
		$plugAuth  = & CopixController::instance ()->getPlugin ('auth|auth');
		$user      = & $plugAuth->getUser();
		$version   = $dao->getLastVersion ($this->vars['id_doc']);
		if (!$document = $dao->get ($this->vars['id_doc'], $version)){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('document.error.cannotGetDocumentBack'),
			'back'=>CopixUrl::get ('copixheadings|admin|', array('browse'=>'document'))));
		}

		//check if the user has the rights
		$servicesHeadings = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
		$capability       = CopixUserProfile::valueOf ('document', $servicesHeadings->getPath ($document->id_head));
		if (($capability < PROFILE_CCV_VALID && $document->status_doc == $workflow->getPropose()) ||
		($capability < PROFILE_CCV_PUBLISH && $document->status_doc == $workflow->getValid()) ||
		$document->status_doc == $workflow->getPublish()) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('document.error.notAnAuthorizedHead'),
			'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'document','id_head'=>$document->id_head))));
		}

		$document->status_doc       = $workflow->getRefuse ();
		$document->statusauthor_doc = $user->login;
		$document->statusdate_doc   = date('Ymd');
		$document->statuscomment_doc= isset($this->vars['statuscomment_doc_'.$document->id_doc]) ? $this->vars['statuscomment_doc_'.$document->id_doc] : null;
		$dao->update ($document);

		//launch event
		CopixEventNotifier::notify (new CopixEvent ('DocumentRefuse',array ('document'=>$document)));

		if (isset($this->vars['back']) && strlen($this->vars['back']) > 0) {
			return new CopixActionReturn (CopixactionReturn::REDIRECT, $this->vars['back']);
		}else{
			return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('browse'=>'document', 'id_head'=>$document->id_head)));
		}
	}

	/**
    * Set document status to propose
    */
	function doStatusPropose (){
		$workflow = & CopixClassesFactory::getInstanceOf ('copixheadings|workflow');

		if (!isset ($this->vars['id_doc'])){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('document.error.mustSpecifyId'),
			'back'=>CopixUrl::get ('copixheadings|admin|', array('browse'=>'document'))));
		}

		$dao       = & CopixDAOFactory::create ('Document');
		$plugAuth  = & CopixController::instance ()->getPlugin ('auth|auth');
		$user      = & $plugAuth->getUser();
		$version   = $dao->getLastVersion ($this->vars['id_doc']);
		if (!$document = $dao->get ($this->vars['id_doc'], $version)){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('document.error.cannotGetDocumentBack'),
			'back'=>CopixUrl::get ('copixheadings|admin|', array('browse'=>'document'))));
		}

		//check if the user has the rights
		$servicesHeadings = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
		$capability       = CopixUserProfile::valueOf ('document', $servicesHeadings->getPath ($document->id_head));

		if ($capability < PROFILE_CCV_WRITE &&
		$document->status_doc == $workflow->getPublish ()) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('document.error.notAnAuthorizedHead'),
			'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'document','id_head'=>$document->id_head))));
		}
		$document->status_doc       = $workflow->getPropose ();
		//easy workflow ?
		if (CopixConfig::get ('document|easyWorkflow') == 1){
			$document->status_doc = $workflow->getBest ($document->id_head, 'document');
		}

		//launch event
		if ($document->status_doc == $workflow->getPublish ()) {
			CopixEventNotifier::notify (new CopixEvent ('DocumentPublish',array ('document'=>$document)));
		}elseif($document->status_doc == $workflow->getValid ()) {
			CopixEventNotifier::notify (new CopixEvent ('DocumentValid',array ('document'=>$document)));
		}else{
			CopixEventNotifier::notify (new CopixEvent ('DocumentPropose',array ('document'=>$document)));
		}

		$document->statusauthor_doc = $user->login;
		$document->statusdate_doc   = date('Ymd');
		$document->statuscomment_doc= isset($this->vars['statuscomment_doc_'.$document->id_doc]) ? $this->vars['statuscomment_doc_'.$document->id_doc] : null;
		$dao->update ($document);

		if (isset($this->vars['back']) && strlen($this->vars['back']) > 0) {
			return new CopixActionReturn (CopixactionReturn::REDIRECT, $this->vars['back']);
		}else{
			return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('browse'=>'document', 'id_head'=>$document->id_head)));
		}
	}

	/**
    * Set document status to publish
    */
	function doStatusTrash (){
		$workflow = & CopixClassesFactory::getInstanceOf ('copixheadings|workflow');

		if (!isset ($this->vars['id_doc'])){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('document.error.mustSpecifyId'),
			'back'=>CopixUrl::get ('copixheadings|admin|', array('browse'=>'document'))));
		}

		$dao       = & CopixDAOFactory::create ('Document');
		$plugAuth  = & CopixController::instance ()->getPlugin ('auth|auth');
		$user      = & $plugAuth->getUser();
		$version   = $dao->getLastVersion ($this->vars['id_doc']);
		if (!$document = $dao->get ($this->vars['id_doc'], $version)){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('document.error.cannotGetDocumentBack'),
			'back'=>CopixUrl::get ('copixheadings|admin|', array('browse'=>'document'))));
		}
		//check if the user has the rights
		$servicesHeadings = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
		$capability       = CopixUserProfile::valueOf ('document', $servicesHeadings->getPath ($document->id_head));

		if ($capability < PROFILE_CCV_WRITE ||
		($capability < PROFILE_CCV_VALID && $document->status_doc == $workflow->getPropose()) ||
		($capability < PROFILE_CCV_PUBLISH && $document->status_doc == $workflow->getValid()) ||
		$document->status_doc == $workflow->getPublish()) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('document.error.notAnAuthorizedHead'),
			'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'document','id_head'=>$document->id_head))));
		}

		$document->status_doc       = $workflow->getTrash ();
		$document->statusauthor_doc = $user->login;
		$document->statusdate_doc   = date('Ymd');
		$document->statuscomment_doc= isset($this->vars['statuscomment_doc_'.$document->id_doc]) ? $this->vars['statuscomment_doc_'.$document->id_doc] : null;
		$dao->update ($document);

		if (isset($this->vars['back']) && strlen($this->vars['back']) > 0) {
			return new CopixActionReturn (CopixactionReturn::REDIRECT, $this->vars['back']);
		}else{
			return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('browse'=>'document', 'id_head'=>$document->id_head)));
		}
	}

	/**
    * Delete a document
    */
	function doDelete (){
		if (! isset ($this->vars['id_doc'])){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('document.error.mustSpecifyId'),
			'back'=>CopixUrl::get ('copixheadings|admin|', array('browse'=>'document'))));
		}

		$dao = & CopixDAOFactory::create ('Document');
		$version   = $dao->getLastVersion ($this->vars['id_doc']);
		if (!$document = $dao->get ($this->vars['id_doc'], $version)){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('document.error.cannotGetDocumentBack'),
			'back'=>CopixUrl::get ('copixheadings|admin|', array('browse'=>'document'))));
		}

		//check if the user has the rights
		$servicesHeadings = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
		$capability       = CopixUserProfile::valueOf ('document', $servicesHeadings->getPath ($document->id_head));
		$workflow         = & CopixClassesFactory::getInstanceOf ('copixheadings|workflow');
		if ($capability < PROFILE_CCV_WRITE ||
		($capability < PROFILE_CCV_PUBLISH && $document->status_doc == $workflow->getPublish())) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('document.error.notAnAuthorizedHead'),
			'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'document','id_head'=>$document->id_head))));
		}

		//Check if we have the confirmation infomation, if not, asks the user if he really wants to delete the given news.
		if ($document->status_doc != $workflow->getTrash() && !isset ($this->vars['confirm'])){
			return CopixActionGroup::process ('genericTools|messages::getConfirm',
			array ('confirm'=>CopixUrl::get ('document|admin|delete',array('browse'=>'document','id_doc'=>$document->id_doc, 'id_head'=>$document->id_head,'confirm'=>1)),
			'cancel'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'document','id_head'=>$document->id_head)),
			'message'=>CopixI18N::get ('document.messages.confirmDelete', $document->title_doc))
			);
		}

		//launch event
		CopixEventNotifier::notify (new CopixEvent ('DeletedContent',array ('id'=>$document->id_doc, 'type'=>'document')));

		$sp  = & CopixDAOFactory::createSearchParams ();
		$sp->addCondition ('id_doc', '=', $this->vars['id_doc']);
		$arToDelete = $dao->findBy ($sp);
		foreach ($arToDelete as $document){
			unlink(CopixConfig::get ('document|path').$document->id_doc.'_v'.$document->version_doc.'.'.$document->extension_doc);
		}
		$dao->deleteById ($document->id_doc);

		return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('browse'=>'document','id_head'=>$document->id_head)));
	}

	/**
   * Affichage de l'écran de sélection d'un document
   */
	function getSelectDocument (){
		$tpl = & new CopixTpl ();
		$tpl->assign ('TITLE_PAGE', CopixI18N::get ('document.titlePage.onlineDocument'));
		$tpl->assign ('MAIN',CopixZone::process ('SelectDocument', array ('back'=>CopixRequest::get ('back'), 'select'=>CopixRequest::get ('select'), 'editorName'=>CopixRequest::get ('editorName'))));

		if (isset($this->vars['popup'])){
			return new CopixActionReturn (CopixactionReturn::DISPLAY_IN, $tpl, '|blank.tpl');
		}
		return new CopixActionReturn (CopixactionReturn::DISPLAY, $tpl);
	}
}
?>
