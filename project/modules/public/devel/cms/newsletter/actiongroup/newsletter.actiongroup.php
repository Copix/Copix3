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
CopixClassesFactory::fileInclude ('cms|ServicesCMSPage');

require_once (CopixModule::getPath('newletter').'newsletter/'.COPIX_CLASSES_DIR.'newsletter.services.class.php');
require_once (COPIX_UTILS_PATH.'CopixEMailer.class.php');

/**
* @package	cms
* @subpackage newsletter
* front office for newsletter.
*/
class ActionGroupNewsletter extends CopixActionGroup {
	/**
    * Display newsletter
    */
	function getNewsletter () {
		if (!isset($this->vars['id'])){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('newsletter.error.missingParameters'),
			'back'=>CopixUrl::get()));
		}
		if (!isset($this->vars['date'])){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('newsletter.error.missingParameters'),
			'back'=>CopixUrl::get()));
		}
		$dao = & CopixDAOFactory::getInstanceOf ('newslettersend');
		if (!$toShow = $dao->get ($this->vars['id'], $this->vars['date'])){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('newsletter.error.cannotFindNewsletter'),
			'back'=>CopixUrl::get()));
		}
		$tpl = & new CopixTpl ();
		$tpl->assign ('TITLE_PAGE', $toShow->title_nls);
		$tpl->assign ('MAIN', $toShow->htmlcontent_nls);
		return new CopixActionReturn (CopixactionReturn::DISPLAY, $tpl);
	}

	/**
    * Souscription à la newsletter.
    */
	function doSubscribe (){
		if (!isset($this->vars['id_nlg'])){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('newsletter.error.missingParameters'),
			'back'=>CopixUrl::get()));
		}
		if ((! isset ($this->vars['mail'])) || (strlen($this->vars['mail'])==0)){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('newsletter.error.missingParameters'),
			'back'=>CopixUrl::get()));
		}

		$dao     = & CopixDAOFactory::getInstanceOf ('newslettermail');
		$daoLink = & CopixDAOFactory::getInstanceOf ('NewsletterMailLinkGroups');

		foreach ((array)$this->vars['id_nlg'] as $id_nlg){
			if (!($link = $daoLink->get($id_nlg, $this->vars['mail']))) {
				$record = & CopixDAOFactory::createRecord ('NewsletterMailLinkGroups');
				$record->mail_nlm = $this->vars['mail'];
				$record->id_nlg   = $id_nlg;
				$daoLink->insert ($record);
			}
		}

		if (!$mail = $dao->get ($this->vars['mail'])){
			$this->_sendConfirm($this->vars['mail']);

			$record = & CopixDAOFactory::createRecord ('newslettermail');
			$record->valid_nlm = 0;
			$record->mail_nlm = $this->vars['mail'];
			$dao->insert ($record);

			$tpl = & new CopixTpl ();
			$tpl->assign ('TITLE_PAGE', CopixI18N::get ('newsletter.titlePage.subscribe'));
			$tpl->assign ('MAIN', CopixI18N::get ('newsletter.message.subscribe'));

			return new CopixActionReturn (CopixactionReturn::DISPLAY, $tpl);
		}else{
			return CopixActionGroup::process ('newsletter|Newsletter::doValidSubscription',
			array ('mail'=>$this->vars['mail']));
		}
	}

	/**
    * desinscription à la newsletter.
    */
	function doUnsubscribe (){
		if (!isset($this->vars['mail'])){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('newsletter.error.missingParameters'),
			'back'=>CopixUrl::get()));
		}
		if (!isset($this->vars['id_nlg'])){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('newsletter.error.missingParameters'),
			'back'=>CopixUrl::get()));
		}

		$dao = & CopixDAOFactory::getInstanceOf ('NewsletterMailLinkGroups');

		foreach ((array)$this->vars['id_nlg'] as $id_nlg){
			$dao->delete ($id_nlg, $this->vars['mail']);
		}

		$dao = & CopixDAOFactory::getInstanceOf ('NewsletterMailLinkGroups');

		//if all groupe checked we delete mail
		$sp       = CopixDAOFactory::createSearchParams ();
		$sp->addCondition ('mail_nlm','=',$this->vars['mail']);

		if (!count($dao->findBy($sp))) {
			$daoMail = CopixDAOFactory::getInstanceOf ('NewsletterMail');
			$daoMail->delete ($this->vars['id_nlm']);
		}


		$subject = CopixI18N::get ('newsletter.mail.subject.unsubscribe');
		$message = CopixI18N::get ('newsletter.message.unsubscribe.confirm');
//TODO cc et cci non définis
$cc = null;
$cci = null;
		$monMail = & new CopixEMail ($this->vars['mail'], $cc, $cci, $subject, $message);
		$monMail->send ();
		$tpl = & new CopixTpl ();

		$tpl->assign ('TITLE_PAGE', CopixI18N::get ('newsletter.titlePage.unsubscribe'));
		$tpl->assign ('MAIN'      , CopixI18N::get ('newsletter.message.unsubscribe.confirm'));

		return new CopixActionReturn (CopixactionReturn::DISPLAY, $tpl);
	}

	/**
    * UNSouscription à la newsletter.
    */
	function getUnsubscribe (){
		if ((! isset ($this->vars['mail'])) || (strlen($this->vars['mail'])==0)){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('newsletter.error.missingParameters'),
			'back'=>CopixUrl::get()));
		}

		$dao = & CopixDAOFactory::getInstanceOf ('newslettermail');
		if (!$mail = $dao->get ($this->vars['mail'])){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('newsletter.error.cannotFindMail'),
			'back'=>CopixUrl::get()));
		}

		$tpl = & new CopixTpl ();
		$tpl->assign ('TITLE_PAGE', CopixI18N::get ('newsletter.titlePage.unsubscribe'));
		$tpl->assign ('MAIN'      , CopixZone::process ('Unsubscribe',array('mail'=>$mail)));

		return new CopixActionReturn (CopixactionReturn::DISPLAY, $tpl);
	}


	/**
    * Souscription à la newsletter.
    */
	function doValidSubscription (){
		if ((! isset ($this->vars['mail'])) || (strlen($this->vars['mail'])==0)){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('newsletter.error.missingParameters'),
			'back'=>CopixUrl::get()));
		}

		$dao = & CopixDAOFactory::getInstanceOf ('newslettermail');
		if (!$mail = $dao->get ($this->vars['mail'])){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('newsletter.error.cannotFindMail'),
			'back'=>CopixUrl::get()));
		}

		$mail->valid_nlm = 1;
		$dao->update ($mail);

		$tpl = & new CopixTpl ();
		$tpl->assign ('TITLE_PAGE', CopixI18N::get ('newsletter.titlePage.validsubscribe'));
		$tpl->assign ('MAIN', CopixI18N::get ('newsletter.message.validSubscribe'));

		return new CopixActionReturn (CopixactionReturn::DISPLAY, $tpl);
	}

	/**
    * send a confirmation mail.

    */
	function _sendConfirm ($to){
		$subject  = CopixI18N::get ('newsletter.mail.subject.confirmation');
		$message  = CopixI18N::get ('newsletter.mail.subscribeMessage');

		$message .= '&nbsp;<a href="'.CopixUrl::get().CopixUrl::get('newsletter|default|validSubscription', array('mail'=>$to)).'">';
		$message .= CopixI18N::get ('newsletter.mail.subject.here').'</a>';

		$messageTextAlternatif  = CopixI18N::get ('newsletter.mail.subscribeMessage');
		$messageTextAlternatif .= ' '.CopixUrl::get().CopixUrl::get('newsletter|default|validSubscription', array('mail'=>$to));

//TODO cc et cci non définis
$cc = null;
$cci = null;
		$monMailHTML = & new CopixHTMLEMail ($to, $cc, $cci, $subject, $message, $messageTextAlternatif);
		$monMailHTML->send ();
	}
}
?>