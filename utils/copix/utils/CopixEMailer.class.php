<?php
/**
 * @package		copix
 * @subpackage 	utils
 * @author		Croes Gérald, Jouanneau Laurent , see copix.org for other contributors.
 * @copyright	2001-2006 CopixTeam
 * @link			http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Represents an EMail
 * @package copix
 * @subpackage utils
 */
abstract class CopixEMail {
	/**
	 * Content of the EMail.
	 * @var string
	 */
	protected $message;

	/**
	 * Subject
	 * @var string
	 */
	private $subject;

	/**
	 * Recipients
	 * @var string
	 */
	private $to;

	/**
	 * Carbon Copy
	 * @var string
	 */
	private $cc;

	/**
	 * Hidden carbon copy
	 * @var string
	 */
	private $cci;

	/**
	 * Sender
	 * @var string
	 */
	private $from;

	/**
	 * Sender name
	 * @var string
	 */
	private $fromName;

	/**
	 * Attachments
	 *
	 * Array of array ('body'=>$x, 'name'=>$x, 'c_type'=>$x, 'encoding'=>$x)
	 *
	 * @var associative array
	 */
	private $attachments = array ();

	/**
	 * Constructor
	 * @param string $to recipient
	 * @param string $cc Carbon Copy
	 * @param string $cci Hidden Carbon Copy
	 * @param string $message the message (HTML Format)
	 */
	public function __construct ($to, $cc, $cci, $subject, $message){
		$this->from     = CopixConfig::get ('|mailFrom');
		$this->fromName = CopixConfig::get ('|mailFromName');

		$this->to = $to;
		$this->cc = $cc;
		$this->cci = $cci;
		$this->message = $message;
		$this->subject = $subject;
	}

	/**
	 * Sends the EMail
	 * @param string $from the mail adress to send the email with
	 * @param sting $fromName the name of the expeditor
	 */
	public function send ($from = null, $fromName = null){
		$sender = new CopixEMailer();
		return $sender->send($this, $from, $fromName);		
	}

	/**
	 * Checks if we can send an email with the given configuration.
	 */
	public function check (){
		$error = new CopixErrorObject ();
		if ($this->to === null){
			$error->addError ('to', 'Aucune valeur donnée à destinataire.');
		}
		if ($this->from === null){
			$error->addError ('from', 'Aucune valeur expéditeur');
		}
		return $error;
	}

	/**
	 * Add an attachment
	 * @param binary $fileData the dataFile content
	 * @param string $fileName the fileName
	 * @param string $cType the mime type
	 * @param string $encoding the encoding type of filedata
	 * PhiX (15/12/2005)
	 */
	public function addAttachment ($fileData, $fileName = '', $cType='application/octet-stream', $encoding = 'base64') {
		$this->attachments[] = array(
                                    'body'		=> $fileData,
                                    'name'		=> $fileName,
                                    'c_type'	=> $cType,
                                    'encoding'	=> $encoding
		);
	}

	/**
	 * Enter description here...
	 *
	 * @param htmlMimeMail $mail
	 * @param unknown_type $fromAdress
	 * @param unknown_type $fromName
	 * @return unknown
	 */
	public function prepareEmail(htmlMimeMail $mail, $fromAdress, $fromName) {
		// Adds attachments
		foreach ($this->attachments as $attach) {
			$mail->addAttachment ($attach['body'], $attach['name'], $attach['c_type'], $attach['encoding']);
		}

		// Subject, To, Cc
		$mail->setSubject($this->subject);
		$to = (array) $this->to;
		if(!empty($this->cc)) {
			$mail->setCc ($this->cc);
		}
		
        // Bcc
        $bcc = $this->cci;
        $alwaysBcc = CopixConfig::get ('|mailAlwaysBcc');
        if(!empty($alwaysBcc)) {
        	$bcc = empty($bcc) ? $alwaysBcc : "$alwaysBcc; $bcc";
        }
        if(!empty($bcc)) {
	        $mail->setBcc ($bcc);
        }
		
		// Adresse de retour
		$fromAdress = $fromAdress == null ? $this->from : $fromAdress;
		$fromName =   $fromName == null ? $this->fromName : $fromName;
		$mail->setFrom ('"'.$fromName.'" <'.$fromAdress.'>');
		
		return $to;
	}
	
}

/**
 * EMail with a HTML content
 * @package copix
 * @subpackage utils
 */
class CopixHTMLEMail extends CopixEMail {
	/**
	 * Text equivalent of $this->message for mailers that cannot read HTML format
	 */
	protected $textEquivalent;

	/**
	 * Constructor
	 * @param string $to recipient
	 * @param string $cc Carbon Copy
	 * @param string $cci Hidden Carbon Copy
	 * @param string $message the message (HTML Format)
	 * @param string $textEquivalent The text alternative of $message if the mailer do not support HTML type
	 */
	function CopixHTMLEMail ($to, $cc, $cci, $subject, $message, $textEquivalent=null){
		parent::__construct ($to, $cc, $cci, $subject, $message);
		$this->textEquivalent = $textEquivalent;
	}

	function prepareEmail(htmlMimeMail $mailer, $fromAdress, $fromName) {
		$to = parent::prepareEmail($mailer, $fromAdress, $fromName);
		$mailer->setTextEncoding("8bit");
		$mailer->setHtmlEncoding("8bit");
		$mailer->setHtml($this->message, $this->textEquivalent);
		return $to;
	}

}

/**
 * EMail with a text content
 * @package copix
 * @subpackage utils
 */
class CopixTextEMail extends CopixEMail {
	/**
	 * Constructor
	 * @param string $to recipient
	 * @param string $cc Carbon Copy
	 * @param string $cci Hidden Carbon Copy
	 * @param string $message the message (HTML Format)
	 */
	public function __construct ($to, $cc, $cci, $subject, $message){
		parent::__construct ($to, $cc, $cci, $subject, $message);
	}

	public function prepareEmail(htmlMimeMail $mailer, $fromAdress, $fromName) {
		$to = parent::prepareEmail($mailer, $fromAdress, $fromName);		
		$mailer->setTextEncoding("8bit");
		$mailer->setText($this->message);
		return $to;
	}
}


/**
 * The mailer (uses htmlMimeMail to really send e mail)
 * @package copix
 * @subpackage utils
 */
class CopixEMailer {
	
	/**
	 * Liste des erreurs.
	 *
	 * @var array
	 */
	private $errors = array();
	
	/**
	 * Initialise un objet htmlMimeMail pour l'envoi.
	 *
	 * @return htmlMimeMail
	 */
	private function _createMailer() {
		Copix::RequireOnce (COPIX_PATH.'../htmlMimeMail/htmlMimeMail.php');
		$mail =  new htmlMimeMail ();
		$mail->setReturnPath(CopixConfig::get ('|mailFrom'));
		$mail->setFrom('"'.CopixConfig::get ('|mailFromName').'" <'.CopixConfig::get ('|mailFrom').'>');
		$mail->setHeader('X-Mailer', 'COPIX (http://copix.org) with HTML Mime mail class (http://www.phpguru.org)');
		if (CopixConfig::get ('|mailMethod') == 'smtp'){
			$auth = (CopixConfig::get ('|mailSmtpAuth') == '') ? null : CopixConfig::get ('|mailSmtpAuth');
			$pass = (CopixConfig::get ('|mailSmtpPass') == '') ? null : CopixConfig::get ('|mailSmtpPass');
			$hasAuth = ($auth != null);
			$mail->setSMTPParams(CopixConfig::get ('|mailSmtpHost'), null, null, $hasAuth, $auth, $pass);
		}
		return $mail;		
	}
	
	/**
	 * Retourne une en-tête du mail 
	 *
	 * @param htmlMimeMail $mail Mail construit.
	 * @param string $header Nom de l'en-tête.
	 * @return string la valeur de l'en-tête ou une chaîne vide.
	 */
	private function _header(htmlMimeMail $mail, $header) {
		return isset($mail->headers[$header])? $mail->headers[$header] : '';
	}
	
	/**
	 * Sends an email.
	 * @param CopixEMail $copixEMail the mail to send
	 * @param string $fromAdress the expeditor email adress
	 * @param string $fromName the expeditor name
	 * @return boolean (the mail was send or not)
	 */
	public function send (CopixEMail $copixEMail, $fromAdress=null, $fromName=null){
		$mailer = $this->_createMailer();

		// Prépare le mail
		$to = $copixEMail->prepareEmail($mailer, $fromAdress, $fromName);

		$toReturn = false;
		if (intval(CopixConfig::get ('|mailEnabled')) == 1) {
			$mailMethod = CopixConfig::get ('|mailMethod');
				
			// Met un place un error handle pour récuperer les messages d'avertissement
			set_error_handler(array($this, '_handleError'), E_WARNING|E_CORE_WARNING|E_USER_WARNING);
			$oldHtmlErrors = ini_set('html_errors', false);
				
			// Effectue l'envoi
			$toReturn = $mailer->send ($to, $mailMethod);
				
			// Restaure le handler
			ini_set('html_errors', $oldHtmlErrors);
			restore_error_handler();
				
			/// Récupère aussi les erreurs du mailer
			if(isset($mailer->errors)) {
				$this->errors = array_merge($this->errors, $mailer->errors);
			}
				
			$status = $toReturn ? 'SENT' : 'FAILED';
		} else {
			$status = "DISABLED";
		}

		if(intval(CopixConfig::get ('|mailLogging')) == 1) {

			// Construit le message si nécessaire (pour calculer la taille)
			if(!$mailer->is_built) {
				$mailer->buildMessage();
			}

			// Message de base
			$msg = sprintf("%s: %s, from=%s, to=%s, cc=%s, bcc=%s, subject=%s, size=%d",
				date("Y-m-d H:i:s"),
				$status,
				$this->_header($mailer, 'From'),
				(!empty($to) ? join(",",  $to) : ''),
				$this->_header($mailer, 'Cc'),
				$this->_header($mailer, 'Bcc'),
				$this->_header($mailer, 'Subject'),
				strlen($mailer->output)
			);

			// Ajoute les messages d'erreurs s'il y en a
			if(count($this->errors)) {
				$msg .= sprintf(", errors=<%s>", join("|", $this->errors));
			}

			// Récupère la référence à l'appelant
			$me = dirname(__FILE__);
			$logExtra = array();
			foreach(debug_backtrace() as $trace) {
				if($trace['file'] && (substr($trace['file'], 0, strlen($me)) != $me)) {
					$logExtra = $trace;
					break;
				}
			}

			// Ecrit le log
			_log($msg, "email", ($status == "FAILED") ? CopixLog::ERROR : CopixLog::NOTICE, $logExtra);
		}

		return $toReturn;
	}
	
    /**
     * Capture les warnings.
     *
     * @param integer $errno
     * @param string $errstr
     */
    public function _handleError($errno, $errstr) {
    	$this->errors[] = $errstr;
    }	

}
?>