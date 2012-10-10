<?php
/**
 * @package cms
 * @subpackage survey
 * @author Bertrand Yan
 * @copyright 2001-2005 CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * @package cms
 * @subpackage survey
 * handle the administration of the SURVEY
 */
class ActionGroupSurveyAdmin extends CopixActionGroup {
	/**
    * show result for a survey

    * @return void 
    */
	function viewResult() {
		if (! CopixRequest::get('id_svy', null)) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message' => CopixI18N::get ('survey.id.required'),
			'back' => CopixUrl::get ('copixheadings|admin|')));
		}

		$dao = & CopixDAOFactory::getInstanceOf ('Survey');
		if (!$survey = $dao->get (CopixRequest::get('id_svy'))) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			 array ('message' => CopixI18N::get ('survey.unable.get'),
			 'back' => CopixUrl::get ('copixheadings|admin|')));
		}

		// check delete permission
		$headingProfileServices = &CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
		if (CopixUserProfile::valueOf ($headingProfileServices->getPath($survey->id_head), 'survey') < PROFILE_CCV_WRITE) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message' => CopixI18N::get('survey.right.required', 'lecture'),
			'back'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'survey', 'level'=>$survey->id_head))));
		}

		// create and assign template
		$tpl = & new CopixTpl ();
		$tpl->assign ('TITLE_PAGE', CopixI18N::get ('survey.titlePage.result', array ($survey->title_svy)));
		$tpl->assign ('MAIN',       CopixZone::process ('viewResult', array ('id_svy' => CopixRequest::get('id_svy'))));
		return new CopixActionReturn (CopixactionReturn::DISPLAY, $tpl);
	}

	/**
    * apply updates on the edited survey.
    * save to datebase if ok and save file.
    */
	function doValid () {
		if (!$toValid = $this->_getSessionSurvey()) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message' => CopixI18N::get ('survey.unable.get'),
			'back' => CopixUrl::get('||')));
		}
		$dao = & CopixDAOFactory::getInstanceOf ('Survey');

		// update form
		$this->_validFromForm($toValid);
		$toValid->option_svy = serialize($toValid->option_svy); // option_svy is an array
		// check if survey obj is OK
		if ($dao->check ($toValid) !== true) {
			// unserialize array of option item
			$toValid->option_svy = unserialize($toValid->option_svy);
			// store in session current survey
			$this->_setSessionSurvey($toValid);
			return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('survey|admin|edit', array ('e' => 1)));
		}

		// inserting or updating.
		if ($toValid->id_svy !== -1) {
			$dao->update ($toValid);
		} else {
			$toValid->id_svy = date("YmdHis") . rand(0, 100);
			$dao->insert ($toValid);
		}
		$this->_setSessionSurvey (null);
		return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('browse' => 'survey', 'level' => $toValid->id_head)));
	}

	/**
    * gets the edit page for the survey.
    */
	function getEdit () {
		if (!$toEdit = $this->_getSessionSurvey ()) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message' => CopixI18N::get ('survey.unable.get.edited'),
			'back' => CopixUrl::get('||')));
		}
		// get the current copixheadings caption
		$dao = &CopixDAOFactory::getInstanceOf ('copixheadings|CopixHeadings');
		if ($heading = $dao->get($toEdit->id_head)) {
			$toEdit->caption_head = $heading->caption_head;
		} else {
			$toEdit->caption_head = CopixI18N::get('copixheadings|headings.message.root');
		}
		$tpl = &new CopixTpl ();
		$tpl->assign ('TITLE_PAGE', $toEdit->id_svy == -1 ? CopixI18N::get ('survey.titlePage.create') : CopixI18N::get ('survey.titlePage.update'));
		$tpl->assign ('MAIN', CopixZone::process ('SurveyEdit', array ('toEdit' => $toEdit, 'e' => isset ($this->vars['e']))));
		return new CopixActionReturn (CopixactionReturn::DISPLAY, $tpl);
	}

	/**
    * gets survey contrib menu
    * 
    * @param bigint id_head id of current heading
    */
	function getSurveyContrib () {
		$tpl = &new CopixTpl ();
		$servicesHeading = &CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');

		$id_head = $this->_getSessionHeading ();
		// check if the heading exists. In the mean time, getting its caption
		$dao = &CopixDAOFactory::getInstanceOf ('copixheadings|CopixHeadings');
		if ($heading = $dao->get ($id_head)) {
			$caption_head = $heading->caption_head;
		} else {
			if ($id_head === null) {
				$caption_head = CopixI18N::get('copixheadings|headings.message.root');
			} else {
				return CopixActionGroup::process ('genericTools|Messages::getError',
				array ('message' => CopixI18N::get ('copixheadings|admin.error.cannotFindHeading'),
				'back' => 'index.php?module=copixheadings&desc=admin&level=' . $id_head));
			}
		}

		$tpl->assign ('TITLE_PAGE', '[' . $caption_head . '] ' . CopixI18N::get ('survey.titlePage.manageSurvey'));
		$tpl->assign ('MAIN',
		CopixZone::process ('SurveyContrib',
		 array ('profile|profile' => array (new CapabilityValueOf ($servicesHeading->getPath ($id_head) , 'survey', PROFILE_CCV_WRITE)),
		 'id_head' => $id_head)
		 )
		);
		return new CopixActionReturn (CopixactionReturn::DISPLAY, $tpl);
	}

	/**
    * prepare a new survey to edit.
    */
	function doCreate () {
		// init a new survey
		$survey = &CopixDAOFactory::createRecord ('Survey');
		$survey->id_head = CopixRequest::get('id_head', null, true);

		$survey->authuser_svy = 0;
		$survey->id_svy = -1;
		$survey->option_svy = array ();
		$survey->response_svy = 0;

		$this->_setSessionSurvey($survey);
		return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('survey|admin|edit'));
	}

	/**
    * Add a new option to the current survey
    */
	function doAddOption () {
		CopixClassesFactory::fileInclude ('survey|surveyoption');
		$toEdit = $this->_getSessionSurvey ();

		$this->_validFromForm ($toEdit);
		$toEdit->option_svy[] = new SurveyOption($this->vars['newoption']);
		$this->_setSessionSurvey ($toEdit);
		return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('survey|admin|edit'));
	}

	/**
    * Delete option given by the parameters index
    */
	function doDeleteOption () {
		$toEdit = $this->_getSessionSurvey ();
		$this->_validFromForm ($toEdit);
		unset($toEdit->option_svy[$this->vars['index']]);
		$this->_setSessionSurvey ($toEdit);
		return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('survey|admin|edit'));
	}

	/**
    * prepare the survey to edit.
    * check if we were given the survey id to edit, then try to get it.
    */
	function doPrepareEdit () {
		if (!isset ($this->vars['id_svy'])) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message' => CopixI18N::get ('survey.id.required'),
			'back' => CopixUrl::get ('copixheadings|admin|')));
		}

		$dao = &CopixDAOFactory::getInstanceOf ('Survey');
		if (!$toEdit = $dao->get ($this->vars['id_svy'])) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message' => CopixI18N::get ('survey.unable.get'),
			'back' => CopixUrl::get ('copixheadings|admin|')));
		}
		$toEdit->option_svy = unserialize($toEdit->option_svy);
		$this->_setSessionSurvey($toEdit);
		return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('survey|admin|edit'));
	}

	/**
    * Cancel the edition...... empty the session data
    */
	function doCancelEdit () {
		// locate current edited heading
		if ($survey = $this->_getSessionSurvey()) {
			$idHeadToReturn = $survey->id_head;
		} else {
			$idHeadToReturn = null;
		}
		// unset session survey
		$this->_setSessionSurvey(null);
		// return to current heading
		return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('browse' => 'survey', 'level' => $idHeadToReturn)));
	}

	/**
    * validation temporaire des éléments saisis.
    */
	function doValidEdit () {
		$toEdit = $this->_getSessionSurvey ();
		$this->_validFromForm ($toEdit);
		$this->_setSessionSurvey ($toEdit);
		return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('survey|admin|edit'));
	}

	/**
    * Delete a survey
    */
	function doDelete () {
		// get id to delete
		if (! CopixRequest::get('id_svy', null)) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message' => CopixI18N::get ('survey.id.required'),
			'back' => CopixUrl::get ('copixheadings|admin|')));
		}
		// get dao record
		$dao = &CopixDAOFactory::getInstanceOf ('Survey');
		if (!$survey = $dao->get ($this->vars['id_svy'])) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message' => CopixI18N::get ('survey.unable.get'),
			'back' => CopixUrl::get ('copixheadings|admin|')));
		}
		// Confirmation screen ?
		if (!isset ($this->vars['confirm'])) {
			return CopixActionGroup::process ('genericTools|Messages::getConfirm',
			array ('title' => CopixI18N::get ('survey.titlePage.confirmDelete'),
			'message' => CopixI18N::get ('survey.messages.confirmDelete'),
			'confirm' => CopixUrl::get('admin|delete', array('confirm' => 1, 'id_svy' => $survey->id_svy)),
			'cancel' => CopixUrl::get('copixheadings|admin|', array('browse' => 'survey', 'level' => $survey->id_head))));
		}
		// check delete permission
		$headingProfileServices = &CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
		if (CopixUserProfile::valueOf ($headingProfileServices->getPath($survey->id_head), 'survey') < PROFILE_CCV_MODERATE) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message' => CopixI18N::get('survey.right.required', 'modération'),
			'back' => CopixUrl::get('copixheadings|admin|', array('browse' => 'survey', 'level' => $survey->id_head))));
		}
		// delete record
		$dao->delete ($survey->id_svy);
		// return to adminheading panel
		return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('browse' => 'survey', 'level' => $survey->id_head)));
	}

	/**
    * updates informations on a single survey object from the vars.

    */
	function _validFromForm (&$toUpdate) {
		$toCheck = array ('title_svy', 'option_svy');
		foreach ($toCheck as $elem) {
			if (isset ($this->vars[$elem])) {
				$toUpdate->$elem = $this->vars[$elem];
			}
		}
		foreach ($toUpdate->option_svy as $index => $option) {
			if (isset ($this->vars['option' . $index])) {
				$toUpdate->option_svy[$index]->title = $this->vars['option' . $index];
			}
		}
	}

	/**
    * gets the current edited survey.

    */
	function _getSessionSurvey () {
		CopixDAOFactory::fileInclude ('Survey');
		CopixClassesFactory::fileInclude ('survey|SurveyOption');
		return isset ($_SESSION['MODULE_SURVEY_EDITED_SURVEY']) ? unserialize ($_SESSION['MODULE_SURVEY_EDITED_SURVEY']) : null;
	}

	/**
    * sets the current edited survey.
    * 

    */
	function _setSessionSurvey ($toSet) {
		$_SESSION['MODULE_SURVEY_EDITED_SURVEY'] = $toSet !== null ? serialize($toSet) : null;
	}
}
?>