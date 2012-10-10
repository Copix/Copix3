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
 * Connexion à un serveur POP3
 *
 * @package copix
 * @subpackage mail
 */
class CopixPOP3Connection {
	/**
	 * Nom de domaine
	 *
	 * @var string
	 */
	private $_host = null;
	
	/**
	 * Port
	 *
	 * @var int
	 */
	private $_port = null;
	
	/**
	 * Nom d'utilisateur
	 *
	 * @var string
	 */
	private $_user = null;
	
	/**
	 * Mot de passe
	 *
	 * @var string
	 */
	private $_password = null;
	
	/**
	 * Chaine de connexion utilisée pour la copnnexion au serveur
	 *
	 * @var string
	 */
	private $_connectionString = null;
	
	/**
	 * Identifiant de la connexion
	 *
	 * @var Resource
	 */
	private $_connection = null;
	
	/**
	 * Constructeur
	 *
	 * @param string $pHost Nom de domaine
	 * @param string $pUser Nom d'utilisateur
	 * @param string $pPassword Mot de passe
	 * @param int $pPort Port
	 */
	public function __construct ($pHost, $pUser, $pPassword, $pPort = 110, $pOptions = '/pop3', $pFolder = '') {
		$this->connect ($pHost, $pUser, $pPassword, $pPort, $pOptions, $pFolder);
	}
	
	/**
	 * Connexion au serveur
	 *
	 * @param string $pHost Nom de domaine
	 * @param string $pUser Nom d'utilisateur
	 * @param string $pPassword Mot de passe
	 * @param int $pPort Port
	 * @throws CopixPOP3Exception Les fonctions imap ne sont pas installées
	 * @throws CopixPOP3Exception Connexion au serveur impossible
	 */
	public function connect ($pHost, $pUser, $pPassword, $pPort = 110, $pOptions = '/pop3', $pFolder = '') {
		$this->_host = $pHost;
		$this->_user = $pUser;
		$this->_password = $pPassword;
		$this->_port = $pPort;
		if ($pPort != '' && $pPort[0] != ':') {
			$pPort = ':' . $pPort;
		}
		$this->_connectionString = '{' . $pHost . $pOptions . $pPort . '}' . $pFolder;
		
		if (!function_exists ('imap_open')) {
			throw new CopixPOP3Exception ($this, 'Les fonctions imap ne sont pas installées.', CopixPOP3Exception::IMAP_NOT_INSTALLED);
		}
		if (!$this->_connection = imap_open ($this->_connectionString, $pUser, $pPassword)) {
			$this->_connection = null;
			throw new CopixPOP3Exception ($this, 'Connexion au serveur POP3 "' . $pUser . '@' . $pHost . ':' . $pPort . '" impossible.', CopixPOP3Exception::CONNECTION_ERROR);
		}
	}
	
	/**
	 * Indique si on est binn connecté au serveur
	 *
	 */
	public function isConnected () {
		return ($this->_connection !== null);
	}
	
	public function moveMail ($pMails, $pMailbox) {
		//_dump($pMails, $pMailbox);
		imap_mail_move($this->_connection, $pMails, $pMailbox);
		imap_expunge($this->_connection);
	}
	
	public function makeMailbox ($pMailbox) {
		imap_createmailbox($this->_connection, $this->_connectionString.$pMailbox);
	}
	
	/**
	 * Certifie qu'on est connecté, lève une exception si on ne l'est pas
	 *
	 * @throws CopixPOP3Exception Aucune connexion à un serveur POP3 en cours
	 */
	public function assertConnected () {
		if (!$this->isConnected ()) {
			throw new CopixPOP3Exception ($this, 'Aucune connexion à un serveur POP3 en cours.', CopixPOP3Exception::NOT_CONNECTED);
		}
	}
	
	/**
	 * Déconnection
	 *
	 * @param int $pFlag Voir la documentation sur imap_close (http://fr2.php.net/imap_close)
	 * @return boolean
	 */
	public function disconnect ($pFlag = null) {
		if ($this->isConnected ()) {
			return imap_close ($this->_connection, $pFlag);
		}
	}
	
	/**
	 * Retourne le nom de domaine
	 *
	 * @return string
	 */
	public function getHost () {
		return $this->_host;
	}
	
	/**
	 * Retourne le port
	 *
	 * @return int
	 */
	public function getPort () {
		return $this->_port;
	}
	
	/**
	 * Retourne l'utilisateur
	 *
	 * @return string
	 */
	public function getUser () {
		return $this->_user;
	}
	
	/**
	 * Retourne le mot de passe
	 *
	 * @return string
	 */
	public function getPassword () {
		return $this->_password;
	}
	
	/**
	 * Retourne la chaine de connexion utilisée
	 *
	 * @return string
	 */
	public function getConnectionString () {
		return $this->_connectionString;
	}
	
	/**
	 * Retourne la liste des mails
	 *
	 * @return CopixPOP3Mail[]
	 */
	public function getMails () {
		$this->assertConnected ();
		
		$toReturn = array ();
		for ($boucle = 1; $boucle <= imap_num_msg ($this->_connection); $boucle++) {
			$toReturn[] = new CopixPOP3EMail ($this->_connection, imap_headerinfo ($this->_connection, $boucle));
		}
		return $toReturn;
	}
	
	/**
	 * Retourne un mail en particulier
	 *
	 * @param string $pId Identifiant du mail
	 * @return CopixPOP3Mail
	 */
	public function getMail ($pId) {
		$this->assertConnected ();
		
		foreach ($this->getMails () as $mail) {
			if ($mail->getId () == $pId) {
				return $mail;
			}
		}

		throw new CopixPOP3Exception ($this->_connection, 'Le mail "' . $pId . '" n\'existe pas.');
	}
	
	/**
	 * Supprime un mail
	 *
	 * @param string $pId Identifiant du mail
	 */
	public function delete ($pId) {
		$this->assertConnected ();

		$mail = $this->getMail ($pId);
		imap_delete ($this->_connection, $mail->getMsgNo ());
		imap_expunge ($this->_connection);
	}
	
	/**
	 * Retourne les alertes générées par les appels aux fonctions imap. Après appel, les alertes sont vidées
	 *
	 * @return mixed
	 */
	public function getAlerts () {
		if ($this->isConnected ()) {
			// on test si la fonction existe parceque CopixPOP3Exception appelle cette méthode, et on a une exception sur le test d'existante de ces méthodes
			return (function_exists ('imap_alerts')) ? imap_alerts () : null;
		}
	}
	
	/**
	 * Retourne les erreurs générées par les appels aux fonctions imap. Après appel, les erreurs sont vidées
	 *
	 * @return mixed
	 */
	public function getErrors () {
		if ($this->isConnected ()) {
			// on test si la fonction existe parceque CopixPOP3Exception appelle cette méthode, et on a une exception sur le test d'existante de ces méthodes
			return (function_exists ('imap_errors')) ? imap_errors () : null;
		}
	}
	
	/**
	 * Retourne l'erreur générée par la dernière requête imap. Après l'appel, les erreurs ne sont pas vidées
	 *
	 * @return string
	 */
	public function getLastError () {
		if ($this->isConnected ()) {		
			return imap_last_error ();
		}
	}
}
