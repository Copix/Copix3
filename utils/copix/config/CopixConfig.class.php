<?php
/**
 *
 * @package		copix
 * @subpackage	core
 * @author		Croës Gérald, Bertrand Yan
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Fichier de configuration principal du framework
 * Définit une classe dont les propriétés représentent tout les paramètres du framework, avec leurs valeurs par défaut.
 * Pour indiquer des valeurs spécifiques, il faut le faire via le fichier de configuration copix.conf.php
 *
 * @package		copix
 * @subpackage	core
 */
class CopixConfig {
	/**
	 * Mode de fonctionnement ou tout sera forcé à l'initialisation
	 */
	const FORCE_INITIALISATION = 0;

	/**
	 * Mode de fonctionnement en développement
	 */
	const DEVEL = 1;

	/**
	 * Mode de fonctionnement en production
	 */
	const PRODUCTION = 2;

	/**
	 * Singleton
	 * 
	 * @ar CopixConfig
	 */
	private static $_instance = false;
	
	/* ================================================================================================================== */
	/*                                              CONFIGURATION GENERALE                                                */
	/* ================================================================================================================== */

	/**
	 * Configuration des modules
	 *
	 * @var array
	 */
	private $_configModule = array();

	/**
	 * Si une action invalide lance une erreur ou non
	 *
	 * @see notFoundDefaultRedirectTo
	 */
	public $invalidActionTriggersError = false;

	/**
	 * Indique vers quelle url (url type copix) on redirige l'utilisateur s'il demande une action non prise en charge par le controller.
	 *
	 * @var string
	 */
	public $notFoundDefaultRedirectTo = false;

	/**
	 * Indique si les compilateurs doivent checker le cache pour savoir si il faut mettre à jour ou pas le cache
	 *
	 * @var boolean
	 */
	public $compile_check = true;

	/**
	 * Indique si il faut toujours recompiler
	 *
	 * @var boolean
	 */
	public $force_compile = false;

	/**
	 * Chemin ou l'on doit doit aller chercher les modules.
	 * 
	 * @var array
	 */
	public $arModulesPath = array ();

	/**
	 * Indique si le système d'autorisation des modules est activé
	 *
	 * @var boolean
	 */
	public $checkTrustedModules = false;

	/**
	 * Liste des modules autorisés
	 *
	 * @var array 'nom_du_module' => true / false
	 */
	public $trustedModules = array ();

	/**
	 * Le nom de la session pour permettre à plusieurs instances de Copix de cohabiter sur le même espace
	 *
	 * @var string
	 */
	public $sessionName = 'Copix';

	/**
	 * Indique si la session doit démarrer automatiquement
	 * 
	 * Si true, la session démarre automatiquement qu'elle soit utilisée ou non
	 * Si false, la session démarre à la première sollicitation (via CopixSession) 
	 * 
	 * @var boolean
	 */
	public $session_autostart = true;
	
	/**
	 * Nom de la clef de $_SESSION où seront stockées les sessions via CopixSession
	 *
	 * @var string
	 */
	public $copixsession_key = 'COPIX';
	
	/**
	 * Indique s'il faut sécuriser la session avec un cookie par utilisateur
	 *
	 * @var boolean
	 */
	public $session_secure_with_cookie = false;

	/**
	 * Mode de fonctionnement de l'application par défaut
	 *
	 * @var int
	 */
	private $_mode = self::DEVEL;
	
	/* ================================================================================================================== */
	/*                                                  COPIXPROXY                                                        */
	/* ================================================================================================================== */

	/**
	 * Proxys enregistrés
	 * 
	 * @var array
	 */
	private $_copixproxy_proxys = array ();
	
	/**
	 * Indique si les proxys ont été triés (évite de trier plusieurs fois le tableau déja trié)
	 *
	 * @var boolean
	 */
	private $_copixproxy_isSorted = false;
	
	/**
	 * Indique si les proxys configurés dans le fichier de config ont été chargés
	 *
	 * @var boolean
	 */
	private $_copixproxy_loaded = false;
	
	/**
	 * Ajoute un proxy
	 * 
	 * @param string $pId Identifiant
	 * @param string $pHost Adresse
	 * @param int $pPort Port
	 * @param string $pUser Utilisateur
	 * @param string $pPassword Mot de passe
	 * @param array $pNotFor Adresses pour lesquelles on n'utilise pas ce proxy
	 * @param boolean $pEnabled Indique si le proxy est activé
	 * @throws CopixProxyException Le proxy existe déja, code CopixProxyException::EXISTS
	 */
	public function copixproxy_register ($pId, $pHost, $pPort, $pUser, $pPassword, $pEnabled = true, $pNotForHosts = array (), $pForHosts = array ()) {
		$this->copixproxy_loadRegisteredProxys ();
		
		if (self::exists ($pId)) {
			throw new CopixProxyException (_i18n ('copix:copixproxy.error.exists', $pId), CopixProxyException::EXISTS);
		}
		$this->_copixproxy_proxys[$pId] = new CopixProxy ($pId, $pHost, $pPort, $pUser, $pPassword, $pEnabled, $pNotForHosts, $pForHosts);
		$this->_copixproxy_isSorted = false;
	}
	
	/**
	 * Supprime un proxy
	 * 
	 * @throws CopixProxyException Le proxy n'a pu être trouvé, code CopixProxyException::NOT_FOUND
	 */
	public function copixproxy_unregister ($pId) {
		$this->copixproxy_loadRegisteredProxys ();
		
		if (!$this->copixproxy_exists ($pId)) {
			throw new CopixProxyException (_i18n ('copix:copixproxy.error.notFound', $pId), CopixProxyException::NOT_FOUND);
		}
		unset ($this->_copixproxy_proxys[$pId]);
	}
	
	/**
	 * Retourne la liste des proxys configurés
	 * 
	 * @return CopixProxy[]
	 */
	public function copixproxy_getProxys () {
		$this->copixproxy_loadRegisteredProxys ();
		
		if (!$this->_copixproxy_isSorted) {
			ksort ($this->_copixproxy_proxys);
			$this->_copixproxy_isSorted = true;
		}
		return $this->_copixproxy_proxys;
	}
	
	/**
	 * Retourne le chemin du fichier de configuration des proxys
     *
	 * @return string
	 */
	public function copixproxy_getConfigFilePath () {
		return COPIX_VAR_PATH . 'config/proxys.conf.php';
	}
	
	/**
	 * Charge la configuration des proxys depuis le fichier de config une seule fois
	 * Le but étant de ne pas charger la config si jamais on n'appelle pas les méthodes liées aux proxys
	 */
	public function copixproxy_loadRegisteredProxys () {
		if ($this->_copixproxy_loaded) {
			return ;
		}
		
		$this->_copixproxy_loaded = true;
		$config = $this->copixproxy_getConfigFilePath ();
		if (is_readable ($config)) {
			$proxys = require ($config);
			foreach ($proxys as $id => $proxy) {
				$this->copixproxy_register (
					$id, $proxy['host'], $proxy['port'], $proxy['user'],	$proxy['password'],
					$proxy['enabled'], $proxy['notForHosts'], $proxy['forHosts']
				);
			}
		}
	}
	
	/* ================================================================================================================== */
	/*                                               AUTHENTIFICATION                                                     */
	/* ================================================================================================================== */

	/**
	 * Liste des gestionnaires d'utilisateurs enregistrés, triée par rang croissant.
	 *
	 * @var array
	 */
	private $_arUserHandlers = array ();

    /**
     * Indique si on a chargé les user handlers
     *
     * @var boolean
     */
    private $_userhandlers_loaded = false;
    
	/**
	 * Liste des gestionnaires de groupes enregistrés
	 *
	 * @var array
	 */
	private $_arGroupHandlers = array ();

    /**
     * Indique si on a chargé les group handlers
     *
     * @var boolean
     */
    private $_grouphandlers_loaded = false;

	/**
	 * Liste des gestionnaires de droits enregistrés.
	 *
	 * @var array
	 */
	private $_arCredentialHandlers = array ();

    /**
     * Indique si on a chargé les credential handlers
     *
     * @var boolean
     */
    private $_credentialhandlers_loaded = false;

	/**
	 * Si l'on souhaite utiliser le cache lors du test des droits.
	 * Cette option n'est à désactiver que dans le contexte de développement.
	 *
	 * @var boolean
	 */
	public $copixauth_cache = true;
	
	/**
	 * Si l'on souhaite que plusieurs sites qui partagent la m�me session utilisent un cache de droit diff�rent
	 *  c'est ici que l'on sp�cifie l'identifiant de cache concern�  
	 * @var string
	 */
	public $copixauth_sharedcredentialskey = 'copix';

	/**
	 * Si l'on souhaite que plusieurs sites qui partagent la m�me session utilisent un cache d'apaprtenance aux groupes
	 *  c'est ici que l'on sp�cifie l'identifiant de cache concern�  
	 * @var string
	 */
	public $copixauth_sharedgroupskey = 'copix';	

	/**
	 * Enregistrement de gestionnaire d'authentification
	 *
	 * @param mixed $pHandlerDefinition String (nom handler) ou array pour décrire le handler
	 */
	public function copixauth_registerUserHandler ($pHandlerDefinition) {
		// Si le handler n'est pas un tableau, alors on crée le tableau
		if (!is_array ($pHandlerDefinition)) {
			$pHandlerDefinition = array ('name' => $pHandlerDefinition);
		}
		// 
		if (!isset($pHandlerDefinition['required'])) {
			$pHandlerDefinition['required'] = false;
		}
		// Fixe la priorité par défaut : 10 x le nombre d'handler déjà enregistré 
		if (!isset($pHandlerDefinition['rank']) || !is_numeric($pHandlerDefinition['rank'])) {
			$pHandlerDefinition['rank'] = 10 * (1 + count($this->_arUserHandlers));
		} else {
			$pHandlerDefinition['rank'] = intval($pHandlerDefinition['rank']);
		}
		$this->_arUserHandlers[$pHandlerDefinition['name']] = $pHandlerDefinition;
		
		// Maintient l'ordre
		uasort($this->_arUserHandlers, array($this, '_copixauth_rankCompareFunc'));
	}
	
	/**
	 * Méthode utilisée pour trier les gestionnaires par rang.
	 * 
	 * @param array $a Définition du premier gestionnaire.
	 * @param array $b Définition du seconde gestionnaire.
	 * @return int Valeur négative si le rang de $a est inférieur à celui de $b, valeur positive si
	 *             c'est l'inverse et 0 s'ils sont égaux. 
	 */
	private function _copixauth_rankCompareFunc($a, $b) {
		return (isset($a['rank']) ? $a['rank'] : PHP_INT_MAX) - (isset($b['rank']) ? $b['rank'] : PHP_INT_MAX);
	}

	/**
	 * Indique si le handler d'utilisateur donné est présent
	 *
	 * @param string $pHandlerName Nom du handler à tester
	 * @return boolean
	 */
	public function copixauth_isRegisteredUserHandler ($pHandlerName) {
		$this->_copixauth_loadRegisteredUserHandlers ();
		return isset ($this->_arUserHandlers[$pHandlerName]);
	}

	/**
	 * Supression de tous les gestionnaires utilisateurs enregistrés
	 */
	public function copixauth_clearUserHandlers () {
		//Si on supprime, on charge la configuration "avant" pour qu'elle aussi soit supprimée
		$this->_copixauth_loadRegisteredUserHandlers ();		
		$this->_arUserHandlers = array ();
	}

	/**
	 * Retourne la liste des gestionnaires d'utilisateurs enregistrés
	 *
	 * @return array
	 */
	public function copixauth_getRegisteredUserHandlers () {
		$this->_copixauth_loadRegisteredUserHandlers ();
		return $this->_arUserHandlers;
	}

	/**
	 * Enregistrement d'un gestionnaire de groupe
	 *
	 * @param mixed $pHandlerDefinition String (nom du handler) ou array pour décrire le handler
	 */
	public function copixauth_registerGroupHandler ($pHandlerDefinition) {
		// Si le handler n'est pas un tableau, alors on crée le tableau
		if (!is_array ($pHandlerDefinition)) {
			$pHandlerDefinition = array ('name' => $pHandlerDefinition);
		}
		// valeurs par défaut
		if (!isset ($pHandlerDefinition['required'])) {
			$pHandlerDefinition['required'] = false;
		}
		$this->_arGroupHandlers[$pHandlerDefinition['name']] = $pHandlerDefinition;
	}

	/**
	 * Indique si le gestionnaire de groupe donné est enregistré
	 *
	 * @param string $pHandlerName Nom du groupe à tester
	 * @return boolean
	 */
	public function copixauth_isRegisteredGroupHandler ($pHandlerName) {
		$this->_copixauth_loadRegisteredGroupHandlers ();		
		return isset ($this->_arGroupHandlers[$pHandlerName]);
	}

	/**
	 * Efface la liste des gestionnaires de groupes enregistrés.
	 */
	public function copixauth_clearGroupHandlers () {
		//Si on supprime, on charge la configuration "avant" pour qu'elle aussi soit supprimée
		$this->_copixauth_loadRegisteredGroupHandlers ();
		$this->_arGroupHandlers = array ();
	}

	/**
	 * Retourne la liste des gestionnaires de groupes enregistrés
	 *
	 * @return array
	 */
	public function copixauth_getRegisteredGroupHandlers () {
		$this->_copixauth_loadRegisteredGroupHandlers ();
		return $this->_arGroupHandlers;
	}

	/**
	 * Enregistrement d'un gestionnaire de droits
	 *
	 * @param mixed $pHandlerDefinition String (nom du handler) ou array pour décrire le handler (clefs : stopOnSuccess, stopOnFailure, handle, handleExcept)
	 */
	public function copixauth_registerCredentialHandler ($pHandlerDefinition) {
		// Si le handler n'est pas un tableau, alors on crée le tableau
		if (!is_array ($pHandlerDefinition)) {
			$pHandlerDefinition = array ('name' => $pHandlerDefinition);
		}
		// paramètres par défaut du handler
		if (!isset ($pHandlerDefinition['stopOnSuccess'])) {
			$pHandlerDefinition['stopOnSuccess'] = false;
		}
		if (!isset ($pHandlerDefinition['stopOnFailure'])) {
			$pHandlerDefinition['stopOnFailure'] = true;
		}
		if (!isset ($pHandlerDefinition['handle'])) {
			$pHandlerDefinition['handle'] = 'all';
		}
		if ((!is_array ($pHandlerDefinition['handle'])) && ($pHandlerDefinition['handle'] !== 'all')) {
			$pHandlerDefinition['handle'] = array ($pHandlerDefinition['handle']);
		}
		if (!isset ($pHandlerDefinition['handleExcept'])) {
			$pHandlerDefinition['handleExcept'] = array ();
		}
		$this->_arCredentialHandlers[$pHandlerDefinition['name']] = $pHandlerDefinition;
	}

	/**
	 * Indique si le gestionnaire de droit donné est enregistré ou non
	 *
	 * @param string $pHandlerName Nom du gestionnaire à tester
	 * @return boolean
	 */
	public function copixauth_isRegisteredCredentialHandler ($pHandlerName) {
		$this->_copixauth_loadRegisteredCredentialHandlers ();
		return isset ($this->_arCredentialHandlers[$pHandlerName]);
	}

	/**
	 * Récupère la liste des gestionnaires de droit enregistrés
	 *
	 * @return array
	 */
	public function copixauth_getRegisteredCredentialHandlers () {
		$this->_copixauth_loadRegisteredCredentialHandlers ();
		return $this->_arCredentialHandlers;
	}

	/**
	 * Efface la liste des gestionnaires de droits enregistrés
	 */
	public function copixauth_clearCredentialHandlers () {
		//Si on supprime, on charge la configuration "avant" pour qu'elle aussi soit supprimée
		$this->_copixauth_loadRegisteredCredentialHandlers ();
		$this->_arCredentialHandlers = array ();
	}

	/* ================================================================================================================== */
	/*                                                      LOGS                                                          */
	/* ================================================================================================================== */

	/**
	 * Définition des logs par défauts
	 *
	 * @var array
	 */
	private $_arLogDefinition = array ();

	/**
	 * Type de log par défaut.
	 * 
	 * @var string
	 */
	private $_copixlog_defaultTypeName = 'default';

	/**
	 * Retourne la liste des logs configurés
	 *
	 * @return array
	 */
	public function copixlog_getRegistered () {
		return array_keys ($this->_arLogDefinition);
	}

	/**
	 * Enregistrement d'un type de log
	 *
	 * @param array $pLogDefinition Définition du log à enregistrer (clefs : handle, strategy, level, enabled)
	 */
	public function copixLog_registerProfile ($pLogDefinition) {
		// On met toujours la définition du log sous la forme d'un tableau
		if (!is_array ($pLogDefinition)) {
			$pLogDefinition = array ('name' => $pLogDefinition);
		}
		// La stratégie par défaut est file
		if (!isset ($pLogDefinition['handle'])) {
			$pLogDefinition['handle'] = 'all';
		}
		// La stratégie par défaut est file
		if (!isset ($pLogDefinition['strategy'])) {
			$pLogDefinition['strategy'] = 'file';
		}
		// Le level par défaut est CopixLog::INFORMATION
		
		if (!isset ($pLogDefinition['level'])) {
			$pLogDefinition['level'] = array (CopixLog::INFORMATION, CopixLog::NOTICE, CopixLog::WARNING, CopixLog::EXCEPTION, CopixLog::ERROR, CopixLog::FATAL_ERROR);
		} else if (!is_array ($pLogDefinition['level'])) {
			switch ($pLogDefinition['level']) {
				case CopixLog::VERBOSE :
					$pLogDefinition['level'] = array (CopixLog::VERBOSE, CopixLog::INFORMATION, CopixLog::NOTICE, CopixLog::WARNING, CopixLog::EXCEPTION, CopixLog::ERROR, CopixLog::FATAL_ERROR);
					break;
				case CopixLog::INFORMATION :
					$pLogDefinition['level'] = array (CopixLog::INFORMATION, CopixLog::NOTICE, CopixLog::WARNING, CopixLog::EXCEPTION, CopixLog::ERROR, CopixLog::FATAL_ERROR);
					break;
				case CopixLog::NOTICE :
					$pLogDefinition['level'] = array (CopixLog::NOTICE, CopixLog::WARNING, CopixLog::EXCEPTION, CopixLog::ERROR, CopixLog::FATAL_ERROR);
					break;
				case CopixLog::WARNING :
					$pLogDefinition['level'] = array (CopixLog::WARNING, CopixLog::EXCEPTION, CopixLog::ERROR, CopixLog::FATAL_ERROR);
					break;
				case CopixLog::EXCEPTION :
					$pLogDefinition['level'] = array (CopixLog::EXCEPTION, CopixLog::ERROR, CopixLog::FATAL_ERROR);
					break;
				case CopixLog::ERROR :
					$pLogDefinition['level'] = array (CopixLog::ERROR, CopixLog::FATAL_ERROR);
					break;
				case CopixLog::FATAL_ERROR :
					$pLogDefinition['level'] = array (CopixLog::FATAL_ERROR);
					break;
			}
		}

		// log actif ?
		if (!isset ($pLogDefinition['enabled'])) {
			$pLogDefinition['enabled'] = true;
		}
		// Sauvegarde des infos sur les logs
		if (isset ($pLogDefinition['name'])) {
			$this->_arLogDefinition[$pLogDefinition['name']] = $pLogDefinition;
		}
	}

	/**
	 * Retourne les handler capables de gérer le type de log donné
	 *
	 * @param string $pType Type de log dont on vet les handlers
	 * @return array
	 */
	public function copixlog_getProfileFromType ($pType) {
		$arProfil = array ();

		foreach ($this->_arLogDefinition as $keys => $profil) {
			if (is_array ($profil['handle'])) {
				if (in_array ($pType, $profil['handle'])) {
					$arProfil[] = $profil;
				}
			} else {
				if ($profil['handle'] == 'all' || $profil['handle'] == $pType) {
					if (isset ($profil['handleExcept'])) {
						if (in_array ($pType, is_array ($profil['handleExcept']) ? $profil['handleExcept']: array ($profil['handleExcept']))) {
							continue;
						}
					}
					$arProfil[] = $profil;
				}
			}
		}
		return $arProfil;
	}

	/**
	 * Recupération des données de configuration pour un type de log
	 *
	 * @param string $pName Nom du type de log dont on souhaite récupérer les informations
	 * @return mixed Informations de configuration du type de log. Null si le type de log n'est pas configuré
	 */
	public function copixlog_getProfile ($pName) {
		if ($pName !== null) {
			if (isset ($this->_arLogDefinition[$pName])) {
				return $this->_arLogDefinition[$pName];
			}
		}
		return $this->copixlog_getDefaultType ();
	}

	/**
	 * Récupération de la liste des profils enregistrés
	 *
	 * @return array
	 */
	public function copixlog_getRegisteredProfiles () {
		return $this->_arLogDefinition;
	}

	/**
	 * Retourne les données de configuration pour le type de log configuré par défaut
	 *
	 * @return array ou null si aucun type de log par défaut
	 */
	public function copixlog_getDefaultType () {
		// si aucun log demandé par défaut, null
		if (($typeName = $this->copixlog_getDefaultTypeName ()) === null) {
			return null;
		}
		// si le type par défaut n'est pas configuré, null
		if (!isset ($this->_arLogDefinition[$typeName])) {
			return null;
		}
		return $this->_arLogDefinition[$typeName];
	}

	/**
	 * Récupération du log par défaut
	 *
	 * @return string
	 */
	public function copixlog_getDefaultTypeName () {
		return $this->_copixlog_defaultTypeName;
	}

	/**
	 * Permet de changer le log par défaut
	 *
	 * @param string $pLog Nom du log à définir comme log par défaut
	 */
	public function copixlog_setDefaultTypeName ($pLog) {
		$this->_copixlog_defaultTypeName = $pLog;
	}

	/* ================================================================================================================== */
	/*                                                      CACHE                                                         */
	/* ================================================================================================================== */

	/**
	 * Tableau de définition sur les types de cache
	 *
	 * @var array
	 */
	private $_arCacheDefinition = array ();

	/**
	 * Le nom du type de cache à utiliser par défaut
	 *
	 * @var string
	 */
	private $_copixcache_defaultTypeName = 'default';

	/**
	 * Enregistrement d'un type de cache
	 *
	 * @param mixed $pCacheDefinition Tableau contenant le système de cache. Clefs : strategy, enabled, link, duration
	 */
	public function copixcache_registerType ($pCacheDefinition) {
		// On met toujours la définition du cache sous la forme d'un tableau
		if (!is_array ($pCacheDefinition)) {
			$pCacheDefinition = array ('name' => $pCacheDefinition);
		}
		// La stratégie par défaut est file
		if (!isset ($pCacheDefinition['strategy'])) {
			$pCacheDefinition['strategy'] = 'file';
		}
		// si file et pas de répertoire, on place par défaut comme répertoire le nom du cache
		if ($pCacheDefinition['strategy'] == 'file' && !isset ($pCacheDefinition['dir'])) {
			$pCacheDefinition['dir'] = $pCacheDefinition['name'];
		}
		// cache actif ?
		if (!isset ($pCacheDefinition['enabled'])) {
			$pCacheDefinition['enabled'] = true;
		}
		// Liens entre les caches ?
		if (!isset ($pCacheDefinition['link'])) {
			$pCacheDefinition['link'] = '';
		}
		// durée ?
		if (!isset ($pCacheDefinition['duration'])) {
			$pCacheDefinition['duration'] = 0;
		}
		// Sauvegarde des infos sur le cache
		$this->_arCacheDefinition[$pCacheDefinition['name']] = $pCacheDefinition;
	}

	/**
	 * Recupération des données de configuration pour un type de cache
	 *
	 * @param string $pName Nom du type de cache dont on souhaite récupérer les informations
	 * @return mixed les informations de configuration du type de cache. Null si le type de cache n'est pas configuré
	 */
	public function copixcache_getType ($pName) {
		if ($pName !== null) {
			if (isset ($this->_arCacheDefinition[$pName])) {
				return $this->_arCacheDefinition[$pName];
			}
		}

		return $this->copixcache_getDefaultType ();
	}

	/**
	 * Retourne les données de configuration pour le type de cache configuré par défaut
	 *
	 * @return array ou null si aucun type de cache par défaut
	 */
	public function copixcache_getDefaultType () {
		// si aucun cache demandé par défaut, null
		if (($typeName = $this->copixcache_getDefaultTypeName ()) === null) {
			return null;
		}
		// si le type par défaut n'est pas configuré, null
		if (!isset ($this->_arCacheDefinition[$typeName])) {
			return null;
		}
		return $this->_arCacheDefinition[$typeName];
	}

	/**
	 * Récupération du nom du cache par défaut
	 *
	 * @return string
	 */
	public function copixcache_getDefaultTypeName () {
		return $this->_copixcache_defaultTypeName;
	}

	/**
	 * Permet de changer le cache par défaut
	 *
	 * @param string $pCache Nom du cache à définir comme cache par défaut
	 */
	public function copixcache_setDefaultTypeName ($pCache) {
		$this->_copixcache_defaultTypeName = $pCache;
	}

	/**
	 * Récupération de la liste des nom des profils enregistrés
	 *
	 * @return array
	 */
	public function copixcache_getRegistered () {
		return array_keys ($this->_arCacheDefinition);
	}

	/**
	 * Récupération de la liste des profils enregistrés
	 *
	 * @return array
	 */
	public function copixcache_getRegisteredProfiles () {
		return $this->_arCacheDefinition;
	}

	/* ================================================================================================================== */
	/*                                            CONNEXION BASE DE DONNEES                                               */
	/* ================================================================================================================== */

	/**
	 * Nom du profil de connexion par défaut
	 * 
	 * @var string
	 */
	private $_copixdb_defaultProfileName = null;

	/**
	 * Tableau des profils de connexion connus
	 * 
	 * @var array
	 */
	private $_copixdb_profiles = array ();

    /**
     * Indique si on a chargé les profils de base de données
     *
     * @var boolean
     */
    private $_db_loaded = false;
	
	/**
	 * Retourne le chemin du fichier de configuration des bases de donnéess
	 * 
	 * @return string
	 */
	public function copixdb_getConfigFilePath () {
		return COPIX_VAR_PATH . 'config/db_profiles.conf.php';
	}

	/**
	 * Ajoute un profil de connexion à CopixDB
	 *
	 * @param string $pName Nom du profil que l'on souhaite ajouter
	 * @param object $pConnectionString Profil de connexion que l'on souhaite utiliser
	 * @param string $pUser Login
	 * @param string $pPassword Mot de passe
	 * @param array $pOptions Tableau d'options en fonction du driver utilisé.
	 */
	public function copixdb_defineProfile ($pName, $pConnectionString, $pUser, $pPassword, $pOptions = array ()) {
		$this->_copixdb_profiles[$pName] = new CopixDBProfile ($pName, $pConnectionString, $pUser, $pPassword, $pOptions);
	}

	/**
	 * Retourne la liste des profils définis dans CopixDB
	 *
	 * @return array Liste des profils de connexion définis
	 */
	public function copixdb_getProfiles () {
		$this->_copixdb_loadProfiles ();		
		return array_keys ($this->_copixdb_profiles);
	}
	
	/**
	 * Retourne le nombre de profils de connexion définits
	 *
	 * @return int
	 */
	public function copixdb_getProfilesCount () {
		$this->_copixdb_loadProfiles ();
		return count ($this->_copixdb_profiles);
	}

	/**
	 * Définit le nom du profil de connexion par défaut
	 *
	 * @param string $pName Nom du profil de connexion que l'on souhaite mettre par défaut
	 */
	public function copixdb_defineDefaultProfileName ($pName) {
		$this->_copixdb_defaultProfileName = $pName;
	}

	/**
	 * Récupère le nom du profil de connexion par défaut
	 *
	 * @return string
	 */
	public function copixdb_getDefaultProfileName () {
		$this->_copixdb_loadProfiles ();
		return $this->_copixdb_defaultProfileName;
	}

	/**
	 * Retourne le profil de connexion de nom $pName
	 *
	 * @param string $pName
	 * @return CopixDBProfile
	 * @throws CopixDBException
	 */
	public function copixdb_getProfile ($pName = null) {
		$this->_copixdb_loadProfiles ();
		if ($pName === null) {
			$pName = $this->copixdb_getDefaultProfileName ();
			if ($pName === null) {
				throw new CopixDBException (_i18n ('copix:copixdb.error.noDefaultProfil'));
			}
		}
		if (isset ($this->_copixdb_profiles[$pName])) {
			return $this->_copixdb_profiles[$pName];
		}
		throw new CopixDBException (_i18n ('copix:copixdb.error.unknowProfil', array ($pName)));
	}

	/* ================================================================================================================== */
	/*                                                     RESSOURCES                                                        */
	/* ================================================================================================================== */

	/**
	 * Chemins relatifs des ressources
	 *
	 * @var array
	 */
	private $_copixresource_dirs = array(
		'' // Racine
	);
	
	/**
	 * Indique si on ajoute le numéro de version en paramètre des ressources
	 * Attention : les ressources ne seront donc pas mises en cache, c'est mauvais pour les performances.
	 * Si vous pouvez configurer htaccess, utilisez plutôt $copixresource_addVersionInFileName
	 *
	 * @var Boolean
	 */
	public $copixresource_addVersionParam = false;
	
	/**
	 * Indique si on ajoute le numéro de version dans le nom de fichier des ressources
	 * Permet d'indiquer une date d'expiration lointaine pour les ressources afin qu'elles soient gardées en cache
	 * Si vous l'utilisez, ouvrez le fichier .htaccess et supprimez le # au début de la ligne suivante :
	 * #RewriteRule ^(.+)\.v[0-9]+\.(png|gif|jpe?g|ico|css|js|swf)$ $1.$2 [NC,L]
	 *
	 * @var Boolean
	 */
	public $copixresource_addVersionInFileName = false;

	/**
	 * Types de ressources pour les serveurs de ressource
	 */
	const RESSERVER_JS = 'js';
	const RESSERVER_IMAGES = 'images';
	const RESSERVER_STYLES = 'styles';
	const RESSERVER_OTHERS = 'others';

	/**
	 * Serveurs de ressource
	 *
	 * @var array
	 */
	private $_resourcesServers = array (
		self::RESSERVER_JS => array (),
		self::RESSERVER_IMAGES => array (),
		self::RESSERVER_STYLES => array (),
		self::RESSERVER_OTHERS => array ()
	);

	/**
	 * Indique si on a ajouté un serveur de ressource, pour éviter des calculs
	 *
	 * @var boolean
	 */
	private $_resourcesHaveServers = false;
	
	/**
	 * Indique au bout de combien d'utilisation d'un serveur on passe au suivant
	 * 
	 * @var int
	 */
	private $_resourcesChangeServer = 2;

	/**
	 * Indique si on doit compresser le retour au navigateur en GZIP
	 *
	 * @var boolean
	 */
	public $copixresource_gzipCompress = false;
	
	/**
	 * Ajoute un chemin de lequel chercher les ressources.
	 *
	 * @param string $pDirectory Chemin relatif à ajouter.
	 */
	public function copixresource_addDirectory($pDirectory) {
		if(substr($pDirectory, -1) != '/') {
			$pDirectory .= '/';
		}
		$this->_copixresource_dirs[$pDirectory] = $pDirectory;
	}
	
	/**
	 * Remets à zéro la liste des chemins de ressources.
	 *
	 */
	public function copixresource_clearDirectories() {
		$this->_copixresource_dirs = array('');
	}
		
	/**
	 * Retourne la liste des 
	 *
	 * @return array
	 */
	public function copixresource_getDirectories() {
		return $this->_copixresource_dirs;
	}

	/**
	 * Ajoute un serveur de ressource
	 *
	 * @param string $pHost Nom du serveur, sans http:// ni le chemin vers index.php (ou resource.php), et ne doit pas finir par un / ou \
	 * @param string $pType Indique pour quel type de resource on ajoute ce serveur
	 */
	public function copixresource_addServer ($pHost, $pType) {
		$this->_resourcesHaveServers = true;
		$this->_resourcesServers[$pType][] = $pHost;
	}

	/**
	 * Supprime les serveurs d'un type de ressource et remet _resourcesServers à la bonne valeur
	 *
	 * @param String $pType Indique pour quel type de ressource on supprime les serveurs
	 */
	public function copixresource_removeServers ($pType = '') {
		if ($pType === '') {
			foreach ($this->_resourcesServers as $type => $servers) {
				$this->_resourcesServers [$type] = array();
			}
			$this->_resourcesHaveServers = false;
			return;
		}
		$this->_resourcesServers[$pType] = array();
		$this->_resourcesHaveServers = false;
		foreach ($this->_resourcesServers as $type => $host) {
			if (count ($this->_resourcesServers [$type]) > 0) {
				$this->_resourcesHaveServers = true;
				break;
			}
		}
	}

	/**
	 * Retourne la liste des serveurs ajoutés pour le type de ressource demandé
	 *
	 * @param string $pType Indique pour quel type de resource on veut les serveurs
	 * @return array
	 */
	public function copixresource_getServers ($pType) {
		return (isset ($this->_resourcesServers[$pType])) ? $this->_resourcesServers[$pType] : array ();
	}

	/**
	 * Indique si on a ajouté un serveur de ressource, pour éviter de faire des calculs inutiles si on n'a pas de serveur
	 *
	 * @return boolean
	 */
	public function copixresource_haveServers () {
		return $this->_resourcesHaveServers;
	}

	/**
	 * Définit au bout de combien d'utilisations d'un serveur de ressource on passe au suivant
	 *
	 * @param int $pCount Nombre d'utilisation maximum
	 */
	public function copixresource_setChangeServer ($pCount) {
		$this->_resourcesChangeServer = $pCount;
	}

	/**
	 * Retourne le nombre de fois qu'on va utiliser un serveur de ressources avant de passer au suivant
	 *
	 * @return int
	 */
	public function copixresource_getChangeServer () {
		return $this->_resourcesChangeServer;
	}
	
	/* ================================================================================================================== */
	/*                                                TEMPLATES / THEMES                                                  */
	/* ================================================================================================================== */
	
	/**
	 * Liste des chemins des thèmes.
	 */
	private $_copixtpl_paths = array ();
	
	/**
	 * Répertoires des thèmes
	 *
	 * @var array
	 */
	private $_copixtheme_paths = array ();

	/**
	 * Ajoute un nouveau chemin pour les templates
	 *
	 * @param string $pPath
	 */
	public function copixtpl_addPath($pPath) {
		$pPath = CopixFile::getRealPath($pPath);
		if(substr($pPath, -1) != DIRECTORY_SEPARATOR) {
			$pPath .= DIRECTORY_SEPARATOR;
		}
		$this->_copixtpl_paths[$pPath] = $pPath;
	}
	
	/**
	 * Remets à zéro la liste des chemins des templates
	 *
	 */
	public function copixtpl_clearPaths() {
		$this->_copixtpl_paths = array();
	}
	
	/**
	 * Récupère la liste des chemins des templates
	 *
	 * @return array
	 */
	public function copixtpl_getPaths () {
		return $this->_copixtpl_paths;
	}
	
	/**
	 * Ajoute un répertoire pour les thèmes
	 *
	 * @param string $pPath Chemin physique à ajouter
	 */
	public function copixtheme_addPath ($pPath) {
		$this->_copixtheme_paths[$pPath] = $pPath;
	}
	
	/**
	 * Retourne la liste des chemins configurés pour les thèmes
	 *
	 * @return array
	 */
	public function copixtheme_getPaths () {
		return $this->_copixtheme_paths;
	}
	
	/* ================================================================================================================== */
	/*                                               INTERNATIONALISATION                                                  */
	/* ================================================================================================================== */

	/**
	 * timezone par défaut pour éviter un E_STRICT lors de l'utilisation des fonctions relatives aux dates 
	 *
	 * @var string
	 */
	public $default_timezone = 'Europe/Paris';

	/**
	 * Indique si l'on souhaite que les paramètres de langue soient pris en compte
	 * lors du calcul des chemins des templates (système de thèmes) et des ressources (en complément du système de thème).
	 *
	 * @see CopixTpl
	 * @var boolean
	 */
	public $i18n_path_enabled = false;

	/**
	 * Indique si on lève une exception lors d'une clef i18n manquante
	 *
	 * @var boolean
	 */
	public $i18n_missingKeyLaunchException = false;
	
	/**
	 * Liste des gestionnaires d'internationalisation enregistrés
	 *
	 * @var array
	 */
	private $_arI18nHandlers = array ();

	/**
	 * Indique si on a chargé les i18n handlers
	 *
	 * @var boolean
	 */
	private $_i18nHandlers_loaded = false;
	
	
	/**
	 * Liste des locales autorisés
	 *
	 * @var array
	 */
	public $i18n_availables = array ('en');
	
	/**
	 * Ordre dans le quel la langue sera récupéré
	 * b : browser
	 * c : cookie
	 * e : environnement
	 * bec / bce / ecb / ecb / ceb / cbe
	 * @var string
	 */
	public $i18n_order = 'cbe';
	
	/**
	 * Locale par default a utilisé
	 * @var string
	 */
	public $i18n_default = 'en';
	
	/**
	 * Définit si l'on autorise la gestion de la langue par cookie
	 * @var boolean
	 */
	public $i18n_use_cookie_locale = true;
	
	/**
	 * Test si un local est autorisé
	 * @param string $pLocal
	 * @return boolean
	 */
	public function localIsAvailable ($pLocal) {
		return in_array($pLocal, $this->i18n_availables);
	}
	
		
	/**
	 * Définit la façon de détecter la langue de l'utilisateur, les langues autorisées ainsi que la langue par défaut en cas 
	 * d'impossibilité de matcher le tout
	 * 
	 * @param $pMethod    CopixI18N::BROWSER / CopixI18N::ENVIRONMENT / code 
	 * @param $pAvailable tableau des codes langue autorisés
	 * @param $pDefault   la langue par défaut si la détection ne donne rien qui correspond à pAvailable   
	 * @deprecated A affecter directement les option de Copix
	 */
	public function setI18N ($pMethod, $pAvailable = array (), $pDefault = null) {
		
		if ($pMethod == CopixI18N::BROWSER) {
			$this->copixi18n_order = 'cbe';
		} else {
			$this->copixi18n_order = 'ceb';
		}
		
		$this->i18n_availables = $pAvailable;
		
		$this->i18n_default = $pDefault;
	}
	
	/**
	 * Charge la liste des gestionnaires d'i18n enregistrés
	 *
	 * @return array
	 */
	public function _copixi18n_loadRegisteredI18nHandlers () {
		if (!$this->_i18nHandlers_loaded){
			$this->_i18nHandlers_loaded = true;
			// Configuration des credentialhandler
			if (include (COPIX_VAR_PATH . 'config/i18n_handlers.conf.php')) {
				if (isset ($_i18n_handlers)) {
					foreach ($_i18n_handlers as $handler) {
						$this->copixi18n_registerI18nHandler ($handler['context'], $handler['name']);
					}
				}
			}
		}
	}
	
	/**
	 * Récupère la liste des gestionnaires d'i18n enregistrés
	 *
	 * @return array
	 */
	public function copixi18n_getRegisteredI18nHandlers () {
		$this->_copixi18n_loadRegisteredI18nHandlers ();
		return $this->_arI18nHandlers;
	}
	
	
	/**
	 * Enregistrement d'un gestionnaire d'i18n
	 * @param string $pContext
	 * @param string $pHandlerName Nom du handler
	 */
	public function copixi18n_registerI18nHandler ($pContext, $pHandlerName) {
		if (!Copix::installed() || CopixModule::isEnabled($pContext)) {
			CopixContext::push ($pContext);
			$this->_arI18nHandlers[] = _ioClass ($pContext.'|'.$pHandlerName);
			CopixContext::pop ();
		}
		
	}
	
	
	/* ================================================================================================================== */
	/*                                               MOTEUR DE TEMPLATES                                                  */
	/* ================================================================================================================== */

	/**
	 * Indique si il faut mettre en cache le resultat du template
	 *
	 * @var int
	 */
	public $template_caching = 0;

	/**
	 * Doit on utiliser des sous-répertoires pour la compilation des templates (Smarty uniquement).
	 *
	 * @var boolean
	 */
	public $template_use_sub_dirs = false;

	/**
	 * Nom du fichier template principal
	 *
	 * @var string
	 */
	public $mainTemplate = 'default|main.php';

	/* ================================================================================================================== */
	/*                                                 CACHES HTTP                                                        */
	/* ================================================================================================================== */	
	const ETAG_MD5_FILECONTENT = 0;
	const ETAG_FILEDATETIME = 1;
	const ETAG_MD5_FILEDATETIME_AND_SIZE = 2;
	const ETAG_DISABLED = 3;

	public $etag = self::ETAG_FILEDATETIME;
	
	/* ================================================================================================================== */
	/*                                                 COPIXHTMLHEADER                                                    */
	/* ================================================================================================================== */

	/**
	 * Mettre à true dans la config pour ajouter une console (en dev) ou éviter que les appels à console.méthode() ne créent d'erreur (en prod)
	 * Eviter autant que possible, c'est une requête HTTP inutile la plupart du temps.
	 *
	 * @var boolean
	 */
	public $copixhtmlheader_includeFirebugLite = false;

	/**
	 * Indique si on veut concaténer les fichiers javascript dans un seul, pour réduire le nombre de requêtes HTTP
	 *
	 * @var boolean
	 */
	public $copixhtmlheader_concatJS = false;
	
	/**
	 *  Valeur desactivé pour la compression du javascript
	 */
	const COMPRESS_JS_NONE = 0;
	/**
	 * Supprime les blancs et les commentaires
	 */
	const COMPRESS_JS_REDUCE = 1;
	/**
	 * Reduit le nom des variables au plus court. 
	 */
	const COMPRESS_JS_SHRINK = 2;
	/**
	 * all strings concatenated with the operator of addition are merged. 
	 */
	const COMPRESS_JS_CONCATSTRING = 4;
	/**
	 * add semi-colons at the end of block (like function, object...) if necessary. Currently, works only if shrink option is actived. 
	 */
	const COMPRESS_JS_COMPATIBILITY = 8;
	/**
	 * Actives toutes les options de la compressions JS
	 */
	const COMPRESS_JS_ALL = 15;
	
	/**
	 * Indique si on veut compresser les fichiers javascript concaté pour réduire la taille du fichier
	 *
	 * @var int
	 */
	public $copixhtmlheader_concatCompressJS = self::COMPRESS_JS_NONE;

	/**
	 * Indique si on veut concaténer les fichiers css dans un seul, pour réduire le nombre de requêtes HTTP
	 *
	 * @var boolean
	 */
	public $copixhtmlheader_concatCSS = false;

	/**
	 * Indique si on doit permettre la mise en cache par les proxys publics
	 * (Les proxys publics ne stockent pas les fichiers avec un ? dans le nom)
	 *
	 * @var boolean
	 */
	public $copixhtmlheader_concatEnableProxyCache = false;

	/* ================================================================================================================== */
	/*                                                 GESTION DES URL                                                    */
	/* ================================================================================================================== */

	/**
	 * Type de gestion des URL : default ou prepend
	 *
	 * @var string
	 */
	public $significant_url_mode = 'default';

	/**
	 * Hack pour la gestion des url type prepend sous IIS
	 *
	 * @var string
	 */
	public $significant_url_prependIIS_path_key = '__COPIX_SIGNIFICANT_URL__';

	/**
	 * Hack pour la gestion des url type prepend sous IIS : supprime les \ dans les url
	 *
	 * @var string
	 */
	public $stripslashes_prependIIS_path_key = true;

	/**
	 * Variable du tableau $_SERVERS pour récupérer le nom de la page (en général SCRIPT_NAME, PHP_SELF, REDIRECT_SCRIPT_URL)
	 *
	 * @var string
	 */
	public $url_requestedscript_variable = array ('ORIG_SCRIPT_NAME', 'SCRIPT_NAME');

	/**
	 * Indique si la fonction realpath est active (false) ou non (true) sur le serveur
	 *
	 * @var boolean
	 */
	public $realPathDisabled = false;
	
	/**
	 * Alias a CopixFile::getRealPath 
	 * @deprecated use CopixFile::getRealPath
	 * 
	 * @param string $pPath Répertoire dont on veut le realpath
	 * @return string
	 */
	public static function getRealPath ($pPath) {
		return CopixFile::getRealPath ($pPath);
	}
	
	/**
	 * La réaction a avoir par défaut lorsqu'une erreur survient
	 *
	 * @var CopixErrorHandlerAction
	 */
	public $copixerrorhandler_defaultaction = null;
	
	/**
	 * Indique si le gestionnaire d'erreur de Copix est actif ou non 
	 */
	public $copixerrorhandler_enabled = false;
	
	/**
	 * Tableau qui contient pour chaque niveau d'erreur le type de réaction a avoir
	 *
	 * @var array of CopixErrorHandlerAction
	 */
	public $copixerrorhandler_actions = array ();

	/**
	 * Version de compatibilit� a inclure automatiquement pour mootools
	 *
	 * @var string
	 */
	public $mootools_compatibility_version = '1.11';

	/* ================================================================================================================== */
	/*                                                DIVERSES METHODES                                                   */
	/* ================================================================================================================== */

	/**
	 * Constructeur privé pour le singleton
	 */
	private function __construct () {
		date_default_timezone_set ($this->default_timezone);
		
		//construction des rapprochements par défaut des erreurs que l'on pourrait traiter.
		if (!defined ('E_RECOVERABLE_ERROR')) {
			define ('E_RECOVERABLE_ERROR', E_ERROR);
		}
		if (!defined ('E_DEPRECATED')) {
			define ('E_DEPRECATED', 8192);
		}
		$this->copixerrorhandler_actions = array (
			// Les erreurs suivantes ne peuvent pas être prises en charge (cf. http://fr3.php.net/manual/en/function.set-error-handler.php) :
			// E_ERROR, E_PARSE, E_CORE_ERROR, E_CORE_WARNING, E_COMPILE_ERROR, E_COMPILE_WARNING
			E_WARNING           => new CopixErrorHandlerAction (false, CopixLog::WARNING),
			E_NOTICE            => new CopixErrorHandlerAction (false, CopixLog::NOTICE),
			E_USER_ERROR        => new CopixErrorHandlerAction (true,  CopixLog::ERROR),
			E_USER_WARNING      => new CopixErrorHandlerAction (false, CopixLog::WARNING),
			E_USER_NOTICE       => new CopixErrorHandlerAction (false, CopixLog::NOTICE),
			E_STRICT            => new CopixErrorHandlerAction (false, CopixLog::NOTICE),
			E_RECOVERABLE_ERROR => new CopixErrorHandlerAction (false, CopixLog::WARNING),
			E_DEPRECATED        => new CopixErrorHandlerAction (false, CopixLog::WARNING),
		);
		$this->copixerrorhandler_defaultaction = new CopixErrorHandlerAction (true, CopixLog::ERROR);
		
		// Configuration des plugins
		if (is_readable (COPIX_VAR_PATH . 'config/plugins.conf.php')) {
			require (COPIX_VAR_PATH . 'config/plugins.conf.php');
			if (isset ($_plugins)) {
				foreach ($_plugins as $pluginName) {
					CopixPluginRegistry::register ($pluginName);
				}
			}
		}

		// Configuration des profils de log
		foreach (CopixLogConfigFile::getList () as $profile) {
			$this->copixlog_registerProfile ($profile);
		}

		// Configuration des profils de cache
		if (is_readable (COPIX_VAR_PATH . 'config/cache_profiles.conf.php')) {
			require (COPIX_VAR_PATH . 'config/cache_profiles.conf.php');
			if (isset ($_cache_types)) {
				foreach ($_cache_types as $type) {
					$this->copixcache_registerType ($type);
				}
			}
		}
	}
	
	/**
	 * Chargement de la configuration des bases de données. 
	 */
	private function _copixdb_loadProfiles (){
		if (!$this->_db_loaded){
			$this->_db_loaded = true;
			// Configuration des profils de base de données
			if (is_readable ($this->copixdb_getConfigFilePath ())) {
				require ($this->copixdb_getConfigFilePath ());
				if (isset ($_db_profiles)) {
					foreach ($_db_profiles as $profileName => $profileInformations) {
						$this->copixdb_defineProfile ($profileName, $profileInformations['driver'] . ':' . $profileInformations['connectionString'], $profileInformations['user'], $profileInformations['password'], $profileInformations['extra']);
						if ($profileInformations['default']) {
							if ($this->_copixdb_defaultProfileName === null){
								//Comme on charge la configuration des bases a tout moment, 
								//on ne redéfinit pas le profil par défaut s'il a été défini
								//dans le code de l'utilisateur
								$this->copixdb_defineDefaultProfileName ($profileName);
							}
						}
					}

					if (isset ($_db_default_profile)) {
						if ($this->_copixdb_defaultProfileName === null){
							//Voir commentaire du dessus par rapport aux profils par défaut
							$this->copixdb_defineDefaultProfileName ($_db_default_profile);
						}
					}
				}
			}
		}
	}

	/**
	 * Retourne un objet contenant des infos sur la paramètre (propriétés : name, id)
	 *
	 * @param string $pId Nom du paramètre, sous la forme [module|]name
	 * @return object
	 */
	private static function _getParamInfos ($pId) {
		if (($pos = strpos ($pId, '|')) === false) {
			$module = CopixContext::get ();
			$pId = $module . '|'.$pId;
		}else {
			$module = substr ($pId, 0, $pos);
			if (! $module){
				$module = "default";
				$pId = $module.$pId;
			}
		}

		$toReturn = new StdClass ();
		$toReturn->module = $module;
		$toReturn->id = $pId;
		return $toReturn;
	}

	/**
	 * Retourne le singleton
	 *
	 * @return CopixConfig
	 */
	public static function instance () {
		if (CopixConfig::$_instance === false) {
			CopixConfig::$_instance = new CopixConfig ();
		}
		return CopixConfig::$_instance;
	}
	
	/**
	 * Force un rechargememt de la configuration à partir des fichiers.
	 * 
	 * Dans les faits, détruit l'instance en cours.
	 */
	public static function reload () {
		CopixConfig::$_instance = false;
	}

	/**
	 * Retourne la valeur du paramètre $pId
	 *
	 * @param string $pId Nom du paramètre, sous la forme [module|]name
	 * @return mixed
	 */
	public static function get ($pId) {
		// dans copix 3.0.x, le thème était dans admin|defaultThemeId
		// ça a été changé pour être dans le module default, c'est mieux placé (c'était la seule config du module admin, et ça permet de centraliser les configs)
		if ($pId == 'admin|defaultThemeId' || (CopixContext::get () == 'admin' && $pId == 'defaultThemeId')) {
			$pId = 'default|publicTheme';
			_log ('La config admin|defaultThemeId n\'existe plus, il faut utiliser default|publicTheme et default|adminTheme.', 'errors', CopixLog::ERROR);
		}
		$param = self::_getParamInfos ($pId);
		return CopixConfig::instance ()->_getModuleConfig ($param->module)->get ($param->id);
	}

	/**
	 * Regarde si le paramètre donné existe.
	 *
	 * @param string $pId Nom du paramètre, sous la forme [module|]name
	 * @return boolean
	 */
	public static function exists ($pId) {
		$param = self::_getParamInfos ($pId);
		return CopixConfig::instance ()->_getModuleConfig ($param->module)->exists ($param->id);
	}

	/**
	 * Retourne tous les paramètres d'un module
	 *
	 * @param string $pModuleName Nom du module
	 * @return array
	 */
	public static function getParams ($pModuleName) {
		if (!(CopixModule::isEnabled ($pModuleName))) {
			return array ();
		}
		return CopixConfig::instance ()->_getModuleConfig ($pModuleName)->getParams ();
	}

	/**
	 * Modifie la valeur d'un paramètre
	 *
	 * @param string $pId Nom du paramètre, sous la forme [module|]name
	 * @param mixed $pValue Nouvelle valeur
	 */
	public static function set ($pId, $pValue) {
		// dans copix 3.0.x, le thème était dans admin|defaultThemeId
		// ça a été changé pour être dans le module default, c'est mieux placé (c'était la seule config du module admin, et ça permet de centraliser les configs)
		if ($pId == 'admin|defaultThemeId' || (CopixContext::get () == 'admin' && $pId == 'defaultThemeId')) {
			$pId = 'default|publicTheme';
			_log ('La config admin|defaultThemeId n\'existe plus, il faut utiliser default|publicTheme et default|adminTheme.', 'errors', CopixLog::ERROR);
		}
		$param = self::_getParamInfos ($pId);
		CopixConfig::instance ()->_getModuleConfig ($param->module)->set ($param->id, $pValue);
	}

	/**
	 * Retourne un singleton de CopixModuleConfig
	 *
	 * @param string $pKind Type de module dont on veut les données (moduleName, copix:, plugin:name, ..., ...)
	 * @return CopixModuleConfig
	 */
	private function _getModuleConfig ($pKind) {
		if (isset ($this->_configModule[$pKind])) {
			return $this->_configModule[$pKind];
		}
		return $this->_configModule[$pKind] = new CopixModuleConfig ($pKind);
	}

	/**
	 * Retourne l'OS du serveur
	 *
	 * @return string
	 */
	public static function getOSName () {
		return substr (PHP_OS, 0, (($pos = strpos (PHP_OS, ' ')) === false) ? strlen (PHP_OS) : $pos);
	}

	/**
	 * Indique si l'OS du serveur est Windows ou non
	 *
	 * @return boolean
	 */
	public static function osIsWindows () {
		return (strtoupper (substr (CopixConfig::getOsName (), 0, 3)) === 'WIN');
	}

	/**
	 * Définition du mode d'utilisation du framework
	 *
	 * @param int $ Mode de configuration à définir (DEVEL, PRODUCTION, FORCE_INITIALISATION)
	 * @throws CopixException
	 */
	public function setMode ($pMode) {
		if (!in_array ($pMode, array (self::DEVEL, self::PRODUCTION, self::FORCE_INITIALISATION))) {
			throw new CopixException (_i18n ('copix:copixerrorhandler.unknownMode', $pMode));
		}
		switch ($this->_mode = $pMode){
			case self::DEVEL:
				$this->compile_check = true;
				$this->force_compile = false;
				$this->copixauth_cache = false;
				$this->cacheEnabled = true;
				$this->apcEnabled   = true;
				$this->i18n_missingKeyLaunchException = true;
				$this->session_secure_with_cookie = false;
				break;
			case self::PRODUCTION:
				$this->compile_check = false;
				$this->force_compile = false;
				$this->copixauth_cache = true;
				$this->cacheEnabled = true;
				$this->apcEnabled   = true;
				$this->i18n_missingKeyLaunchException = false;
				$this->session_secure_with_cookie = true;
				break;
			case self::FORCE_INITIALISATION:
				$this->compile_check = true;
				$this->force_compile = true;
				$this->copixauth_cache = false;
				$this->cacheEnabled = false;
				$this->apcEnabled   = false;
				$this->i18n_missingKeyLaunchException = true;
				$this->session_secure_with_cookie = false;				
		}
	}

	/**
	 * Récupération du mode de fonctionnement du framework
	 *
	 * @return int Mode de fonctionnement actuellement configuré
	 */
	public function getMode () {
		return $this->_mode;
	}
	
	/**
	 * Execution de code pour la configuration
	 * Ce code doit etre mis après la construction de copixconfig
	 */
	public function initialize () {
		if ($this->copixerrorhandler_enabled){
			Copix::setErrorHandler (new CopixErrorHandler ($this));
		}

		if ($this->sessionName != null) {
			session_name ($this->sessionName);
		}

		$this->_copixtpl_paths = array_merge ($this->_copixtpl_paths, $this->copixtheme_getPaths ());
		return $this;
	}
	
	/**
	 * Chargement (si nécessaire) de la configuration des UserHandler
	 */
	private function _copixauth_loadRegisteredUserHandlers (){
		if (!$this->_userhandlers_loaded){
            $this->_userhandlers_loaded = true;
			// Configuration des userhandler
			if (include (COPIX_VAR_PATH . 'config/user_handlers.conf.php')) {
				if (isset ($_user_handlers)) {
					foreach ($_user_handlers as $handler) {
						$this->copixauth_registerUserHandler ($handler);
					}
				}
			}
		}
	}
	
	private function _copixauth_loadRegisteredGroupHandlers (){
		if (!$this->_grouphandlers_loaded){
            $this->_grouphandlers_loaded = true;
			// Configuration des grouphandler
			if (include (COPIX_VAR_PATH . 'config/group_handlers.conf.php')) {
				if (isset ($_group_handlers)) {
					foreach ($_group_handlers as $handler) {
						$this->copixauth_registerGroupHandler ($handler);
					}
				}
			}
		}
	}
	
	public function _copixauth_loadRegisteredCredentialHandlers () {
		if (!$this->_credentialhandlers_loaded){
            $this->_credentialhandlers_loaded = true;
			// Configuration des credentialhandler
			if (include (COPIX_VAR_PATH . 'config/credential_handlers.conf.php')) {
				if (isset ($_credential_handlers)) {
					foreach ($_credential_handlers as $handler) {
						$this->copixauth_registerCredentialHandler ($handler);
					}
				}
			}
		}
	}
	
	/**
	 * Chargement d'un fichier de configuration donné
	 *
	 * @param string $pConfigFile le chemin du fichier de configuration a charger
	 * @return CopixConfig
	 */
	public function load ($pConfigFile){
		require ($pConfigFile);
        $this->_db_loaded = false;
        $this->_userhandlers_loaded = false;
        $this->_grouphandlers_loaded = false;
        $this->_credentialhandlers_loaded = false;
		return $this;
	}
}