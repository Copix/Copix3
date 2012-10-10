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
class CopixEMail {
    /**
    * Content of the EMail.
    * @var string
    */
    var $message;

    /**
    * Subject
    * @var string
    */
    var $subject;

    /**
    * Recipients
    * @var string
    */
    var $to;

    /**
    * Carbon Copy
    * @var string
    */
    var $cc;

    /**
    * Hidden carbon copy
    * @var string
    */
    var $cci;

    /**
    * Sender
    * @var string
    */
    var $from;
    
    /**
    * Sender name
    * @var string
    */
    var $fromName;
    
    /**
    * Attachments
    * 
    * Array of array ('body'=>$x, 'name'=>$x, 'c_type'=>$x, 'encoding'=>$x)
    *
    * @var associative array
    */
    var $attachments = array ();

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
        $mailer = new CopixEMailer ();
        return $mailer->send ($this, $from, $fromName);
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
	var $textEquivalent;

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
}

/**
* EMail with a text content
* @package copix
* @subpackage utils
*/
class CopixTextEMail extends CopixEMail{
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
}

/**
* The mailer (uses htmlMimeMail to really send e mail)
* @package copix
* @subpackage utils
*/
class CopixEMailer {
    /**
    * Sends an email.
    * @param CopixEMail $copixEMail the mail to send
    * @param string $fromAdress the expeditor email adress
    * @param string $fromName the expeditor name
    * @return boolean (the mail was send or not)
    */
    public function send ($copixEMail, $fromAdress=null, $fromName=null){
    	$mailer = $this->_createMailer ();

        //check if we've been asked not to send the emails.
        if (intval(CopixConfig::get ('|mailEnabled')) !== 1){
            return;
        }

        //check the HTML content, if any.
        if (strtolower (get_class ($copixEMail)) == strtolower ('CopixHTMLEMail')){
            $mailer->setHtml($copixEMail->message, $copixEMail->textEquivalent);
        }else{
            $mailer->setText ($copixEMail->message);
        }

        //Adds attachments
        foreach ($copixEMail->attachments as $attach) {
       		$mailer->addAttachment ($attach['body'], $attach['name'], $attach['c_type'], $attach['encoding']);	
        }
        $mailer->setSubject($copixEMail->subject);
        $mailer->setCc ($copixEMail->cc);
        $mailer->setBcc ($copixEMail->cci);

        $fromAdress = $fromAdress == null ? $copixEMail->from : $fromAdress;
        $fromName =   $fromName == null ? $copixEMail->fromName : $fromName;
        if (CopixConfig::get ('|mailMethod') === "mail"){
            $mailer->setFrom ($fromAdress);
        }else{
            $mailer->setFrom ('"'.$fromName.'" <'.$fromAdress.'>');
        }
        return $mailer->send ((array) $copixEMail->to, CopixConfig::get ('|mailMethod'));
    }

    /**
    * Creates a mailer object
    * @return htmlMimeMail newly created object
    */
    private function  _createMailer () {
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
}
?>