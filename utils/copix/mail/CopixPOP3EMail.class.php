<?php
/**
 * @package copix
 * @subpackage mail
 * @author Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Informations sur un email provenant d'une connexion POP3
 *
 * @package copix
 * @subpackage mail
 */
class CopixPOP3EMail {
	/**
	 * Retour de getBody en HTML
	 */
	const TEXT_HTML = 'TEXT/HTML';

	/**
	 * Retour de getBody en TEXT
	 */
	const TEXT_PLAIN = 'TEXT/PLAIN';

	/**
	 * Indique si le mail est récent
	 *
	 * @var boolean
	 */
	private $_isRecent = false;
	
	/**
	 * Indique si le mail a été lu
	 *
	 * @var boolean
	 */
	private $_isReaded = false;
	
	/**
	 * Indique si le mail contient un drapeau
	 *
	 * @var boolean
	 */
	private $_isFlagged = false;
	
	/**
	 * Indique si on a répondu à ce mail
	 *
	 * @var boolean
	 */
	private $_isAnswered = false;
	
	/**
	 * Indique si le mail est supprimé (mais la corbeille n'est pas encore vidée)
	 *
	 * @var boolean
	 */
	private $_isDeleted = false;
	
	/**
	 * Indique si c'est un brouillon
	 *
	 * @var boolean
	 */
	private $_isDraft = false;
	
	/**
	 * Adressse de l'expéditeur (différence avec sender ?)
	 *
	 * @var CopixPOP3Address
	 */
	private $_from = null;
	
	/**
	 * Adresse de l'expéditeur (différence avec from ?)
	 *
	 * @var CopixPOP3Address
	 */
	private $_sender = null;
	
	/**
	 * Adresses des destinataires
	 *
	 * @var CopixPOP3Address[]
	 */
	private $_to = null;
		
	/**
	 * Destinataires en copie carbone
	 *
	 * @var CopixPOP3Address[]
	 */
	private $_cc = null;
	
	/**
	 * Destinataires en copie cachée
	 *
	 * @var CopixPOP3Address[]
	 */
	private $_bcc = null;
	
	/**
	 * Adresse où on doit répondre
	 *
	 * @var CopixPOP3Address
	 */
	private $_replyTo = null;
	
	/**
	 * ???
	 *
	 * @var CopixPOP3Address
	 */
	private $_returnPath = null;
	
	/**
	 * Sujet
	 *
	 * @var string
	 */
	private $_subject = null;
	
	/**
	 * Identifiant unique du mail, attention n'est pas le retour de imap_uid car ce retour change en fonction de la position du mail ...
	 *
	 * @var string
	 */
	private $_id = null;
	
	/**
	 * Taille du corps du mail
	 *
	 * @var int
	 */
	private $_size = null;
	
	/**
	 * Date d'envoi du mail
	 *
	 * @var int
	 */
	private $_date = null;
	
	/**
	 * Numéro du message
	 *
	 * @var int
	 */
	private $_msgNo = null;
	
	/**
	 * Connection au serveur
	 *
	 * @var Resource
	 */
	private $_connection = null;
	
	public function move ($mailbox) {
		imap_mail_move($this->_connection, $this->_msgNo, $mailbox);
		imap_expunge($this->_connection);
	}
	
	/**
	 * Constructeur
	 *
	 * @param stdClass $pHeaders Informations sur le mail
	 */
	public function __construct ($pConnection, $pHeaders) {
		// voir http://fr.php.net/manual/fr/function.imap-headerinfo.php
		$this->_connection = $pConnection;
		
		// propriétés diverses
		$this->_isRecent = (trim ($this->_getHeader ($pHeaders, 'Recent')) != null);
		$this->_isReaded = ($this->_getHeader ($pHeaders, 'Recent') == 'R');
		$this->_isFlagged = ($this->_getHeader ($pHeaders, 'Flagged') == 'F');
		$this->_isAnswered = ($this->_getHeader ($pHeaders, 'Answered') == 'A');
		$this->_isDeleted = ($this->_getHeader ($pHeaders, 'Deleted') == 'D');
		$this->_isDraft = ($this->_getHeader ($pHeaders, 'Draft') == 'X');
		
		// adresses
		$this->_to = $this->_getAddresses ($pHeaders, 'to');
		$this->_from = $this->_getAddress ($pHeaders, 'from');
		$this->_cc = $this->_getAddresses ($pHeaders, 'cc');
		$this->_bcc = $this->_getAddresses ($pHeaders, 'bcc');
		$this->_replyTo = $this->_getAddress ($pHeaders, 'reply_to');
		if ($this->_replyTo === null) {
			$this->_replyTo = clone ($this->_from);
		}
		$this->_sender = $this->_getAddress ($pHeaders, 'sender');
		$this->_returnPath = $this->_getAddress ($pHeaders, 'return_path');
		
		// autres informations
		$this->_msgNo = trim ($this->_getHeader ($pHeaders, 'Msgno'));
		$this->_subject = $this->_getDecodedText ($this->_getHeader ($pHeaders, array ('subject', 'Subject')));
		$this->_size = intval ($this->_getHeader ($pHeaders, 'Size'));
		$this->_date = intval ($this->_getHeader ($pHeaders, 'udate'));
		
		// on n'utilise pas imap_uid parceque ce numéro change selon la position du mail dans la liste, donc il n'est pas un identifiant du mail
		// bien laisser l'appel à _getID en dernier, car il utilise les infos précédement définies
		$this->_id = $this->_getID ($pHeaders);
	}

	/**
	 * Corrige les problèmes de textes en iso dans des textes en utf8
	 *
	 * @param string $pText
	 * @return string
	 */
	private function _fixEnconding ($pText) {
		$toReturn = str_replace ('Ã©', 'é', $pText);
		$toReturn = str_replace ('Ã¨', 'è', $toReturn);
		$toReturn = str_replace ('Ãª', 'ê', $toReturn);
		return $toReturn;
	}
	
	/**
	 * Retourne une information en particulier, en vérifiant son existance, sinon retourne la valeur par défaut
	 *
	 * @param stdClass $pHeaders Informations sur le mail
	 * @param string $pName Nom de l'information dont on veut la valeur
	 * @param mixed $pDefaultValue Valeur par défaut si l'information demandée n'existe pas
	 * @return string
	 */
	private function _getHeader ($pHeaders, $pName, $pDefaultValue = null) {
		if (!is_array ($pName)) {
			$pName = array ($pName);
		}
		foreach ($pName as $name) {
			if (isset ($pHeaders->$name)) {
				return $pHeaders->$name;
			}
		}
		return $pDefaultValue;
	}
	
	/**
	 * Retourne une adresse si elle existe
	 *
	 * @param stdClass $pHeaders Informations sur le mail
	 * @param string $pName Nom de l'information qui contient l'adresse
	 * @param int $pIndex Numéro de l'adresse à récupérer
	 * @return CopixEMailAddress
	 */
	private function _getAddress ($pHeaders, $pName, $pIndex = 0) {
		if (($addresses = $this->_getHeader ($pHeaders, $pName)) !== null) {
			return $this->_getObjAddress ($addresses[$pIndex]);
		}
		return null;
	}
	
	/**
	 * Retourne une adresse si elle existe
	 *
	 * @param stdClass $pHeaders Informations sur le mail
	 * @param string $pName Nom de l'information qui contient l'adresse
	 * @return CopixEMailAddress[]
	 */
	private function _getAddresses ($pHeaders, $pName) {
		if (($addresses = $this->_getHeader ($pHeaders, $pName)) !== null) {
			$toReturn = array ();
			foreach ($addresses as $address) {
				$toReturn[] = $this->_getObjAddress ($address);
			}
			return $toReturn;
		}
		return null;
	}
	
	/**
	 * Retourne un objet contenant les infos de l'adresse
	 *
	 * @param stdClass $pAddress Informations sur l'adresse
	 * @return CopixEMailAddress
	 */
	private function _getObjAddress ($pAddress) {
		$personnal = (isset ($pAddress->personal)) ? $this->_getDecodedText ($pAddress->personal) : null;
		return new CopixEMailAddress ($pAddress->mailbox . '@' . $pAddress->host, $personnal);
	}
	
	/**
	 * Retourne un identifiant unique et qui ne change pas (contrairement à imap_uid qui change si le mail change de position dans la liste)
	 *
	 * @param stdClass $pHeaders Informations sur le mail
	 * @return string
	 */
	private function _getID ($pHeaders) {
		$id = $this->getSubject () . '_' . $this->getSize () . '_' . $this->getDate ();
		if ($this->getFrom () !== null) {
			$id .= $this->getFrom ()->getFullAddress ();
		}
		return md5 ($id);
	}

	/**
	 * Retourne le texte décodé depuis une chaine codée
	 *
	 * @param string $pText Text codé
	 * @return string
	 */
	private function _getDecodedText ($pText) {
		$toReturn = null;
		$parts = imap_mime_header_decode ($pText);
		foreach ($parts as $part) {
			// on peut avoir un encodage définit à iso, et avoir quand même de l'UTF8, donc on essaye de le détecter tant bien que mal
			$toReturn .= (strtolower ($part->charset) == 'utf-8') ? $part->text : _filter ('utf8')->get ($part->text);
		}
		return $this->_fixEnconding ($toReturn);
	}
	
	/**
	 * Indique si le mail est récent
	 *
	 * @return boolean
	 */
	public function isRecent () {
		return $this->_isRecent;
	}
	
	/**
	 * Indique si le mail a été lu
	 *
	 * @return boolean
	 */
	public function isReaded () {
		return $this->_isReaded;
	}
	
	/**
	 * Indique si le mail contient un drapeau
	 *
	 * @return boolean
	 */
	public function isFlagged () {
		return $this->_isFlagged;
	}
	
	/**
	 * Indique si on a répondu à ce mail
	 *
	 * @return boolean
	 */
	public function isAnswered () {
		return $this->_isAnswered;
	}
	
	/**
	 * Indique si ce mail a été supprimé, mais que la corbeille n'a pas encore été vidée
	 *
	 * @return boolean
	 */
	public function isDeleted () {
		return $this->_isDeleted;
	}
	
	/**
	 * Indique si ce mail est un brouillon
	 *
	 * @return boolean
	 */
	public function isDraft () {
		return $this->_isDraft;
	}
	
	/**
	 * Retourne le numéro du message
	 *
	 * @return int
	 */
	public function getMsgNo () {
		return $this->_msgNo;
	}
	
	/**
	 * Retourne l'adresse de l'expéditeur
	 *
	 * @return CopixEMailAddress
	 */
	public function getFrom () {
		return $this->_from;
	}
	
	/**
	 * Retourne l'adresse de l'expéditeur (différence avec getFrom ?)
	 *
	 * @return CopixEMailAddress
	 */
	public function getSender () {
		return $this->_sender;
	}
	
	/**
	 * Retourne les adresses des destinataires
	 *
	 * @return CopixEMailAddress[]
	 */
	public function getTo () {
		return $this->_to;
	}
	
	/**
	 * Retourne les adresses des destinataires en copie
	 *
	 * @return CopixEMailAddress[]
	 */
	public function getCC () {
		return $this->_cc;
	}
	
	/**
	 * Retourne les adresses des destinataires en copie cachée
	 *
	 * @return CopixEMailAddress[]
	 */
	public function getBCC () {
		return $this->_bcc;
	}
	
	/**
	 * Retourne l'adresse à laquelle on doit répondre
	 *
	 * @return CopixEMailAddress
	 */
	public function getReplyTo () {
		return $this->_replyTo;
	}
	
	/**
	 * Retourne le contenu de Return-path
	 *
	 * @return string
	 */
	public function getReturnPath () {
		return $this->_returnPath;
	}
	
	/**
	 * Retourne le sujet du mail
	 *
	 * @return string
	 */
	public function getSubject () {
		return $this->_subject;
	}
	
	/**
	 * Retourne l'identifiant du mail, attention ce n'est pas le retour de imap_uid qui dépend de la position du mail dans la liste, dont n'identifie pas un mail
	 *
	 * @return string[32]
	 */
	public function getId () {
		return $this->_id;
	}
	
	/**
	 * Retourne la taille du corps du mail
	 *
	 * @return int
	 */
	public function getSize () {
		return $this->_size;
	}
	
	/**
	 * Retourne la date d'envoi du mail au format demandé
	 *
	 * @param string $pFormat Format de la date, si null sera pris dans CopixI18N::getDateTimeFormat ()
	 * @return string
	 */
	public function getDate ($pFormat = null) {
		if ($pFormat === null) {
			$pFormat = CopixI18N::getDateTimeFormat ();
		}
		return date ($pFormat, $this->_date);
	}
	
	/**
	 * Retourne le corps du mail selon $pType
	 * Si la version HTML n'est pas disponible, c'est la version text qui sera retournée
	 *
	 * @return string
	 */
	public function getBody ($pMimeType = CopixPOP3EMail::TEXT_HTML, $pFormat = true) {
		// recherche du body du mail
		$realMimeType = $pMimeType;
		$toReturn = $this->_getPart ($this->getMsgNo (), $pMimeType);
		if ($toReturn === false) {
			$toReturn = $this->_getPart ($this->getMsgNo (), self::TEXT_PLAIN);
			$realMimeType = self::TEXT_PLAIN;
		}
		
		// si on a un body en texte et qu'on veut formater le retour
		if ($realMimeType == self::TEXT_PLAIN && $pFormat) {
			$toReturn = nl2br ($toReturn);
			$toReturn = preg_replace ('/([^\w\/])(www\.[a-z0-9\-]+\.[a-z0-9\-]+)/i', '$1http://$2', $toReturn);
			$toReturn = preg_replace ('/([\w]+:\/\/[\w-?&;#~=\.\/\@]+[\w\/])/i', '<a target="_blank" href="$1" class="email_text_link">$1</a>', $toReturn);
			$toReturn = preg_replace ('/([\w-?&;#~=\.\/]+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,3}|[0-9]{1,3})(\]?))/i', '<a href="mailto:$1">$1</a>', $toReturn);
		}
		
		return $this->_fixEnconding (_filter ('utf8')->get ($toReturn));
	}
	
	
	/**
	 * Retourne le corps du mail selon $pType
	 * Si la version HTML n'est pas disponible, c'est la version text qui sera retournée
	 *
	 * @return string
	 */
	public function getEML ($pMimeType = CopixPOP3EMail::TEXT_HTML, $pFormat = true) {
		$text = imap_fetchbody ($this->_connection, $this->getMsgNo(), null);
		return $text;
	}
	
	
	/**
	 * Retourne le type MIME d'une structure de mail
	 *
	 * @param stdClass $pStructure Structure du mail, retour de imap_fetchstructure
	 * @return string
	 * @see http://www.linuxscope.net/articles/mailAttachmentsPHP.html
	 */
	private function _getMimeType (&$pStructure) {
		$primary_mime_type = array ('TEXT', 'MULTIPART','MESSAGE', 'APPLICATION', 'AUDIO', 'IMAGE', 'VIDEO', 'OTHER');
		if ($pStructure->subtype) {
			return $primary_mime_type[(int)$pStructure->type] . '/' . $pStructure->subtype;
		}
		return 'TEXT/PLAIN';
	}

	/**
	 * Retourne la partie demandée
	 * 
	 * @param int $pMsgNo Numéro du message
	 * @param string $pMimeType Type MIME à retourner
	 * @param stdClass $pStructure Structure retournée par imap_fetchstructure
	 * @param int $pPartNumber Numéro de la partie à retourner
	 * @return string
	 * @see http://www.linuxscope.net/articles/mailAttachmentsPHP.html
	 */
	private function _getPart ($pMsgNo, $pMimeType, $pStructure = null, $pPartNumber = false) {
		if ($pStructure == null) {
			$pStructure = imap_fetchstructure ($this->_connection, $pMsgNo);
		}
		if ($pStructure) {
			// mail en une seule partie
			if ($pMimeType == $this->_getMimeType ($pStructure)) {
				if ($pPartNumber===false) {
					$pPartNumber = '1';
				}
				$text = imap_fetchbody ($this->_connection, $pMsgNo, $pPartNumber);
				if ($pStructure->encoding == 3) {
					return imap_base64 ($text);
				} else if ($pStructure->encoding == 4) {
					return imap_qprint ($text);
				} else {
					return $text;
				}
			}

			// mail en plusieurs parties
			if ($pStructure->type == 1) {
				while (list ($index, $subStructure) = each ($pStructure->parts)) {
					$prefix = '';
					if ($pPartNumber) {
						$prefix = $pPartNumber . '.';
					}
					$data = $this->_getPart ($pMsgNo, $pMimeType, $subStructure, $prefix . ($index + 1));
					if ($data !== false && isset ($subStructure->parameters)) {
						foreach ($subStructure->parameters as $parameter) {
							if (strtolower ($parameter->attribute) == 'charset' && strtolower ($parameter->value) == 'iso-8859-1') {
								$data = utf8_encode ($data);
							}
						}
					}
					if ($data) {
						return $data;
					}
				}
			}
		}
		return false;
	}
}
