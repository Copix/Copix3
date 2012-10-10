<?php
/**
 * @package standard
 * @subpackage admin 
 * @author		Estelle Fersing
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Actions pour tester si l'envoi de courriers électroniques fonctionne
 * @package standard
 * @subpackage admin  
 */
class ActionGroupEmail extends CopixActionGroup {
	/**
	 * Executé avant toute action
	 *
	 * @param string $pActionName Nom de l'action
	 */
	public function beforeAction ($pActionName) {
		CopixPage::add ()->setIsAdmin (true);
		CopixAuth::getCurrentUser ()->assertCredential ('basic:admin');
	}
	
	/**
	 * Création du mail avec vérification des paramètres
	 */
	public function processCreate (){
		_notify ('breadcrumb', array (
			'path' => array ('#' => _i18n ('email.create'))
		));
		$ppo = new CopixPPO ();
	   	$ppo->TITLE_PAGE = _i18n ('email.create');
	   	
		$ppo->infomail = _class("emailservices") ->getInfoMail();
	   	
		if( ($ppo->mail = CopixSession::get ('admin|email|donnees')) == null) {
	   		CopixSession::set ('admin|email|donnees', _class("emailservices") ->newMail() );
	   		$ppo->mail = CopixSession::get ('admin|email|donnees');
	   	}
			   	
	   	$ppo->sending  = _request('sending', 'false');
	   	$ppo->errors = _request('error');

	    return _arPPO ($ppo, 'email.tpl');
	}
	
	/**
	 * Envoie du mail
	 */
	public function processSend (){
		$ppo = _ppo ();
		$ppo->mail = CopixSession::get ('admin|email|donnees');
		$ppo->mail['send'] = true;
		$ppo->mail['dest'] = _request("maildest");
		$ppo->mail['cc'] = _request("mailcc");
		$ppo->mail['cci'] = _request("mailcci");
		$ppo->mail['from'] = _request("mailfrom");
		$ppo->mail['fromname'] = _request("mailfromname");
		$ppo->mail['subject'] = _request("mailtitle");
		$ppo->mail['msg'] = _request("mailmsg");
		CopixSession::set ('admin|email|donnees', $ppo->mail);
		$arrErrors = _class("emailservices") ->sendMail($ppo->mail);
		return CopixActionGroup::process ('admin|Email::create', array ('error'=>$arrErrors, 'sending'=>'true'));
	}
}