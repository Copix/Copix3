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
 * Structure de base pour les classes d'email, se base sur htmlMimeMail
 *
 * @package		copix
 * @subpackage	utils
 */
abstract class CopixEMail {
	/**
	 * Contenu du mail
	 *
	 * @var string
	 */
	protected $_message;

	/**
	 * Sujet
	 *
	 * @var string
	 */
	private $_subject;

	/**
	 * Destinataire
	 *
	 * @var string
	 */
	private $_to;

	/**
	 * Copie carbone
	 *
	 * @var string
	 */
	private $_cc;

	/**
	 * Copie carbone cachée
	 *
	 * @var string
	 */
	private $_cci;

	/**
	 * Expéditeur
	 *
	 * @var string
	 */
	private $_from;

	/**
	 * Nom de l'expéditeur
	 *
	 * @var string
	 */
	private $_fromName;

	/**
	 * Pièces jointes
	 * Array of array ('body' => $x, 'name' => $x, 'c_type' => $x, 'encoding' => $x)
	 *
	 * @var array
	 */
	private $_attachments = array ();

	/**
	 * Erreurs du dernier envoi de mail
	 *
	 * @var array
	 */
	private $_errors = array ();

	/**
	 * Constructeur
	 *
	 * @param string $pTo Adresse du destinataire, séparer par des ; pour plusieurs adresses
	 * @param string $pCC Copie carbone, séparer par des ; pour plusieurs adresses
	 * @param string $pCCI Copix carbone cachée, séparer par ; pour plusieurs adresses
	 * @param string $pSubject Sujet
	 * @param string $pMessage Contenu du mail
	 */
	public function __construct ($pTo, $pCC, $pCCI, $pSubject, $pMessage) {
		$this->_from = CopixConfig::get ('|mailFrom');
		$this->_fromName = CopixConfig::get ('|mailFromName');
		$this->_to = (is_array($pTo)) ? implode(',', $pTo) : $pTo;
		$this->_cc = (is_array($pCC)) ? implode(',', $pCC) : str_replace (';', ',', $pCC);
		$this->_cci = (is_array($pCCI)) ? implode(',', $pCCI) : str_replace (';', ',', $pCCI);
		$this->_message = $pMessage;
		$this->_subject = $pSubject;
	}

	/**
	 * Envoi de l'e-mail
	 *
	 * @param string $pFrom Adresse de l'expéditeur
	 * @param string $pFromName Nom de l'expéditeur
	 * @param string $pArHeader Tableau de headers particuliers
	 * @return boolean
	 */
	public function send ($pFrom = null, $pFromName = null, $pArHeader = null) {
		$sender = new CopixEMailer ();
		$toReturn = $sender->send ($this, $pFrom, $pFromName, $pArHeader);
		$this->_errors = $sender->getErrors ();
		return $toReturn;
	}

	/**
	 * Indique si on peut envoyer un e-mail avec la configuration actuelle
	 *
	 * @return CopixErrorObject
	 */
	public function check () {
		$error = new CopixErrorObject ();
		if (trim ($this->_to === null)) {
			$error->addError ('to', _i18n ('copix:copixemail.error.emptyTo'));
		} else {
			$this->_to = str_replace (';', ',', $this->_to);
			$dests = explode (',', $this->_to);
			foreach ($dests as $to) {
				try {
					CopixFormatter::getMail ($to);
				} catch (Exception $e) {
					$error->addError ($to, $e->getMessage ());
				}
			}
		}
		if ($this->_from === null) {
			$error->addError ('from', 'copix:copixemail.error.emptyFrom');
		}
		return $error;
	}

	/**
	 * Ajoute une pièce jointe
	 *
	 * @param binary $pFileData Contenu de la pièce jointe
	 * @param string $pFileName Nom du fichier
	 * @param string $pMimeType Type mime (c_type dans le header du mail)
	 * @param string $pEncoding Encodage du contenu
	 */
	public function addAttachment ($pFileData, $pFileName = '', $pMimeType = 'application/octet-stream', $pEncoding = 'base64') {
		Copix::RequireOnce (COPIX_PATH.'../htmlMimeMail/htmlMimeMail.php');
		$this->_attachments[] = new attachment ($pFileData, $pFileName, $pMimeType, iEncodingFactory::getIEncoding ($pEncoding));
	}

	/**
	 * Ajoute les informations stockées dans cette classe à $pMail
	 *
	 * @param htmlMimeMail $pMail Object htmlMimeMail auquel on veut ajouter les informations
	 * @param string $pFromAdress Adresse de l'expéditeur, sera prise dans la configuration '|mailFrom' si == null
	 * @param string $pFromName Nom de l'expéditeur, sera prit dans la configuration '|mailFromName' si == null
	 * @return array Adresses de destination
	 */
	public function prepareEmail (htmlMimeMail $pMail, $pFromAdress, $pFromName) {
		// Adds attachments
		foreach ($this->_attachments as $attach) {
			$pMail->addAttachment ($attach);
		}
		
		if (CopixI18N::getCharset () === 'UTF-8' && extension_loaded("mbstring") && !mb_check_encoding($this->_message, "UTF-8")){
			$this->_message = utf8_encode ($this->_message);
		}

		// Subject, To, Cc
		$pMail->setSubject ($this->_subject);
		$to = (array)$this->_to;
		if (!empty ($this->_cc)) {
			$pMail->setCc ($this->_cc);
		}
		
		// Bcc
		$bcc = $this->_cci;
		$alwaysBcc = CopixConfig::get ('|mailAlwaysBcc');
		if (!empty ($alwaysBcc)) {
			$bcc = (empty ($bcc)) ? $alwaysBcc : "$alwaysBcc; $bcc";
		}
		if (!empty ($bcc)) {
			$pMail->setBcc ($bcc);
		}
		
		// Adresse de retour
		$pFromAdress = ($pFromAdress == null) ? $this->_from : $pFromAdress;
		$pFromName = ($pFromName == null) ? $this->_fromName : $pFromName;
		$pMail->setFrom ('"' . $pFromName . '" <' . $pFromAdress . '>');
		$pMail->setReturnPath ($pFromAdress);
		return $to;
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