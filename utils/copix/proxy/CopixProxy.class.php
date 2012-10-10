<?php
/**
 * @package copix
 * @subpackage proxy
 * @author Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Gestion d'un proxy
 * 
 * @package copix
 * @subpackage proxy
 */
class CopixProxy {
	/**
	 * Identifiant du proxy
	 * 
	 * @var string
	 */
	private $_id = null;
	
	/**
	 * Indique si le proxy est activé
	 * 
	 * @var bolean
	 */
	private $_enabled = false;
	
	/**
	 * Adresse
	 * 
	 * @var string
	 */
	private $_host = null;
	
	/**
	 * Port
	 * 
	 * @var string
	 */
	private $_port = null;
	
	/**
	 * Utilisateur
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
	 * Adresses pour lesquelles le proxy n'est pas disponible
	 * 
	 * @var array
	 */
	private $_notForHosts = array ();
	
	/**
	 * Adresses pour lesquelles le proxy est disponible
	 * 
	 * @var array
	 */
	private $_forHosts = array ();
	
	/**
	 * Retourne le proxy à utiliser pour atteindre l'adresse $pHost
	 *
	 * @param string $pHost Adresse à atteindre
	 * @return CopixProxy
	 */
	public static function getForHost ($pHost) {
		self::_validHost ($pHost);
		foreach (CopixConfig::instance ()->copixproxy_getProxys () as $proxy) {
			if ($proxy->isForHost ($pHost)) {
				return $proxy;
			}
		}
		return null;
	}
	
	/**
	 * Retourne le proxy d'identifiant $pId
	 *
	 * @param string $pId Identifiant du proxy
	 * @return CopixProxy
	 */
	public static function get ($pId) {
		if (!self::exists ($pId)) {
			throw new CopixProxyException (_i18n ('copix:copixproxy.error.notFound', $pId), CopixProxyException::NOT_FOUND);
		}
		return $this->_copixproxy_proxys[$pId];
	}
	
	/**
	 * Indique si un identifiant de proxy existe
	 *
	 * @param string $pId Identifiant de proxy
	 * @return boolean
	 */
	public static function exists ($pId) {
		$proxys = CopixConfig::instance ()->copixproxy_getProxys ();
		return (isset ($proxys[$pId]));
	}

	/**
	 * Valide un tableau d'adresses
	 *
	 * @param string[] $pHosts Adresses
	 * @throws CopixProxyException Le paramètre $pHosts n'est pas un tableau
	 */
	private static function _validHosts ($pHosts) {
		if (!is_array ($pHosts)) {
			throw new CopixProxyException (_i18n ('copix:copixproxy.error.invalidHostsType', $pHosts), CopixProxyException::INVALID_HOSTS_TYPE);
		}
		foreach ($pHosts as $host) {
			self::_validHost ($host);
		}
	}
	
	/**
	 * Valide une adresse
	 *
	 * @param string $pHost Adresse à valider
	 * @throws CopixProxyException Adresse invalide, code CopixProxyException::INVALID_HOST
	 */
	private static function _validHost ($pHost) {
		if (strpos ($pHost, ' ') !== false) {
			throw new CopixProxyException (_i18n ('copix:copixproxy.error.invalidHost', $pHost), CopixProxyException::INVALID_HOST);
		}
	}
	
	/**
	 * Constructeur
	 * 
	 * @param string $pId Identifiant
	 * @param string $pHost Adresse
	 * @param int $pPort Port
	 * @param string $pUser Utilisateur
	 * @param string $pPassword Mot de passe
	 * @param boolean $pEnabled Indique si le proxy est activé
	 * @param array $pNotForHosts Adresses pour lesquelles on n'utilise pas le proxy
	 * @param array $pForHosts Adresses pour lesquelles on utilise le proxy
	 * @throws CopixProxyException Identifiant invalide, code CopixProxyException::INVALID_ID
	 * @throws CopixProxyException Port invalide, code CopixProxyException::INVALID_PORT
	 */
	public function __construct ($pId, $pHost, $pPort, $pUser = null, $pPassword = null, $pEnabled = true, $pNotForHosts = array (), $pForHosts = array ()) {
		if ($pId == null || !preg_match ('/^[a-zA-Z0-9_]/', $pId)) {
			throw new CopixProxyException (_i18n ('copix:copixproxy.error.invalidId', $pId), CopixProxyException::INVALID_ID);
		}
		$this->_id = $pId;
		CopixProxy::_validHost ($pHost);
		$this->_host = $pHost;
		if (intval ($pPort) != $pPort) {
			throw new CopixProxyException (_i18n ('copix:copixproxy.error.invalidPort', $pPort), CopixProxyException::INVALID_PORT);
		}
		$this->_port = intval ($pPort);
		if (!is_string ($pUser)) {
			throw new CopixProxyException (_i18n ('copix:copixproxy.error.invalidUser', $pUser), CopixProxyException::INVALID_USER);
		}
		$this->_user = $pUser;
		if (!is_string ($pPassword)) {
			throw new CopixProxyException (_i18n ('copix:copixproxy.error.invalidPassword', $pPassword), CopixProxyException::INVALID_PASSWORD);
		}
		$this->_password = $pPassword;
		$this->_enabled = (boolean)$pEnabled;
		CopixProxy::_validHosts ($pNotForHosts);
		$this->_notForHosts = $pNotForHosts;
		CopixProxy::_validHosts ($pForHosts);
		$this->_forHosts = $pForHosts;
	}
	
	/**
	 * Indique si ce proxy est activé
	 * 
	 * @return boolean
	 */
	public function isEnabled () {
		return $this->_enabled;
	}
	
	/**
	 * Indique si le proxy est configuré pour l'adresse $pHost
	 * 
	 * @param string $pHost Adresse
	 * @return boolean
	 */
	public function isForHost ($pHost) {
		if ($this->_enabled === false) {
			return false;
		}
		foreach ($this->_notForHosts as $host) {
			if (strlen ($host) <= strlen ($pHost) && substr ($pHost, strlen ($pHost) - strlen ($host)) == $host) {
				return false;
			}
		}
		if (count ($this->_forHosts) > 0) {
			foreach ($this->_forHosts as $host) {
				if (strlen ($host) <= strlen ($pHost) && substr ($pHost, strlen ($pHost) - strlen ($host)) == $host) {
					return true;
				}
			}
			return false;
		}
		return true;
	}
	
	/**
	 * Retourne l'identifiant
	 * 
	 * @return string
	 */
	public function getId () {
		return $this->_id;
	}
	
	/**
	 * Retourne l'adresse
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
	 * Retourne la liste des adresses pour lesquelles le proxy n'est pas utilisable
	 * 
	 * @return array
	 */
	public function getNotForHosts () {
		return $this->_notForHosts;
	}
	
	/**
	 * Retourne la liste des adresses pour lesquelles le proxy est utilisable
	 * 
	 * @return array
	 */
	public function getForHosts () {
		return $this->_forHosts;
	}
}