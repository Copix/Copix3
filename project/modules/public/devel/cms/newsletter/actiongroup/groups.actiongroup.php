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
* @package	cms
* @subpackage newsletter
* Admin services for the newsletter groups.
*/
class ActionGroupGroups extends CopixActionGroup {

	/**
    * initialize group object
    */
	function doCreate () {
		if (!isset($this->vars['id_head'])){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('newsletter.error.missingParameters'),
			'back'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'newsletter'))));
		}
		//check if the user has the rights to create group.
		if (CopixUserProfile::valueOf ('modules|newsletter', 'newsletter') < PROFILE_CCV_MODERATE) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('newsletter.error.notAnAuthorizedAction'),
			'back'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'newsletter', 'level'=>$this->vars['id_head']))));
		}
		$group = & CopixDAOFActory::createRecord ('newslettergroups');
		//just to go back to admin heading page....
		$group->id_head = $this->vars['id_head'];
		$this->_setSessionGroup ($group);

		return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('browse'=>'newsletter', 'level'=>$this->vars['id_head'], 'kind'=>1)));
	}

	/**
    * Prepare theme edition
    */
	function doPrepareEdit () {
		if (!isset($this->vars['id_head'])){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('newsletter.error.missingParameters'),
			'back'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'newsletter'))));
		}
		if (!isset ($this->vars['id_nlg'])){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('newsletter.error.missingParameters'),
			'back'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'newsletter', 'level'=>$this->vars['id_head']))));
		}
		//check if the user has the rights to create group.
		if (CopixUserProfile::valueOf ('modules|newsletter', 'newsletter') < PROFILE_CCV_MODERATE) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('newsletter.error.notAnAuthorizedAction'),
			'back'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'newsletter', 'level'=>$this->vars['id_head']))));
		}
		$dao = & CopixDAOFactory::getInstanceOf ('newslettergroups');
		if (!$toEdit = $dao->get ($this->vars['id_nlg'])){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('newsletter.error.cannotFindGroup'),
			'back'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'newsletter', 'level'=>$this->vars['id_head'], 'kind'=>'1'))));
		}
		//just to go back to admin heading page....
		$toEdit->id_head = $this->vars['id_head'];
		$this->_setSessionGroup($toEdit);

		return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('browse'=>'newsletter', 'level'=>$this->vars['id_head'], 'kind'=>'1')));
	}

	/**
    * Cancel the edition...... empty the session data
    */
	function doCancelEdit (){
		$level = '';
		if ($group = $this->_getSessionGroup()){
			$level = $group->id_head;
		}
		$this->_setSessionGroup(null);
		return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('browse'=>'newsletter', 'level'=>$level, 'kind'=>'1')));
	}

	/**
    * apply updates on the edited group.
    * save to datebase if ok and save file.
    */
	function doValid (){
		if (!$toValid = $this->_getSessionGroup()){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('newsletter.error.cannotGetSession'),
			'back'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'newsletter', 'kind'=>'1'))));
		}
		//check if the user has the rights to create group.
		if (CopixUserProfile::valueOf ('modules|newsletter', 'newsletter') < PROFILE_CCV_MODERATE) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('newsletter.error.notAnAuthorizedAction'),
			'back'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'newsletter', 'level'=>$this->vars['id_head']))));
		}

		$dao = & CopixDAOFactory::getInstanceOf ('newslettergroups');
		$this->_validFromForm($toValid);
		if ($dao->check ($toValid) !== true){
			$this->_setSessionGroup($toValid);
			return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('browse'=>'newsletter', 'level'=>$toValid->id_head, 'kind'=>'1', 'e'=>'1')));
		}
		//inserting or updating.
		if ($toValid->id_nlg > 0){
			$dao->update($toValid);
		}else{
			$dao->insert($toValid);
		}
		$this->_setSessionGroup (null);
		return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('browse'=>'newsletter', 'level'=>$toValid->id_head, 'kind'=>'1')));
	}

	/**
    * supression effective de la catégorie.
    */
	function doDelete (){
		if (!isset($this->vars['id_head'])){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('newsletter.error.missingParameters'),
			'back'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'newsletter'))));
		}
		if (!isset ($this->vars['id_nlg'])){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('newsletter.error.missingParameters'),
			'back'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'newsletter', 'level'=>$this->vars['id_head'], 'kind'=>'1'))));
		}
		//vérification du mode de supression.
		$forceDelete = (isset ($this->vars['forceDelete']) && $this->vars['forceDelete'] == 1);

		if (!$forceDelete){
			if ((!isset ($this->vars['moveTo'])) || (strlen (trim ($this->vars['moveTo'])) == 0)){
				$forceDelete = true;
			}else{
				if ($this->vars['moveTo'] == $this->vars['id_nlg']){
					//on ne peut pas déplacer vers la catégorie à supprimer.
					return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get('newsletter|admingroup|confirmDelete', array('id_nlg'=>$this->vars['id_nlg'], 'id_head'=>$this->vars['id_head'])));
				}
			}
		}

		//si déplacement, procède au déplacement.
		$dao = & CopixDAOFactory::getInstanceOf ('NewsletterMailLinkGroups');
		if (!$forceDelete){
			$dao->moveGroup ($this->vars['id_nlg'], $this->vars['moveTo']);
		}else{
			$dao->deleteByGroup ($this->vars['id_nlg']);
		}

		$daoGroup = CopixDAOFactory::getInstanceOf ('NewsletterGroups');
		$daoGroup->delete ($this->vars['id_nlg']);

		return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get('copixheadings|admin|', array('browse'=>'newsletter', 'level'=>$this->vars['id_head'], 'kind'=>'1')));
	}

	/**
    * Ecran de confirmation de suppression d'une catégorie
    */
	function getConfirmDelete (){
		if (!isset($this->vars['id_head'])){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('newsletter.error.missingParameters'),
			'back'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'newsletter'))));
		}
		if (!isset ($this->vars['id_nlg'])){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('newsletter.error.missingParameters'),
			'back'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'newsletter', 'level'=>$this->vars['id_head']))));
		}

		$tpl = & new CopixTpl ();
		$tpl->assign ('TITLE_PAGE', CopixI18N::get ('newsletter.titlePage.deleteGroup'));
		$tpl->assign ('MAIN', CopixZone::process ('GroupConfirmDelete', array ('id_nlg'=>$this->vars['id_nlg'], 'id_head'=>$this->vars['id_head'])));

		return new CopixActionReturn (CopixactionReturn::DISPLAY, $tpl);
	}


	function _validFromForm (& $toUpdate) {
		$toCheck = array ('name_nlg', 'desc_nlg');
		foreach ($toCheck as $elem){
			if (isset ($this->vars[$elem])){
				$toUpdate->$elem = $this->vars[$elem];
			}
		}
	}

	/**
    * gets the current edited group.

    */
	function _getSessionGroup (){
		CopixDAOFactory::fileInclude ('newslettergroups');
		return isset ($_SESSION['MODULE_NEWSLETTER_EDITED_GROUP']) ? unserialize ($_SESSION['MODULE_NEWSLETTER_EDITED_GROUP']) : null;
	}

	/**
    * sets the current edited group.

    */
	function _setSessionGroup ($toSet){
		$_SESSION['MODULE_NEWSLETTER_EDITED_GROUP'] = $toSet !== null ? serialize($toSet) : null;
	}
}
?>