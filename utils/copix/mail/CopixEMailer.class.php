<?php
/**
 * @package		copix
 * @subpackage	utils
 * @author			Croes Gérald, Jouanneau Laurent
 * @copyright		CopixTeam
 * @link				http://copix.org
 * @license			http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */


/**
 * Mailer utilisant htmlMimeMail
 *
 * @package		copix
 * @subpackage	utils
 */
class CopixEMailer {
	/**
	 * Liste des erreurs
	 *
	 * @var array
	 */
	private $_errors = array ();
	
	/**
	 * Initialise un objet htmlMimeMail pour l'envoi
	 *
	 * @param Array $pArHeader Tableau de headers particuliers
	 * @return htmlMimeMail
	 */
	private function _createMailer ($pArHeader = null) {
		Copix::RequireOnce (COPIX_PATH . '../htmlMimeMail/htmlMimeMail.php');
		$mail = new htmlMimeMail ();
		$mail->setReturnPath (CopixConfig::get ('|mailFrom'));
		$mail->setFrom ('"' . CopixConfig::get ('|mailFromName') . '" <' . CopixConfig::get ('|mailFrom') . '>');
		$mail->setHeader ('X-Mailer', 'COPIX (http://copix.org) with HTML Mime mail class (http://www.phpguru.org)');
		$mail->setTextCharset (CopixI18N::getCharset ());
		$mail->setHTMLCharset (CopixI18N::getCharset ());
		if (is_array($pArHeader)){
			foreach ($pArHeader as $key=>$header){
				$mail->setHeader ($key, $header);
			}
		}
		if (CopixConfig::get ('|mailMethod') == 'smtp') {
			$auth = (CopixConfig::get ('|mailSmtpAuth') == '') ? null : CopixConfig::get ('|mailSmtpAuth');
			$pass = (CopixConfig::get ('|mailSmtpPass') == '') ? null : CopixConfig::get ('|mailSmtpPass');
			$hasAuth = ($auth != null);
			$mail->setSMTPParams (CopixConfig::get ('|mailSmtpHost'), null, null, $hasAuth, $auth, $pass);
		}
		return $mail;
	}
	
	/**
	 * Retourne une en-tête du mail
	 *
	 * @param htmlMimeMail $pMail Object htmlMimeMail
	 * @param string $pHeader Nom de l'en-tête
	 * @return string
	 */
	private function _header (htmlMimeMail $pMail, $pHeader) {
		return $pMail->getHeader ($pHeader);
	}
	
	/**
	 * Envoi un e-mail
	 *
	 * @param CopixEMail $pCopixEMail Object CopixEMail contenant les informations du mail à envoyer
	 * @param string $pFromAdress Adresse de l'expéditeur
	 * @param string $pFromName Nom de l'expéditeur
	 * @param Array $pArHeader Tableau de headers particuliers
	 * @return boolean
	 */
	public function send (CopixEMail $pCopixEMail, $pFromAdress = null, $pFromName = null, $pArHeader = null) {
		$mailer = $this->_createMailer ($pArHeader);
		$this->_errors = array ();

		// Prépare le mail
		$to = $pCopixEMail->prepareEmail ($mailer, $pFromAdress, $pFromName);

		$toReturn = false;
		if (intval (CopixConfig::get ('|mailEnabled')) == 1) {
			$mailMethod = CopixConfig::get ('|mailMethod');
				
			// Met un place un error handle pour récuperer les messages d'avertissement
			set_error_handler (array ($this, '_handleError'), E_WARNING|E_CORE_WARNING|E_USER_WARNING);
			$oldHtmlErrors = ini_set ('html_errors', false);
				
			// Effectue l'envoi
			$toReturn = $mailer->send ($to, $mailMethod);
				
			// Restaure le handler
			ini_set ('html_errors', $oldHtmlErrors);
			restore_error_handler ();
				
			/// Récupère aussi les erreurs du mailer
			if (isset ($mailer->errors)) {
				$this->_errors = array_merge ($this->_errors, $mailer->errors);
			}
				
			$status = ($toReturn) ? 'SENT' : 'FAILED';
		} else {
			$status = "DISABLED";
		}

		if (intval (CopixConfig::get ('|mailLogging')) == 1) {

			// Message de base
			$msg = sprintf (
				"%s: %s, from=%s, to=%s, cc=%s, bcc=%s, subject=%s",
				date ("Y-m-d H:i:s"),
				$status,
				$this->_header ($mailer, 'From'),
				(!empty($to)) ? join (",", $to) : '',
				$this->_header ($mailer, 'Cc'),
				$this->_header ($mailer, 'Bcc'),
				$this->_header ($mailer, 'Subject')
			);

			// Ajoute les messages d'erreurs s'il y en a
			if (count ($this->_errors)) {
				$msg .= sprintf (", errors=<%s>", join ("|", $this->_errors));
			}

			// Récupère la référence à l'appelant
			$me = dirname (__FILE__);
			$logExtra = array ();
			foreach (debug_backtrace () as $trace) {
				if ($trace['file'] && (substr ($trace['file'], 0, strlen ($me)) != $me)) {
					$logExtra = $trace;
					break;
				}
			}

			// Ecrit le log
			_log ($msg, 'email', ($status == "FAILED") ? CopixLog::ERROR : CopixLog::NOTICE, $logExtra);
		}

		return $toReturn;
	}
	
	/**
	 * Capture les warnings
	 *
	 * @param integer $errno Numéro de l'erreur
	 * @param string $errstr Texte de l'erreur
	 */
	public function _handleError ($errno, $errstr) {
		$this->_errors[] = $errstr;
	}

	/**
	 * Retourne les erreurs du dernier envoi
	 *
	 * @return array
	 */
	public function getErrors () {
		return $this->_errors;
	}
}