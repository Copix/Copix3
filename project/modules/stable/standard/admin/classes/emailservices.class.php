<?php
/**
 * @package standard
 * @subpackage admin 
 * 
 * @author		Estelle Fersing
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */
class EmailServices {
	
	/* 
	* Récupère les informations concernant l'envoi d'email
	* @return array avec la configuration pour les envoyer
	*/
	public function getInfoMail() {
		$infoMail = array();
		$arrParametres = CopixConfig::getParams ('default');
		foreach($arrParametres as $oneParam){
	   		if($oneParam['Name'] == 'mailEnabled'){
	   			$infoMail['enable'] = $oneParam['Value'];
	   		}elseif($oneParam['Name'] == 'mailSmtpHost'){
	   			$infoMail['smtp'] = $oneParam['Value'];
	   		}elseif($oneParam['Name'] == 'mailMethod'){
	   			$infoMail['method'] = $oneParam['Value'];
	   		}
	   	}

		return $infoMail;
	}
	
	/* 
	* Génère un nouvel email avec des paramètres par défaut 
	* @return array avec les différentes valeur du mail et de la configuration pour les envoyer
	*/
	public function newMail() {
		$monMail = array();
		$arrParametres = CopixConfig::getParams ('default');
		foreach($arrParametres as $oneParam){
	   		if($oneParam['Name'] == 'mailFrom'){
	   			try {
					$monMail['from'] = CopixFormatter::getMail ($oneParam['Value']); 
				}
				catch(CopixException $e){
					$monMail['from'] = null;
				}
	   		}elseif($oneParam['Name'] == 'mailFromName'){
	   			$monMail['fromname'] = $oneParam['Value'];
	   		}
	   	}
		
		$monMail['dest'] = null;
		$monMail['cc'] = null;
		$monMail['cci'] = null;
		$monMail['subject'] = _i18n ('email.title');
		$monMail['msg'] = _i18n ('email.message');	
		return $monMail;
	}
	
	/*
	 * Vérifie que les champs obligatoire du mail sont remplis et l'envoi
	 * @param array avec les différentes valeurs du mail
	 * @return array avec les erreurs rencontrées
	 */
	public function sendmail($mail) {
		$arrErrors  = array ();
		if($mail['dest'] == null || $mail['dest'] == "") {
			$arrErrors[] = _i18n('email.error.nodest');
		}else{
			$arrTmp = explode(",",$mail['dest']);
			try{
				foreach($arrTmp as $key=>$tmpMail) {
					if($tmpMail != "") {
						CopixFormatter::getMail($tmpMail);
					}
				}
			}catch (CopixException $e){
				$arrErrors[] = _i18n('email.error.baddest');
			}
		}
		
		if($mail['cc'] != "") {
			$arrTmp = explode(",",$mail['cc']);
			try{
				foreach($arrTmp as $key=>$tmpMail) {
					if($tmpMail != "") {
						CopixFormatter::getMail($tmpMail);
					}
				}
			}catch (CopixException $e){
				$arrErrors[] = _i18n('email.error.badcc');
			}
		}else{
			$mail['cc'] = "";
		}
		
		if($mail['cci'] != "") {
			$arrTmp = explode(",",$mail['cci']);
			try{
				foreach($arrTmp as $key=>$tmpMail) {
					if($tmpMail != "") {
						CopixFormatter::getMail($tmpMail);
					}
				}
			}catch (CopixException $e){
				$arrErrors[] = _i18n('email.error.badcci');
			}
		}else{
			$mail['cci'] = "";
		}
		
		if($mail['subject'] == null || $mail['subject'] == "") {
			$arrErrors[] = _i18n('email.error.nosubject');
		}
		
		if($mail['msg'] == null || $mail['msg'] == "") {
			$arrErrors[] = _i18n('email.error.nomsg');
		}
		
		if($mail['from'] == null || $mail['from'] == "") {
			$arrErrors[] = _i18n('email.error.nofrom');
		}else{
			$arrTmp = explode(",",$mail['from']);
			try{
				foreach($arrTmp as $tmpMail) {
					if($tmpMail != "") {
						CopixFormatter::getMail($tmpMail);
					}
				}
			}catch (CopixException $e){
				$arrErrors[] = _i18n('email.error.badfrom');
			}
		}
		
		if($mail['fromname'] == null || $mail['fromname'] == "") {
			$arrErrors[] = _i18n('email.error.nofromname');
		}
		
		if(count($arrErrors) == 0) {
			$monMail = new CopixTextEMail ($mail['dest'], $mail['cc'], $mail['cci'], utf8_decode($mail['subject']), utf8_decode($mail['msg']));
			$monMail->send ($mail['from'], $mail['fromname']);
			CopixSession::set ('admin|email|donnees', $this->newMail());
		}
		return $arrErrors;
		
	}
}