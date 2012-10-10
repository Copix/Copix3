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
* Admin services for the newsletter mail.
*/
class ActionGroupMailAdmin extends CopixActionGroup {
	/**
    * supression effective de l'inscrit.
    */
	function doDelete (){
		if (!isset ($this->vars['id_head'])){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('news.error.missingParameters'),
			'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'newsletter', 'kind'=>'2'))));
		}

		if (!isset ($this->vars['mail_nlm'])){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('news.error.missingParameters'),
			'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'newsletter', 'kind'=>'2'))));
		}

		$dao = & CopixDAOFactory::getInstanceOf ('NewsletterMail');
		if (!$toDelete = $dao->get ($this->vars['mail_nlm'])){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('newsletter.error.cannotFindMail'),
			'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'newsletter', 'kind'=>'2'))));
		}

		//Confirmation screen ?
		if (!isset ($this->vars['confirm'])){
			return CopixActionGroup::process ('genericTools|Messages::getConfirm',
			array ('title'=>CopixI18N::get ('newsletter.title.confirmDeleteMail'),
			'message'=>CopixI18N::get ('newsletter.message.confirmDeleteMail', $toDelete->mail_nlm),
			'confirm'=>CopixUrl::get('newsletter|mail|delete', array('mail_nlm'=>$toDelete->mail_nlm, 'confirm'=>'1', 'id_head'=>$this->vars['id_head'])),
			'cancel'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'newsletter', 'level'=>$this->vars['id_head'], 'kind'=>'2'))));
		}

		//Delete mail
		$dao->delete($toDelete->mail_nlm);

		$dao = & CopixDAOFactory::getInstanceOf ('NewsletterMailLinkGroups');
		$dao->deleteByMail ($toDelete->mail_nlm);


		return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('browse'=>'newsletter', 'level'=>$this->vars['id_head'], 'kind'=>'2')));
	}


	/**
    * apply updates on the edited mail.
    * save to datebase if ok.
    */
	function doValid (){
		if (!$toValid = $this->_getSessionNewsletterMail()){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('newsletter.error.cannotGetSession'),
			'back'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'newsletter', 'kind'=>'2'))));
		}


		//demande de mettre l'objet à jour en fonction des valeurs saisies dans le
		//formulaire.
		$this->_validFromForm($toValid);
		$this->_setSessionNewsletterMail($toValid);
		$mail    = & CopixDAOFactory::createRecord ('NewsletterMail');
		$dao     = & CopixDAOFactory::getInstanceOf ('NewsletterMail');
		$daoLink = & CopixDAOFactory::getInstanceOf ('NewsletterMailLinkGroups');
		$insert  = false;

		if ($dao->check ($toValid) !== true){
			$this->_setSessionNewsletterMail($toValid);
			return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get('newsletter|mail|edit', array('e'=>'1')));
		}else{
			//insert or update ??
			if ($dao->get($toValid->mail_nlm)){
				$dao->update ($toValid);
				$daoLink->deleteByMail($toValid->mail_nlm);
			}else{
				$dao->insert ($toValid);
			}

			if (count($toValid->id_nlg)) {
				foreach ($toValid->id_nlg as $id_nlg){
					$mailLinkGroup           = & CopixDAOFactory::createRecord ('NewsletterMailLinkGroups');
					$mailLinkGroup->id_nlg   = $id_nlg;
					$mailLinkGroup->mail_nlm = $toValid->mail_nlm;
					$daoLink->insert ($mailLinkGroup);
				}
			}
			return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get('copixheadings|admin|', array('browse'=>'newsletter', 'kind'=>'2', 'level'=>$toValid->id_head)));
		}
	}

	/**
    * gets the edit page for the news.
    */
	function getEdit (){
		if (!$toEdit = $this->_getSessionNewsletterMail()){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('newsletter.error.cannotGetSession'),
			'back'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'newsletter', 'kind'=>'2'))));
		}

		if (!count($toEdit->id_nlg)>0) {
			$daoLink = & CopixDAOFactory::getInstanceOf ('NewsletterMailLinkGroups');
			$sp = & CopixDAOFactory::createSearchParams ();
			$sp->addCondition('mail_nlm','=',$toEdit->mail_nlm);
			if (count($tabId = $daoLink->findBy($sp))) {
				foreach ($tabId as $id){
					$toEdit->id_nlg[] = $id->id_nlg;
				}
			}
		}
		$this->_setSessionNewsletterMail($toEdit);

		$tpl = & new CopixTpl ();
		$tpl->assign ('TITLE_PAGE', (isset($toEdit->id_news) && strlen ($toEdit->id_news) >= 1) ? CopixI18N::get ('newsletter.titlePage.updateMail') : CopixI18N::get ('newsletter.titlePage.createMail'));
		$tpl->assign ('MAIN', CopixZone::process ('NewsletterMailEdit',array ('toEdit'=>$toEdit, 'e'=>isset ($this->vars['e']))));
		return new CopixActionReturn (CopixactionReturn::DISPLAY, $tpl);
	}

	/**
    * prepare a new news to edit.
    */
	function doCreate (){
		if (!isset($this->vars['id_head'])){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('newsletter.error.missingParameters'),
			'back'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'newsletter', 'kind'=>'2'))));
		}
		$mail          = & CopixDAOFactory::createRecord ('NewsletterMail');
		$mail->id_nlg  = null;
		//just to go back to admin heading page....
		$mail->id_head = $this->vars['id_head'];
		$this->_setSessionNewsletterMail($mail);

		return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get('newsletter|mail|edit'));
	}

	/**
    * prepare the group to edit.
    * check if we were given the news id to edit, then try to get it.
    */
	function doPrepareEdit () {
		if (!isset($this->vars['id_head'])){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('newsletter.error.missingParameters'),
			'back'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'newsletter', 'kind'=>'2'))));
		}
		if (!isset($this->vars['mail_nlm'])){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('newsletter.error.missingParameters'),
			'back'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'newsletter', 'kind'=>'2', 'level'=>$this->vars['id_head']))));
		}

		$dao = & CopixDAOFactory::getInstanceOf ('NewsletterMail');
		if (!$toEdit = $dao->get ($this->vars['mail_nlm'])){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('newsletter.error.cannotFindMail'),
			'back'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'newsletter', 'level'=>$this->vars['id_head'], 'kind'=>'2'))));
		}
		//just to go back to admin heading page....
		$toEdit->id_head = $this->vars['id_head'];

		$this->_setSessionNewsletterMail($toEdit);
		return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get('newsletter|mail|edit'));
	}

	/**
    * Cancel the edition...... empty the session data
    */
	function doCancelEdit (){
		$level = '';
		if ($mail = $this->_getSessionNewsletterMail()){
			$level = $mail->id_head;
		}
		$this->_setSessionNewsletterMail(null);
		return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('browse'=>'newsletter', 'level'=>$level, 'kind'=>'2')));
	}

	/**
    * updates informations on a single news object from the vars.
    * le formulaire.

    */
	function _validFromForm (& $toUpdate){
		$toCheck = array ('mail_nlm','id_nlg', 'valid_nlm');
		foreach ($toCheck as $elem){
			if (isset ($this->vars[$elem])){
				$toUpdate->$elem = $this->vars[$elem];
			}
		}
		//$toUpdate->valid_nlm = isset($this->vars['valid_nlm']) ? 1 : 0;
	}

	/**
    * gets the current edited mail.

    */
	function _getSessionNewsletterMail () {
		CopixDAOFactory::fileInclude ('NewsletterMail');
		return isset ($_SESSION['MODULE_NEWSLETTER_EDITED_MAIL']) ? unserialize ($_SESSION['MODULE_NEWSLETTER_EDITED_MAIL']) : null;
	}

	/**
    * sets the current edited mail.

    */
	function _setSessionNewsletterMail ($toSet){
		$_SESSION['MODULE_NEWSLETTER_EDITED_MAIL'] = ($toSet !== null ? serialize($toSet) : null);
	}
}
?>