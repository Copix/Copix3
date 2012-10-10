<?php
/**
* @package	cms
* @subpackage news
* @author	Bertrand Yan, Croes Gérald
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
/**
* @package	cms
* @subpackage news
* handle the administration of the news
*/
class ActionGroupNewsAdmin extends CopixActionGroup {
	/**
    * accès à la page de selection d'une image
    */
	function getSelectPicture (){

	    if (!$toEdit = $this->_getSessionNews ()){
	         return CopixActionGroup::process ('genericTools|Messages::getError',
	         array ('message'=>CopixI18N::get ('news.unableToGetEdited'),
	         'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'news'))));
	    }

		$this->_validFromForm($toEdit);
		$this->_setSessionNews($toEdit);

		return CopixActionGroup::process ('pictures|Browser::getBrowser',
		array ('select'=>CopixUrl::get ('news|admin|editPicture'),
		'back'=>CopixUrl::get ('news|admin|edit')));

	}
	/**
    *modification de l'image
    */
	function getEditPicture (){
		//Vérification des données à éditer.
		if (! ($toEdit = $this->_getSessionNews())){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get('cms_portlet_picture.unable.to.get'),
			'back'=>CopixUrl::get ('cms|admin|edit')));
		}

		//récup° de la photo
		if ( isset($this->vars['id']) ){
			$toEdit->id_pict = $this->vars['id'];
			$this->_setSessionNews($toEdit);
		}

		//retour sur la zone editnews
		$tpl = & new CopixTpl ();
		$tpl->assign ('MAIN', CopixZone::process ('NewsEdit', array ('toEdit'=>$toEdit)));
		return new CopixActionReturn (CopixactionReturn::DISPLAY, $tpl);
	}
/**
    * Delete a picture News
    */
	function doDeletePictureNews (){
		if (!isset ($this->vars['id_news'])){
			return $this->_missingParameters();
		}

	    if (!$toEdit = $this->_getSessionNews ()){
	         return CopixActionGroup::process ('genericTools|Messages::getError',
	         array ('message'=>CopixI18N::get ('news.unableToGetEdited'),
	         'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'news'))));
	    }

	    $this->_validFromForm($toEdit);
		$this->_setSessionNews($toEdit);

		$toEdit->id_pict = null;
		$this->_setSessionNews($toEdit);

		//retour sur la zone editnews
		$tpl = & new CopixTpl ();
		$tpl->assign ('MAIN', CopixZone::process ('NewsEdit', array ('toEdit'=>$toEdit)));
		return new CopixActionReturn (CopixactionReturn::DISPLAY, $tpl);

	}


   /**
    * says if we can paste the cutted element (if any) in the given heading (id)
    * @param int this->vars['level'] the heading where we wants to paste the cutted element into
    * @return bool
    */
    function canPaste (){
        if (!$this->_hasCut()){
            return false;
        }

        $dao = & CopixDAOFactory::getInstanceOf ('news');

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
        'news') < PROFILE_CCV_WRITE) {
            return false;
        }
        //do we have write permissions on the cutted element ?
        if (CopixUserProfile::valueOf ($headingProfileServices->getPath($toPaste->id_head),
        'news') < PROFILE_CCV_PUBLISH) {
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
            array ('message'=>CopixI18N::get ('news.error.cannotFindCut'),
            'back'=>CopixUrl::get ('copixheadings|admin|', array ('browse'=>'news', 'level'=>$this->vars['level']))));
        }

        $dao = & CopixDAOFactory::getInstanceOf ('news');
        if (!$toPaste = $dao->get ($this->_getCut())){
            $this->_clearCut ();
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('news.unableToFind'),
            'back'=>CopixUrl::get ('copixheadings|admin|', array('browse'=>'news', 'level'=>$this->vars['level']))));
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
        if (CopixUserProfile::valueOf ($headingProfileServices->getPath($this->vars['level']),'news') < PROFILE_CCV_WRITE) {
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('news.error.cannotPasteHere')));
        }

        //do we have write permissions on the cutted element ?
        if (CopixUserProfile::valueOf ($headingProfileServices->getPath($toPaste->id_head),'news') < PROFILE_CCV_PUBLISH) {
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('news.error.cannotMoveElement')));
        }

        $dao->moveHeading ($toPaste->id_news, $this->vars['level']);
        $this->_clearCut ();
        return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array ('browse'=>'news', 'level'=>$this->vars['level'])));
    }

   /**
    * cuts a document
    * We have to have the rights to write in the given heading the cutted element belongs to to be able to do so.
    * @param int id_doc the document we wants to cut
    */
    function doCut () {
        //No given element.
        if (! isset ($this->vars['id_news'])) {
            return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin', array('browse'=>'news')));
        }

        $dao = & CopixDAOFactory::getInstanceOf ('news');
        if (!$toCut = $dao->get ($this->vars['id_news'])){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('news.unableToFind'),
            'back'=>CopixUrl::get ('copixheadings|admin|', array('browse'=>'news'))));
        }
        //check if the user has the rights
        $servicesHeadings = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
        if (CopixUserProfile::valueOf ($servicesHeadings->getPath ($toCut->id_head), 'news') < PROFILE_CCV_WRITE) {
           return CopixActionGroup::process ('genericTools|Messages::getError',
           array ('message'=>CopixI18N::get ('news.error.notAnAuthorizedHead'),
           'back'=>CopixUrl::get ('copixheadings|admin|', array('browse'=>'news','level'=>$toCut->id_head))));
        }

        //ok, we can cut the element
        $this->_setCut ($this->vars['id_news']);
        return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array ('browse'=>'news', 'level'=>$toCut->id_head)));
    }

   /**
    * cuts the element
    */
    function _setCut ($id_forms) {
        $_SESSION['MODULE_NEWS_CUT'] = $id_forms;
    }

    /**
    * gets the cutted heading
    */
    function _getCut () {
        if (isset ($_SESSION['MODULE_NEWS_CUT'])){
            return $_SESSION['MODULE_NEWS_CUT'];
        }else{
            return null;
        }
    }

    /**
    * says if there's a cutted element
    */
    function _hasCut (){
        return isset ($_SESSION['MODULE_NEWS_CUT']);
    }

    /**
    * clear the pseudo clipboard
    */
    function _clearCut (){
        session_unregister('MODULE_NEWS_CUT');
    }

   /**
    * prepare a new news to edit.
    */
   function doCreate (){
      if (!isset($this->vars['id_head'])){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('news.error.missingParameters'),
            'back'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'news'))));
      }

      //check if the user has the rights to write pages into the given heading.
      $servicesHeadings = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
      if (CopixUserProfile::valueOf ($servicesHeadings->getPath ($this->vars['id_head']), 'news') < PROFILE_CCV_WRITE) {
         return CopixActionGroup::process ('genericTools|Messages::getError',
         array ('message'=>CopixI18N::get ('news.error.notAnAuthorizedHead'),
         'back'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'news', 'level'=>$this->vars['id_head']))));
      }
      // init a new news
      $news                   = & CopixDAOFactory::createRecord ('News');
      $news->id_head          = strlen($this->vars['id_head'] > 0) ? $this->vars['id_head'] : null;
      $news->editionkind_news = CopixConfig::get ('news|editionKind');
      $news->datewished_news = date ('Ymd');

      $this->_setSessionNews($news);
      return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('news|admin|edit'));
   }

   /**
   * prepare the news to edit.
   * check if we were given the news id to edit, then try to get it.
   */
   function doPrepareEdit (){
      if (!isset ($this->vars['id_news'])){
         return CopixActionGroup::process ('genericTools|Messages::getError',
               array ('message'=>CopixI18N::get ('news.error.notAnAuthorizedHead'),
               'back'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'news'))));
      }

      $dao = & CopixDAOFactory::getInstanceOf ('news');
      if (!$toEdit = $dao->get ($this->vars['id_news'])){
          return CopixActionGroup::process ('genericTools|Messages::getError',
          array ('message'=>CopixI18N::get ('news.unableToFind'),
          'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'news'))));
      }

      if (isset($this->vars['back']) && strlen($this->vars['back']) > 0) {
            $toEdit->__Copix_Internal_UrlBack = $this->vars['back'];
      }

      $this->_setSessionNews($toEdit);
      return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('news|admin|edit'));
   }

   /**
    * gets the edit page for the news.
    */
   function getEdit (){
      if (!$toEdit = $this->_getSessionNews ()){
         return CopixActionGroup::process ('genericTools|Messages::getError',
          array ('message'=>CopixI18N::get ('news.unableToGetEdited'),
          'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'news'))));
      }

      $workflow         = & CopixClassesFactory::getInstanceOf ('copixheadings|workflow');
      $servicesHeadings = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
      $capability       = CopixUserProfile::valueOf ($servicesHeadings->getPath ($toEdit->id_head), 'news');

      if ($capability < PROFILE_CCV_WRITE ||
         ($capability < PROFILE_CCV_VALID && $toEdit->status_news == $workflow->getPropose()) ||
         ($capability < PROFILE_CCV_PUBLISH && $toEdit->status_news == $workflow->getValid()) ||
         ($capability < PROFILE_CCV_PUBLISH && $toEdit->status_news == $workflow->getPublish()) ||
          $toEdit->status_news == $workflow->getTrash()) {
         return CopixActionGroup::process ('genericTools|Messages::getError',
         array ('message'=>CopixI18N::get ('news.error.notAnAuthorizedHead'),
         'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'news','level'=>$toEdit->id_head))));
      }

      // get the current copixheadings caption
      $dao = & CopixDAOFactory::getInstanceOf ('copixheadings|CopixHeadings');
      if ($heading = $dao->get( $toEdit->id_head )) {
         $toEdit->caption_head = $heading->caption_head;
      } else {
         $toEdit->caption_head = CopixI18N::get('copixheadings|headings.message.root');
      }

      $tpl = & new CopixTpl ();
      $tpl->assign ('TITLE_PAGE', strlen ($toEdit->id_news) >= 1 ? CopixI18N::get ('news.title.update') : CopixI18N::get ('news.title.create'));
      $tpl->assign ('MAIN', CopixZone::process ('NewsEdit', array ('toEdit'=>$toEdit,'lastpage'=>CopixRequest::get ('lastpage', null, true), 'e'=>isset ($this->vars['e']), 'kind'=>CopixRequest::get ('kind', 0, true))));
      return new CopixActionReturn (CopixactionReturn::DISPLAY, $tpl);
   }

   /**
    * apply updates on the edited news.
    * save to datebase if ok.
    */
   function doValid (){

      if (!$toValid = $this->_getSessionNews ()){
         return CopixActionGroup::process ('genericTools|Messages::getError',
          array ('message'=>CopixI18N::get ('news.unableToGetEdited'),
          'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'news'))));
      }
      //print_r($toValid);


      //check if the user has the rights to write pages into the given heading.
      $workflow         = & CopixClassesFactory::getInstanceOf ('copixheadings|workflow');
      $servicesHeadings = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
      $capability       = CopixUserProfile::valueOf ($servicesHeadings->getPath ($toValid->id_head), 'pictures');

      if ($capability < PROFILE_CCV_WRITE ||
         ($capability < PROFILE_CCV_VALID && $toValid->status_news == $workflow->getPropose()) ||
         ($capability < PROFILE_CCV_PUBLISH && $toValid->status_news == $workflow->getValid())) {
         return CopixActionGroup::process ('genericTools|Messages::getError',
         array ('message'=>CopixI18N::get ('news.error.notAnAuthorizedHead'),
         'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'news','level'=>$toValid->id_head))));
      }

      //update form
      $this->_validFromForm($toValid);

      $plugAuth            = & CopixController::instance ()->getPlugin ('auth|auth');
      $user                = & $plugAuth->getUser();
      $login               = $user->login;
      $dao                 = & CopixDAOFactory::getInstanceOf ('News');

      if ($toValid->id_news !== null){
         if (isset($this->vars['doBest']) && $this->vars['doBest']==1) {
            if (CopixConfig::get ('news|easyWorkflow') == 1){ // easyWFL activated
               $toValid->status_news       = $workflow->getBest($toValid->id_head,'news');
            }else{
               $toValid->status_news       = $workflow->getNext($toValid->id_head,'news',$toValid->status_news);
            }
            $toValid->statusdate_news   = date('Ymd');
            $toValid->statuscomment_news= '';
         }else{
            //if status is refuse then we change to draft
            if ($toValid->status_news == $workflow->getRefuse ()) {
                $toValid->status_news       = $workflow->getDraft ();
                $toValid->statusdate_news   = date('Ymd');
                $toValid->statuscomment_news= '';
            }
         }
         // Check form values
         if ($dao->check ($toValid) !== true) {
            $this->_setSessionNews($toValid);
            return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('news|admin|edit', array('e'=>'1')));
         }
         $dao->update ($toValid);
      }else{
         $toValid->author_news       = $login;
         $toValid->statusauthor_news = $login;
         $toValid->statusdate_news   = date('Ymd');
         if (isset($this->vars['doBest']) && $this->vars['doBest']==1) {
            if (CopixConfig::get ('news|easyWorkflow') == 1){ // easyWFL activated
               $toValid->status_news       = $workflow->getBest($toValid->id_head,'news');
            }else{
               $toValid->status_news       = $workflow->getNext($toValid->id_head,'news',$toValid->status_news);
            }
         }else{
            $toValid->status_news       = $workflow->getDraft();
         }
         // Check form values
         if ($dao->check($toValid) !== true) {
            $this->_setSessionNews($toValid);
            return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('news|admin|edit', array('e'=>'1')));
         }
         $dao->insert ($toValid);
      }

      if (isset($toValid->__Copix_Internal_UrlBack) && strlen($toValid->__Copix_Internal_UrlBack) > 0) {
         return new CopixActionReturn (CopixactionReturn::REDIRECT,$toValid->__Copix_Internal_UrlBack);
      }else{
         return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|',array('browse'=>'news','level'=>$toValid->id_head)));
      }
   }

   /**
    * Cancel the edition...... empty the session data
    */
   function doCancelEdit (){
      $news = $this->_getSessionNews();
      $id_head=$news->id_head;
      $this->_setSessionNews(null);
      return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|',array('browse'=>'news','level'=>$id_head)));
   }

   //- STATUS HANDLER -----------------------------------------------------------------
   /**
   * Set online a picture
   */
   function doStatusPublish () {
      $workflow = & CopixClassesFactory::getInstanceOf ('copixheadings|workflow');

      if (!isset ($this->vars['id_news'])){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('news.error.missingParameters'),
            'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'news'))));
      }

      $dao = & CopixDAOFactory::getInstanceOf ('news');
      if (!$toEdit = $dao->get ($this->vars['id_news'])){
          return CopixActionGroup::process ('genericTools|Messages::getError',
          array ('message'=>CopixI18N::get ('news.unableToFind'),
          'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'news'))));
      }

      //check if the user has the rights
      $servicesHeadings = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
      if (CopixUserProfile::valueOf ($servicesHeadings->getPath ($toEdit->id_head), 'news') < PROFILE_CCV_PUBLISH) {
         return CopixActionGroup::process ('genericTools|Messages::getError',
         array ('message'=>CopixI18N::get ('news.error.notAnAuthorizedHead'),
         'back'=>CopixUrl::get ('copixheadings|admin|', array('browse'=>'news','level'=>$toEdit->id_head))));
      }

      $plugAuth  = & CopixController::instance ()->getPlugin ('auth|auth');
      $user      = & $plugAuth->getUser();
      $toEdit->status_news       = $workflow->getPublish ();
      $toEdit->statusauthor_news = $user->login;
      $toEdit->statusdate_news   = date('Ymd');
      $toEdit->statuscomment_news= isset($this->vars['statuscomment_news_'.$toEdit->id_news]) ? $this->vars['statuscomment_news_'.$toEdit->id_news] : null;
      $dao->update($toEdit);

      if (isset($this->vars['back']) && strlen($this->vars['back']) > 0) {
         return new CopixActionReturn (CopixactionReturn::REDIRECT, $this->vars['back']);
      }else{
         return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('browse'=>'news', 'level'=>$toEdit->id_head)));
      }
   }

    /**
    * Set picture status to valid
    */
    function doStatusValid (){
        $workflow = & CopixClassesFactory::getInstanceOf ('copixheadings|workflow');

        if (!isset ($this->vars['id_news'])){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('news.error.missingParameters'),
            'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'news'))));
        }

        $dao = & CopixDAOFactory::getInstanceOf ('news');
        if (!$toEdit = $dao->get ($this->vars['id_news'])){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('news.unableToFind'),
            'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'news'))));
        }

        $plugAuth  = & CopixController::instance ()->getPlugin ('auth|auth');
        $user      = & $plugAuth->getUser();
        //check if the user has the rights
        $servicesHeadings = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
        if (CopixUserProfile::valueOf ($servicesHeadings->getPath ($toEdit->id_head), 'news') < PROFILE_CCV_VALID &&
            $toEdit->status_news == $workflow->getPublish ()) {
           return CopixActionGroup::process ('genericTools|Messages::getError',
           array ('message'=>CopixI18N::get ('news.error.notAnAuthorizedHead'),
           'back'=>CopixUrl::get ('copixheadings|admin|', array('browse'=>'news','level'=>$toEdit->id_head))));
        }
        $toEdit->status_news       = $workflow->getValid ();
        $toEdit->statusauthor_news = $user->login;
        $toEdit->statusdate_news   = date('Ymd');
        $toEdit->statuscomment_news= isset($this->vars['statuscomment_news_'.$toEdit->id_news]) ? $this->vars['statuscomment_news_'.$toEdit->id_news] : null;
        $dao->update($toEdit);

        //launch event
        CopixEventNotifier::notify (new CopixEvent ('NewsValid',array ('news'=>$toEdit)));


        if (isset($this->vars['back']) && strlen($this->vars['back']) > 0) {
         return new CopixActionReturn (CopixactionReturn::REDIRECT, $this->vars['back']);
      }else{
         return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('browse'=>'news', 'level'=>$toEdit->id_head)));
      }
    }

     /**
    * Set picture status to draft from trash
    */
    function doStatusDraft (){
        $workflow = & CopixClassesFactory::getInstanceOf ('copixheadings|workflow');

        if (!isset ($this->vars['id_news'])){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('news.error.missingParameters'),
            'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'news'))));
        }

        $dao = & CopixDAOFactory::getInstanceOf ('news');
        if (!$toEdit = $dao->get ($this->vars['id_news'])){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('news.unableToFind'),
            'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'news'))));
        }

        $plugAuth  = & CopixController::instance ()->getPlugin ('auth|auth');
        $user      = & $plugAuth->getUser();
        //check if the user has the rights
        $servicesHeadings = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
        if (CopixUserProfile::valueOf ($servicesHeadings->getPath ($toEdit->id_head), 'news') < PROFILE_CCV_VALID &&
            $toEdit->status_news != $workflow->getTrash () && $toEdit->statusauthor_news != $user->login) {
           return CopixActionGroup::process ('genericTools|Messages::getError',
           array ('message'=>CopixI18N::get ('news.error.notAnAuthorizedHead'),
           'back'=>CopixUrl::get ('copixheadings|admin|', array('browse'=>'news','level'=>$toEdit->id_head))));
        }
        $toEdit->status_news       = $workflow->getDraft ();
        $toEdit->statusauthor_news = $user->login;
        $toEdit->statusdate_news   = date('Ymd');
        $toEdit->statuscomment_news= isset($this->vars['statuscomment_news_'.$toEdit->id_news]) ? $this->vars['statuscomment_news_'.$toEdit->id_news] : null;
        $dao->update($toEdit);

        if (isset($this->vars['back']) && strlen($this->vars['back']) > 0) {
         return new CopixActionReturn (CopixactionReturn::REDIRECT, $this->vars['back']);
      }else{
         return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('browse'=>'news', 'level'=>$toEdit->id_head)));
      }
    }

     /**
    * Set picture status to refuse
    */
    function doStatusRefuse (){
        $workflow = & CopixClassesFactory::getInstanceOf ('copixheadings|workflow');

        if (!isset ($this->vars['id_news'])){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('news.error.missingParameters'),
            'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'news'))));
        }

        $dao = & CopixDAOFactory::getInstanceOf ('news');
        if (!$toEdit = $dao->get ($this->vars['id_news'])){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('news.unableToFind'),
            'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'news'))));
        }

        $plugAuth  = & CopixController::instance ()->getPlugin ('auth|auth');
        $user      = & $plugAuth->getUser();

        //check if the user has the rights
        $servicesHeadings = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
        $capability       = CopixUserProfile::valueOf ($servicesHeadings->getPath ($toEdit->id_head), 'news');
        if (($capability < PROFILE_CCV_VALID && $toEdit->status_news == $workflow->getPropose()) ||
           ($capability < PROFILE_CCV_PUBLISH && $toEdit->status_news == $workflow->getValid()) ||
           $toEdit->status_news == $workflow->getPublish()) {
           return CopixActionGroup::process ('genericTools|Messages::getError',
           array ('message'=>CopixI18N::get ('news.error.notAnAuthorizedHead'),
           'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'news','level'=>$toEdit->id_head))));
        }

        $toEdit->status_news       = $workflow->getRefuse ();
        $toEdit->statusauthor_news = $user->login;
        $toEdit->statusdate_news   = date('Ymd');
        $toEdit->statuscomment_news= isset($this->vars['statuscomment_news_'.$toEdit->id_news]) ? $this->vars['statuscomment_news_'.$toEdit->id_news] : null;
        $dao->update($toEdit);

        //launch event
        CopixEventNotifier::notify (new CopixEvent ('NewsRefuse',array ('news'=>$toEdit)));

        if (isset($this->vars['back']) && strlen($this->vars['back']) > 0) {
         return new CopixActionReturn (CopixactionReturn::REDIRECT, $this->vars['back']);
      }else{
         return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('browse'=>'news', 'level'=>$toEdit->id_head)));
      }
    }

    /**
    * Set picture status to propose
    */
    function doStatusPropose (){
        $workflow = & CopixClassesFactory::getInstanceOf ('copixheadings|workflow');

        if (!isset ($this->vars['id_news'])){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('news.error.missingParameters'),
            'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'news'))));
        }

        $dao = & CopixDAOFactory::getInstanceOf ('news');
        if (!$toEdit = $dao->get ($this->vars['id_news'])){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('news.unableToFind'),
            'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'news'))));
        }

        $plugAuth  = & CopixController::instance ()->getPlugin ('auth|auth');
        $user      = & $plugAuth->getUser();

        //check if the user has the rights
        $servicesHeadings = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
        $capability       = CopixUserProfile::valueOf ($servicesHeadings->getPath ($toEdit->id_head), 'news');

        if ($capability < PROFILE_CCV_WRITE &&
            $toEdit->status_news == $workflow->getPublish ()) {
           return CopixActionGroup::process ('genericTools|Messages::getError',
           array ('message'=>CopixI18N::get ('news.error.notAnAuthorizedHead'),
           'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'news','level'=>$toEdit->id_head))));
        }

        $toEdit->status_news       = $workflow->getPropose ();
        //récuperation de l'etat proposer/créer dans le worflow
        if (CopixConfig::get ('news|easyWorkflow') == 1){
            $toEdit->status_news = $workflow->getBest ($toEdit->id_head, 'news');
        }
        $toEdit->statusauthor_news = $user->login;
        $toEdit->statusdate_news   = date('Ymd');
        $toEdit->statuscomment_news= isset($this->vars['statuscomment_news_'.$toEdit->id_news]) ? $this->vars['statuscomment_news_'.$toEdit->id_news] : null;
        $dao->update($toEdit);

        //launch event
        if ($toEdit->status_news == $workflow->getPublish ()) {
        }elseif($toEdit->status_news == $workflow->getValid ()) {
            CopixEventNotifier::notify (new CopixEvent ('NewsValid',array ('news'=>$toEdit)));
        }else{
            CopixEventNotifier::notify (new CopixEvent ('NewsPropose',array ('news'=>$toEdit)));
        }

        if (isset($this->vars['back']) && strlen($this->vars['back']) > 0) {
         return new CopixActionReturn (CopixactionReturn::REDIRECT, $this->vars['back']);
      }else{
         return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('browse'=>'news', 'level'=>$toEdit->id_head)));
      }
    }

     /**
    * Set picture status to trash
    */
    function doStatusTrash (){
        $workflow = & CopixClassesFactory::getInstanceOf ('copixheadings|workflow');

        if (!isset ($this->vars['id_news'])){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('news.error.missingParameters'),
            'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'news'))));
        }

        $dao = & CopixDAOFactory::getInstanceOf ('news');
        if (!$toEdit = $dao->get ($this->vars['id_news'])){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('news.unableToFind'),
            'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'news'))));
        }

        $plugAuth  = & CopixController::instance ()->getPlugin ('auth|auth');
        $user      = & $plugAuth->getUser();

        //check if the user has the rights
        $servicesHeadings = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
        $capability       = CopixUserProfile::valueOf ($servicesHeadings->getPath ($toEdit->id_head), 'news');

        if ($capability < PROFILE_CCV_WRITE ||
           ($capability < PROFILE_CCV_VALID && $toEdit->status_news == $workflow->getPropose()) ||
           ($capability < PROFILE_CCV_PUBLISH && $toEdit->status_news == $workflow->getValid()) ||
           $toEdit->status_news == $workflow->getPublish()) {
           return CopixActionGroup::process ('genericTools|Messages::getError',
           array ('message'=>CopixI18N::get ('news.error.notAnAuthorizedHead'),
           'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'news','level'=>$toEdit->id_head))));
        }

        $toEdit->status_news       = $workflow->getTrash ();
        $toEdit->statusauthor_news = $user->login;
        $toEdit->statusdate_news   = date('Ymd');
        $toEdit->statuscomment_news= isset($this->vars['statuscomment_news_'.$toEdit->id_news]) ? $this->vars['statuscomment_news_'.$toEdit->id_news] : null;
        $dao->update($toEdit);

        if (isset($this->vars['back']) && strlen($this->vars['back']) > 0) {
         return new CopixActionReturn (CopixactionReturn::REDIRECT, $this->vars['back']);
      }else{
         return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('browse'=>'news', 'level'=>$toEdit->id_head)));
      }
    }


   // End of STATUS HANDLER -----------------------------------------------------------------

   function doDelete() {
      if (!isset ($this->vars['id_news'])){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('news.error.missingParameters'),
            'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'news'))));
      }

      $dao = & CopixDAOFactory::getInstanceOf ('news');
      if (!$toDelete = $dao->get ($this->vars['id_news'])){
          return CopixActionGroup::process ('genericTools|Messages::getError',
          array ('message'=>CopixI18N::get ('news.unableToFind'),
          'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'news'))));
      }

      //check if the user has the rights
      $servicesHeadings = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
      $capability       = CopixUserProfile::valueOf ($servicesHeadings->getPath ($toDelete->id_head), 'pictures');
      $workflow         = & CopixClassesFactory::getInstanceOf ('copixheadings|workflow');
      if ($capability < PROFILE_CCV_WRITE ||
         ($capability < PROFILE_CCV_PUBLISH && $toDelete->status_news == $workflow->getPublish())) {
         return CopixActionGroup::process ('genericTools|Messages::getError',
         array ('message'=>CopixI18N::get ('news.error.notAnAuthorizedHead'),
         'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'news','level'=>$toDelete->id_head))));
      }

      //Confirmation screen ?
      if ($toDelete->status_news != $workflow->getTrash() && !isset ($this->vars['confirm'])){
      	return CopixActionGroup::process ('genericTools|Messages::getConfirm',
      		array ('title'=>CopixI18N::get ('news.title.confirmdelevent'),
      		'message'=>CopixI18N::get ('news.message.confirmdelevent'),
      		'confirm'=>CopixUrl::get('news|admin|delete', array('id_news'=>$toDelete->id_news, 'confirm'=>'1')),
      		'cancel'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'news', 'level'=>$toDelete->id_head))));
      }

      //Delete news
      $dao->delete($toDelete->id_news);
      return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('browse'=>'news', 'level'=>$toDelete->id_head)));
   }

   /**
    * _notThisRight
    */
   function _notThisRight ($missingRight,$levelBack=Null) {
   	return CopixActionGroup::process ('genericTools|Messages::getError',
               array ('message'=>CopixI18N::get('news.error.'.$missingRight.'Required'),
                      'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'news','level'=>$levelBack))));
   }

   /**
    * updates informations on a single news object from the vars.
    * le formulaire.

    */
   function _validFromForm (& $toUpdate){
      $toCheck = array ('title_news', 'content_news', 'summary_news','id_pict');
      //$toCheck = array ('title_news', 'content_news', 'summary_news');
      foreach ($toCheck as $elem){
         if (isset ($this->vars[$elem])){
             $toUpdate->$elem = $this->vars[$elem];
         }
      }

      if (isset ($this->vars['datewished_news'])){
         $toUpdate->datewished_news = CopixI18N::dateToBD ($this->vars['datewished_news']);
      }
   }

   /**
	* Fonction privée pour indiquer qu'il manque des paraètres à l'appel de notre fonction
	*/
	function _missingParameters ($id_head = null){
		return CopixActionGroup::process ('genericTools|Messages::getError',
		array ('message'=>CopixI18N::get ('pictures.error.missingParameters'),
		'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'news'))));

	}


   /**
    * sets the current edited news.

    */
   function _setSessionNews ($toSet){
      $_SESSION['MODULE_NEWS_EDITED_NEWS'] = $toSet !== null ? serialize($toSet) : null;
   }

   /**
    * gets the current edited news.

    */
   function _getSessionNews () {
      CopixDAOFactory::fileInclude ('News');
      return isset ($_SESSION['MODULE_NEWS_EDITED_NEWS']) ? unserialize ($_SESSION['MODULE_NEWS_EDITED_NEWS']) : null;
   }
}
?>
