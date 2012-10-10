<?php
/**
 * @package copix
 * @subpackage utils
 * @author Croës Gérald, Favre Brice
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Permet d'envoyer des paquets
 * 
 * @package copix
 * @subpackage utils
 */
class CopixHTTPClientRequest {
	/**
	 * Indique à setPost d'écraser les valeurs déjà passées
	 */
	const REPLACE = 0;
	
	/**
	 * Indique à setPost de rajouter les nouvelles valeurs
	 */
	const ADD = 1;
	
	/**
	 * Indique à setPost de rajouter les nouvelles valeurs et d'ecraser les anciennes si elles ont le même nom
	 */
	const ADD_REPLACE = 2;
	
	/**
	 * Le tableau des paramètre à passer en post
	 */
	private $_post = array ();

	/**
	 * Envoi d'un fichier
	 */
	private $_file = null;

	/**
	 * L'url ou aller
	 */
	private $_url= '';

	/**
	 * Indique si l'on souhaite suivre les redirections
	 */
	private $_followRedirect = false;

	/**
	 * Durée d'attente avant expiration de la requete
	 */
	private $_timeout = 0;

	/**
	 * Indique si on ignore ou non la vérification du certificat
	 */
	private $_doIgnoreSslVerification = false;

	/**
	 * Tableaux d'entête 
	 */
	private $_header = array ();

	/**
	 * Paramètre cookie
	 */
	private $_cookie = '';

	/**
	 * Paramètre pour savoir si on veut choisir l'utilisation du proxy
	 */
	private $_proxy = false;

	/**
	 * IP du proxy
	 *
	 * @var string
	 */
	private $_proxyHost = null;

	/**
	 * Port du proxy
	 *
	 * @var int
	 */
	private $_proxyPort = null;

	/**
	 * Identifiant pour se connecter au proxy
	 *
	 * @var string
	 */
	private $_proxyUser = null;

	/**
	 * Mot de passe pour se connecter au proxy
	 *
	 * @var string
	 */
	private $_proxyPassword = null;

	/**
	 * Indique si le proxy est un tunnel HTTP
	 *
	 * @var boolean
	 */
	private $_proxyIsHTTPTunnel = true;

	/**
	 * Paramètre Interface
	 */
	private $_interface = '';
	
	/**
	 * Le login HTTP à utiliser pour l'authentification basique
	 *
	 * @var string
	 */
	private $_httpUser = null;
	
	/**
	 * Le password HTTP à utiliser pour l'authentification basique
	 *
	 * @var string
	 */	private $_httpPassword = null;

	/**
	 * Construction d'une requête
	 * @param	$pUrl	string	adresse sur laquelle envoyer le paquet.
	 */
	public function __construct ($pUrl) {
		if (!function_exists ('curl_init')) {
			throw new Exception ('L\'extension CURL est nécessaire pour pouvoir utiliser CopixHTTClient.');
		}
		$this->setUrl ($pUrl);
		$this->getProxyStatus ();
	}

	/**
	 * Assignation d'une URL
	 *
	 * @param	$pUrl	string	remplace l'url définie dans le constructeur 
	 */
	public function setUrl ($pUrl) {
		$this->_url = $pUrl;
	}

	/**
	 * Retourne si le proxy est activé ou pas
	 * @param void
	 * @return void
	 */
	public function getProxyStatus () {
		if(CopixConfig::get('default|proxyEnabled')) {
			$this->_proxy = true;
		} else {
			$this->_proxy = false;
		}
	}

	/**
	 * Permet de définir les paramètres à POSTer
	 *
	 * @param 	$pArray	array	Différents champs du POST
	 * @param int REPLACE, ADD_REPLACE, ADD (ancien comportement par défaut)
	 */
	public function setPost ($pArray, $pReplace = self::ADD) {
		switch ($pReplace) {
			case self::REPLACE:
				$this->_post = $pArray;
				break;
			case self::ADD:
				$this->_post = array_merge ($pArray, $this->_post);
				break;
			case self::ADD_REPLACE:
				$this->_post = array_merge ($this->_post, $pArray);
				break;
		}
	}

	/**
	 * Permet de définir un fichier à poster
	 *
	 * @param 	$pFile	string	Nom du fichier à poster (il doit exister) 
	 */
	public function setFile ($pFile) {
		$this->_file = $pFile;
	}


	/**
	 * Fixe la durée avant expiration de la requete
	 *
	 * @param int $pTimeout durée avant expiration de la requete 
	 */
	public function setTimeout ($pTimeout) {
		$this->_timeout = $pTimeout;
	}

	/**
	 * Permet de spécifier si on souhaite ignorer la vérification du certificat SSL ou non
	 *
	 * @param boolean $pIgnoreSslVerification
	 */
	public function setIgnoreCertificate ($pIgnoreSslVerification) {
		$this->_doIgnoreSslVerification = $pIgnoreSslVerification;
	}

	/**
	 * Permet de spécifier une entête de fichier
	 *
	 * @param string $pNameHeader Nom de l'entête
	 * @param string $pContentHeader Contenu de l'entête
	 */
	public function setHeader ($pNameHeader, $pContentHeader) {
		$this->_header[] = $pNameHeader.': '.$pContentHeader;
	}

	/**
	 * Permet d'instancier un cookie
	 *
	 * @param string valeur du Cookie
	 */
	public function setCookie ($pCookie) {
		$this->_cookie = $pCookie;
	}

	/**
	 * Permet de définir si on veut utiliser le proxy ou pas
	 *
	 * @param bool $pStatus true si on utilise ou false sinon
	 * @param boolean $pAutoConfigure Indique si on veut configurer le proxy avec celui configuré au niveau du framework
	 */
	public function setProxy ($pStatus, $pAutoConfigure = true) {
		$this->_proxy = $pStatus;
		if ($pAutoConfigure) {
			$this->setProxyHost (CopixConfig::get('default|proxyHost'));
			$this->setProxyPort (CopixConfig::get('default|proxyPort'));
			$this->setProxyUser (CopixConfig::get('default|proxyUser'));
			$this->setProxyPassword (CopixConfig::get('default|proxyPass'));
		}
	}

	/**
	 * Définit l'IP du proxy
	 *
	 * @param string $pHost IP
	 */
	public function setProxyHost ($pHost) {
		$this->_proxyHost = $pHost;
	}

	/**
	 * Retourne l'IP du proxy
	 *
	 * @return string
	 */
	public function getProxyHost () {
		return $this->_proxyHost;
	}

	/**
	 * Définit le port du proxy
	 *
	 * @param int $pPort
	 */
	public function setProxyPort ($pPort) {
		$this->_proxyPort = intval ($pPort);
	}

	/**
	 * Retourne le port du proxy
	 *
	 * @return string
	 */
	public function getProxyPort () {
		return $this->_proxyPort;
	}

	/**
	 * Définit le login pour se connecter au proxy
	 *
	 * @param string $pUser
	 */
	public function setProxyUser ($pUser) {
		$this->_proxyUser = $pUser;
	}

	/**
	 * Retourne le login pour se connecter au proxy
	 *
	 * @return string
	 */
	public function getProxyUser () {
		return $this->_proxyUser;
	}

	/**
	 * Définit le mot de passe pour se connecter au proxy
	 *
	 * @param string $pPassword
	 */
	public function setProxyPassword ($pPassword) {
		$this->_proxyPassword = $pPassword;
	}

	/**
	 * Retourne le mot de passe pour se connecter au proxy
	 *
	 * @return string
	 */
	public function getProxyPassword () {
		return $this->_proxyPassword;
	}

	/**
	 * Définit si le proxy est un tunnel HTTP
	 *
	 * @param boolean $pIsProxyHTTPTunnel
	 */
	public function setIsProxyHTTPTunnel ($pIsProxyHTTPTunnel) {
		$this->_proxyIsHTTPTunnel = $pIsProxyHTTPTunnel;
	}

	/**
	 * Indique si le proxy est un tunnel HTTP
	 *
	 * @return boolean
	 */
	public function proxyIsHTTPTunnel () {
		return $this->_proxyIsHTTPTunnel;
	}

	/**
	 * Indique si la vérification du certificat SSL sera faite ou non
	 *
	 * @return boolean vrai si le certificat est vérifié
	 */
	public function getIgnoreCertificate () {
		return $this->_doIgnoreSslVerification;
	}

	/**
	 * Renvoie la durée avant expiration de la requete
	 *
	 * @return int durée avant expiration de la requete
	 */
	public function getTimeout () {
		return $this->_timeout;
	}

	/**
	 * Renvoie le paramètre proxy
	 * @param void
	 * @return bool : Vrai si le proxy est activé ou sinon faux
	 */
	public function getProxy() {
		return $this->_proxy;
	}

	/**
	 * Récupération des paramètre demandés en POS
	 *
	 * @return array	les éléments du formulaire
	 */
	public function getPost () {
		return $this->_post;
	}

	/**
	 * Récupération de l'url demandée
	 *
	 * @return string	l'adresse demandé
	 */
	public function getUrl () {
		return $this->_url;
	}

	/**
	 * On indique si l'on souhaite ou non suivre les demandes de redirection HTTP
	 *
	 * @param boolean $pBoolean
	 */
	public function setFollowRedirect ($pBoolean) {
		$this->_followRedirect = (bool) $pBoolean;
	}

	/**
	 * Indique s'il faut suivre ou non les demandes de redirections HTTP
	 *
	 * @return bool
	 */
	public function getFollowRedirect () {
		return $this->_followRedirect;
	}

	/**
	 * Retourne le nom du fichier à envoyer
	 *
	 * @return string
	 */
	public function getFile () {
		return $this->_file;
	}

	/**
	 * Retourne l'entête définie par l'utilisateur
	 *
	 * @return array
	 */
	public function getHeader () {
		return $this->_header;
	}

	/**
	 * Retour la valeur du cookie définie par l'utilisateur
	 *
	 * @return string
	 */
	public function getCookie () {
		return $this->_cookie;
	}

	/**
	 * On indique si l'on veut utiliser une interface différente de celle configurée 
	 *
	 * @param string $pInterface
	 */
	public function setInterface ($pInterface) {
		$this->_interface = $pInterface;
	}

	/**
	 * Retourne l'interface du client HTTP
	 *
	 * @return string
	 */
	public function getInterface () {
		return $this->_interface;
	}
	
	/**
	 * Définission des paramètres d'authentification HTTP
	 *
	 * @param string $pUser
	 * @param string $pPassword
	 */
	public function setHTTPBasicAuthentication ($pUser, $pPassword) {
		$this->_httpUser = $pUser;
		$this->_httpPassword = $pPassword;
	}
	
	/**
	 * Retourne le couple login / password pour l'authentification HTTP
	 *
	 * @return array (login=>value, password=>value)
	 */
	public function getHTTPBasicAuthentication () {
		return array ('login' => $this->_httpUser, 'password' => $this->_httpPassword);
	}
	
	/**
	 * Indique s'il existe des paramètres d'authentification HTTP
	 *
	 * @return boolean
	 */
	public function isHTTPBasicAuthenticated () {
		return $this->_httpPassword !== null && $this->_httpUser !== null; 
	}
}