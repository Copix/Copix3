<?php
/**
 * @package		copix
 * @subpackage	utils
 * @author		Croes Gérald, Steevan BARBOYON
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Gestion de la session
 *
 * @package		copix
 * @subpackage	utils
 */
class CopixSession {
	/**
	 * Cache de copixsession_key, pour ne pas faire trop d'appels au singleton de CopixConfig
	 *
	 * @var string
	 */
	private static $_copixsession_key = null;
	
	/**
	 * Indicateur de lancement de la session
	 *
	 * @var boolean
	 */
	private static $_started = false;
	
	/**
	 * Indique si la vérification de sécurité a été effectuée.
	 *
	 * @var boolean
	 */
	private static $_secure;
	
	/**
	 * Retourne le nom de la clef où sont stockées les informations de CopixSession
	 * Raccourci vers CopixConfig::instance ()->copixsession_key
	 *
	 * @return string
	 */
	private static function _getKey () {
		if (self::$_copixsession_key === null) {
			// | fait planter la session si c'est la toute première clef de $_SESSION
			self::$_copixsession_key = str_replace ('|', '---', CopixConfig::instance ()->copixsession_key);
		}
		return self::$_copixsession_key;
	}
	
	/**
	 * Indique si la session a été lancée.
	 */
	public static function started (){
		return self::$_started;
	}
	
	/**
	 * Démarrage de la session
	 *
	 * @param string $pId Identifiant de session
	 */
	public static function start ($pId = null) {
		if ($pId === null) {
			$pId = CopixConfig::instance ()->sessionName;
		}
		session_name ($pId);
		session_start ();
		self::$_started = true;
	}

	/**
	 * Démarrage de la session si besoin.
	 * 
	 * @param string le nom de la session si on doit démarrer $pId
	 */
	public static function startIfNeeded ($pId = null) {
	    if (! self::$_started){
	        self::start($pId);
	    }	    
	}

	/**
	 * Détruit la session courante et commence une session avec un nouvel identifiant.
	 *
	 * @return boolean true si le nettoyage s'est bien passé.
	 */
	protected static function _wipe() {
		self::destroy();
		self::start();
		return self::regenerateId();
	}
	
	/**
	 * Vérifie la sécurité de la session.
	 *
	 * @return boolean true si la session est sécurisée.
	 */
	protected static function _checkSecurity() {
		// Vérifie qu'on essaie pas de forcer l'identifiant de session
		if(!ini_get("session.use_only_cookies")) {
			$sessionName = session_name ();
			if(isset($_GET[$sessionName]) or isset($_POST[$sessionName])) {
				// Dans le doute, commence une nouvelle session
				return self::_wipe();
			}
		}

		// Vérifie que la session a été créée par nos soins
		$sessionKey = self::get ('sessionKey');
		if(!$sessionKey) {
			// La session n'a pas été créée par nos soins => on repart sur une session propre
			return self::_wipe();
			
		} elseif (CopixConfig::instance ()->session_secure_with_cookie
				&& CopixCookie::get ('sessionKey') !== $sessionKey
			){
			// La clef de session stockée dans la session et celle
			// du cookie ne correspondent pas => on détruit l'utilisateur
			// pour forcer une identification.
			//CopixCookie::set ('sessionKey', $sessionKey);
			CopixAuth::destroyCurrentUser();
		}
		
		// Pas de vérification, tout va pour le mieux dans le meilleur des mondes.
		return true;
	}
	
	/**
	 * Détermine si la session est sécurisée.
	 *
	 * @return boolean true si la session est sécurisée.
	 */
	public static function isSecure() {
	    self::startIfNeeded();
		if(!isset(self::$_secure)) {
			self::$_secure = self::_checkSecurity();
		}
		return self::$_secure;
	}
	
	/**
	 * S'assure de la sécurité de la session.
	 *
	 * @throws CopixCredentialException
	 */
	public static function assertSecure() {
		//isSecure will call startIfNeeded
	    if(!self::isSecure()) {
			throw new CopixCredentialException("Unsecure session");
		} else {
			_notify ('copix_SessionReady');
		}
	}
	
	/**
	 * Force un changement d'identificateur de session, tout en conservant les données.
	 *
	 * @return boolean true si le changement d'identificateur a fonctionné.
	 */
	public static function regenerateId () {
	    self::startIfNeeded();
		if(!session_regenerate_id (true)) {
			return false;
		}
		$_COOKIE[session_name ()] = session_id ();
		if(CopixConfig::instance ()->session_secure_with_cookie) {
			$sessionKey = uniqid ();
			self::set ('sessionKey', $sessionKey);
			CopixCookie::set ('sessionKey', $sessionKey);
		} else {
			self::set ('sessionKey', true);
		}
		self::$_secure = true;
		return true;
	}

	/**
	 * Destruction de la session, du cookie et des variables globales associées
	 *
	 * @return boolean
	 */
	public static function destroy () {
        self::startIfNeeded();	    
		$destroy = session_destroy ();
		if ($destroy) {
			setcookie (session_name (), null, time () - 3600);
			unset ($_COOKIE[session_name ()]);
			$_SESSION = array ();
		}
		self::$_started = false;
		return $destroy;
	}
	
	/**
	 * Destruction de toutes les informations qui ont été rajoutées dans le namespace indiqué
	 *
	 * @param string $pNamespace Namespace à supprimer
	 */
	public static function destroyNamespace ($pNamespace) {
	    self::startIfNeeded();
		unset ($_SESSION[self::_getKey ()][$pNamespace]);
	}
	
	/**
	 * Prépare une valeur pour etre stockée, si c'est un objet ou un tableau, il est encapsulé dans un CopixSessionObject.
	 *
	 * @param mixed $toStore
	 */
	private static function _prepareForStorage (&$pToStore) {
		if ((is_object ($pToStore) && !($pToStore instanceof CopixSessionObject)) || is_array ($pToStore)) {
			$pToStore = new CopixSessionObject ($pToStore);
		}
	}

	/**
	 * Définition d'une variable dans la session
	 *
	 * @param string $pVar	Nom de la variable
	 * @param mixed $pValue Valeur de la variable
	 * @param string $pNamespace Namespace dans lequel on veut placer la variable
	 */
	public static function set ($pVar, $pValue, $pNamespace = 'default') {
        self::startIfNeeded();	    
		if ($pNamespace === null) {
			$pNamespace = 'default';
		}

		if ($pValue === null) {
			unset ($_SESSION[self::_getKey ()][$pNamespace][$pVar]);
			if (isset ($_SESSION[self::_getKey ()][$pNamespace]) && count ($_SESSION[self::_getKey ()][$pNamespace]) == 0) {
				self::destroyNamespace ($pNamespace);
			}
		} else {
			self::_prepareForStorage ($pValue);
			$_SESSION[self::_getKey ()][$pNamespace][$pVar] = $pValue;
		}
	}
	
	/**
	 * Destruction d'une variable en session
	 *
	 * @param string $pVar	Nom de la variable
	 * @param string $pNamespace Namespace dans lequel est la variable à supprimer
	 */
	public static function delete ($pVar, $pNamespace = 'default') {
		self::set ($pVar, null, $pNamespace);
	}
	
	/**
	 * /!\ Méthode à ne plus utiliser /!\
	 * Utiliser set
	 *
	 * @deprecated Utiliser set
	 * @see set
	 */
	public static function setObject ($pVar, $pValue, $pFilename, $pNamespace = 'default') {
		self::set ($pVar, $pValue, $pNamespace);
	}
	
	/**
	 * Ajoute un élément au tableau $pVar, ou le créé si il n'existe pas
	 *
	 * @param string $pVar	Nom de la variable
	 * @param mixed $pValue Valeur de la variable
	 * @param string $pNamespace Namespace dans lequel on veut placer la variable
	 */
	public static function push ($pVar, $pValue, $pNamespace = 'default') {
        self::startIfNeeded();	    
		if (!isset ($_SESSION[$key = self::_getKey ()][$pNamespace][$pVar])) {
			$_SESSION[$key][$pNamespace][$pVar] = new CopixSessionObject (array ());
		}

		$arrayRef = & $_SESSION[$key][$pNamespace][$pVar]->getSessionObject ();
		if (is_array ($arrayRef)){
			array_push ($arrayRef, $pValue);
		}else{
			//TODO FIXME de temps en temps, arrayRef n'est pas un tableau et reste sérialisé...
			/*
			$arrayRef = unserialize ($arrayRef);
			_dump ($arrayRef);
			*/
		}
	}

	/**
	 * Retourne la valeur d'une variable en session
	 *
	 * @param string $pVar	Nom de la variable
	 * @param string $pNamespace Namespace dans lequel on veut lire la variable
	 * @param mixed  $pDefaultValue Valeur par défaut si la variable n'existe pas
	 * @return mixed
	 */
	public static function get ($pVar, $pNamespace = 'default', $pDefaultValue = null) {
        self::startIfNeeded();
	    $value = $pDefaultValue;
		if (isset ($_SESSION[$key = self::_getKey ()][$pNamespace][$pVar])) {
			if ($_SESSION[$key][$pNamespace][$pVar] instanceof CopixSessionObject) {
				$value = $_SESSION[$key][$pNamespace][$pVar]->getSessionObject ();
			} else {
				return $_SESSION[$key][$pNamespace][$pVar];
			}
		}
		return $value;
	}
	
	/**
	 * Indique si une variable existe en session
	 *
	 * @param string $pVar Nom de la variable
	 * @param string $pNamespace Namespace
	 * @return bool
	 */
	public static function exists ($pVar, $pNamespace = 'default') {
        self::startIfNeeded();
	    return isset ($_SESSION[self::_getKey ()][$pNamespace][$pVar]);
	}
	
	/**
	 * Retourne la liste des namespaces
	 *
	 * @return array
	 */
	public static function getNamespaces () {
        self::startIfNeeded();
	    $toReturn = array ();
		$key = self::_getKey ();
		foreach ($_SESSION[$key] as $namespace => $value) {
			$toReturn[] = $namespace;
		}
		sort ($toReturn);
		return $toReturn;
	}
	
	/**
	 * Retourne les variables définies dans le namespace $pNamespace
	 *
	 * @param string $pNamespace Namespace dont on veut les variables
	 * @return array
	 */
	public static function getVariables ($pNamespace = 'default') {
	    $toReturn = array ();
		if (self::namespaceExists ($pNamespace)) {
			$key = self::_getKey ();
			foreach ($_SESSION[$key][$pNamespace] as $var => $value) {
				$toReturn[$var] = self::get ($var, $pNamespace);
			}
		}
		ksort ($toReturn);
		return $toReturn;
	}
	
	/**
	 * Indique si le namespace $pNamespace existe
	 *
	 * @param string $pNamespace Namespace dont on veut vérifier l'existance
	 * @return boolean
	 */
	public static function namespaceExists ($pNamespace) {
        self::startIfNeeded();
	    return isset ($_SESSION[self::_getKey ()][$pNamespace]);
	}
}