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
	 * Si l'on souhaite que plusieurs sites qui partagent la mê session utilisent un cache de droit diffént
	 *  c'est ici que l'on spéfie l'identifiant de cache concerné
	 * @var string
	 */
	public $copixauth_sharedcredentialskey = 'copix';

	/**
	 * Si l'on souhaite que plusieurs sites qui partagent la mê session utilisent un cache d'apaprtenance aux groupes
	 *  c'est ici que l'on spéfie l'identifiant de cache concerné
	 * @var string
	 */
	public $copixauth_sharedgroupskey = 'copix';	


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
	static private $_instance = false;

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
	 * @deprecated
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
	 * Si la configuration de PHP autorise la surcharge de unserialize_callback_func
	 *
	 * @var boolean
	 */
	public $overrideUnserializeCallbackEnabled = true;

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
	 * Indique si l'on souhaite profiter des fonctionnalités d'APC
	 *
	 * @var boolean
	 */
	public $apcEnabled = false;

	/**
	 * Mode de fonctionnement de l'application par défaut
	 *
	 * @var int
	 */
	private $_mode = self::DEVEL;

	/* ================================================================================================================== */
	/*                                                  COPIXDEBUG                                                        */
	/* ================================================================================================================== */
	
	/**
	 * Nombre de niveaux de dump maximum à afficher
	 * 
	 * @var int
	 */
	public $copixdebug_maxDumpLevels = 2;
	
	/**
	 * Indique si on doit afficher la section Constantes par défaut
	 *
	 * @var boolean
	 */
	public $copixdebug_showConstantes = false;
	
	/**
	 * Indique si on doit afficher la section des propriétés non déclarés par défaut
	 *
	 * @var boolean
	 */
	public $copixdebug_showNotDeclaredProperties = true;
	
	/**
	 * Indique si on doit afficher la section des propriétés publiques par défaut
	 *
	 * @var boolean
	 */
	public $copixdebug_showPublicProperties = true;
	
	/**
	 * Indique si on doit afficher la section des propriétés protégées par défaut
	 *
	 * @var boolean
	 */
	public $copixdebug_showProtectedProperties = false;
	
	/**
	 * Indique si on doit afficher la section des propriétés privées par défaut
	 *
	 * @var boolean
	 */
	public $copixdebug_showPrivateProperties = false;
	
	/**
	 * Indique si on doit afficher la section des méthodes publiques par défaut
	 *
	 * @var boolean
	 */
	public $copixdebug_showPublicMethods = true;
	
	/**
	 * Indique si on doit afficher la section des méthodes protégées par défaut
	 *
	 * @var boolean
	 */
	public $copixdebug_showProtectedMethods = false;
	
	/**
	 * Indique si on doit afficher la section des méthodes privées par défaut
	 *
	 * @var boolean
	 */
	public $copixdebug_showPrivateMethods = false;
	
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
	 * Liste des gestionnaires de groupes enregistrés
	 *
	 * @var array
	 */
	private $_arGroupHandlers = array ();

	/**
	 * Liste des gestionnaires de droits enregistrés.
	 *
	 * @var array
	 */
	private $_arCredentialHandlers = array ();

	/**
	 * Si l'on souhaite utiliser le cache lors du test des droits.
	 * Cette option n'est à désactiver que dans le contexte de développement.
	 *
	 * @var boolean
	 */
	public $copixauth_cache = true;

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
		return isset ($this->_arUserHandlers[$pHandlerName]);
	}

	/**
	 * Supression de tous les gestionnaires utilisateurs enregistrés
	 */
	public function copixauth_clearUserHandlers () {
		$this->_arUserHandlers = array ();
	}

	/**
	 * Retourne la liste des gestionnaires d'utilisateurs enregistrés
	 *
	 * @return array
	 */
	public function copixauth_getRegisteredUserHandlers () {
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
		return isset ($this->_arGroupHandlers[$pHandlerName]);
	}

	/**
	 * Efface la liste des gestionnaires de groupes enregistrés.
	 */
	public function copixauth_clearGroupHandlers () {
		$this->_arGroupHandlers = array ();
	}

	/**
	 * Retourne la liste des gestionnaires de groupes enregistrés
	 *
	 * @return array
	 */
	public function copixauth_getRegisteredGroupHandlers () {
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
		return isset ($this->_arCredentialHandlers[$pHandlerName]);
	}

	/**
	 * Récupère la liste des gestionnaires de droit enregistrés
	 *
	 * @return array
	 */
	public function copixauth_getRegisteredCredentialHandlers () {
		return $this->_arCredentialHandlers;
	}

	/**
	 * Efface la liste des gestionnaires de droits enregistrés
	 */
	public function copixauth_clearCredentialHandlers () {
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
	 * Retourne la liste des logs configrués
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
		// Le level par défaut est CopixLog::FATAL_ERROR
		if (!isset ($pLogDefinition['level'])) {
			$pLogDefinition['level'] = CopixLog::INFORMATION;
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
		// durée 
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
		return array_keys ($this->_copixdb_profiles);
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
		if ($pName === null) {
			$pName = $this->copixdb_getDefaultProfileName ();
			if ($pName === null) {
				throw new CopixDBException (_i18n ('copix:copix.error.db.noDefaultProfil'));
			}
		}
		if (isset ($this->_copixdb_profiles[$pName])) {
			return $this->_copixdb_profiles[$pName];
		}
		throw new CopixDBException (_i18n ('copix:copix.error.db.unknowProfil', array ($pName)));
	}

	/* ================================================================================================================== */
	/*                                                     PLUGINS                                                        */
	/* ================================================================================================================== */

	/* * TODO: arPluginsPath désactivé jusqu'à ce qu'on l'implémente vraiment, cf #151.
	 * Chemin ou l'on doit aller chercher les plugins
	 *
	 * @var array
	 * /
	 public $arPluginsPath = array ();
	 */

	/**
	 * Plugins enregistrés
	 *
	 * @var array
	 */
	private $_plugin_registered = array ();

	/**
	 * Enregistrement d'un plugin
	 *
	 * @param string $pPluginName Nom du plugin à enregistrer
	 */
	public function plugin_register ($pPluginName) {
		if (!in_array ($pPluginName, $this->_plugin_registered)) {
			$this->_plugin_registered[] = $pPluginName;
		}
	}

	/**
	 * Récupération des plugins enregistrés
	 *
	 * @return array
	 */
	public function plugin_getRegistered () {
		return $this->_plugin_registered;
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
	
	/* ================================================================================================================== */
	/*                                                CHEMINS DES THEMES                                                  */
	/* ================================================================================================================== */
	
	/**
	 * Liste des chemins des thèmes.
	 */
	private $_copixtpl_paths = array();

	/**
	 * Ajoute un nouveau chemin pour les thèmes
	 *
	 * @param string $pPath
	 */
	public function copixtpl_addPath($pPath) {
		$pPath = self::getRealPath($pPath);
		if(substr($pPath, -1) != DIRECTORY_SEPARATOR) {
			$pPath .= DIRECTORY_SEPARATOR;
		}
		$this->_copixtpl_paths[$pPath] = $pPath;
	}
	
	/**
	 * Remets à zéro la liste des chemins des thèmes.
	 *
	 */
	public function copixtpl_clearPaths() {
		$this->_copixtpl_paths = array();
	}
	
	/**
	 * Récupère la liste des chemins des thèmes.
	 *
	 * @return array
	 */
	public function copixtpl_getPaths() {
		return $this->_copixtpl_paths;
	}
	
	/* ================================================================================================================== */
	/*                                               INTERNATIONALISATION                                                  */
	/* ================================================================================================================== */

	/**
	 * Code langage par defaut
	 *
	 * @var string
	 */
	public $default_language = 'fr';

	/**
	 * Code pays par defaut
	 *
	 * @var string
	 */
	public $default_country = 'FR';

	/**
	 * Charset à utiliser par défaut
	 *
	 * @var string
	 */
	public $default_charset = 'UTF-8';

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
	 * Indique si l'on souhaite générer une exception en cas de clef manquante. Si non, CopixI18N::get retournera le nom de la clef 
	 * @var boolean
	 */
	public $i18n_missingKeyLaunchException = true;

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
	 * Renvoi le répertoire réel (utilise realpath si activé sur le serveur, sinon, renvoie un équivalent)
	 *
	 * @param string $pPath Répertoire dont on veut le realpath
	 * @return string
	 */
	static public function getRealPath ($pPath) {
		$config = CopixConfig::instance ();

		if ($config->realPathDisabled === false) {
			$realPath = realpath ($pPath);
			$last = substr ($pPath, strlen ($pPath) - 1);
			// si on a mi un caractère de fin de répertoire
			if ($last == '\\' || $last == '/') {
				$realPath .= (CopixConfig::osIsWindows ()) ? '\\' : '/';
			}
			return $realPath;
		} else {
			$result = array ();
			$pPathA = preg_split ('/[\/\\\]/', $pPath);
			if (! $pPathA[0]) {
				$result[] = '';
			}
			foreach ($pPathA as $key => $dir) {
				if ($dir == '..') {
					if (end ($result) == '..') {
						$result[] = '..';
					} else if (!array_pop ($result)) {
						$result[] = '..';
					}
				} else if ($dir && $dir != '.') {
					$result[] = $dir;
				}
			}
			if (!end ($pPathA)) {
				$result[] = '';
			}
			return implode ((CopixConfig::osIsWindows ()) ? '\\' : '/', $result);
		}
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
	public $copixerrorhandler_enabled = true;
	
	/**
	 * Tableau qui contient pour chaque niveau d'erreur le type de réaction a avoir
	 *
	 * @var array of CopixErrorHandlerAction
	 */
	public $copixerrorhandler_actions = array ();

	/* ================================================================================================================== */
	/*                                                DIVERSES METHODES                                                   */
	/* ================================================================================================================== */

	/**
	 * Executé juste après le session_start
	 */
	public function afterSessionStart () {
	}

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
		if (file_exists (COPIX_VAR_PATH . 'config/plugins.conf.php')) {
			require (COPIX_VAR_PATH . 'config/plugins.conf.php');
			if (isset ($_plugins)) {
			foreach ($_plugins as $pluginName) {
				$this->plugin_register ($pluginName);
			}
		}
		}
		// Configuration des profils de log
		if (file_exists (COPIX_VAR_PATH . 'config/log_profiles.conf.php')) {
			require (COPIX_VAR_PATH . 'config/log_profiles.conf.php');
			if (isset ($_log_profiles)) {
				foreach ($_log_profiles as $profile) {
					$this->copixlog_registerProfile ($profile);
				}
			}
		}
		// Configuration des profils de cache
		if (file_exists(COPIX_VAR_PATH . 'config/cache_profiles.conf.php')) {
			require (COPIX_VAR_PATH . 'config/cache_profiles.conf.php');
			if (isset ($_cache_types)) {
				foreach ($_cache_types as $type) {
					$this->copixcache_registerType ($type);
				}
			}
		}
		// Configuration des profils de base de données
		if (file_exists (COPIX_VAR_PATH . 'config/db_profiles.conf.php')) {
			require (COPIX_VAR_PATH . 'config/db_profiles.conf.php');
			if (isset ($_db_profiles)) {
				foreach ($_db_profiles as $profileName => $profileInformations) {
					$this->copixdb_defineProfile ($profileName, $profileInformations['driver'] . ':' . $profileInformations['connectionString'], $profileInformations['user'], $profileInformations['password'], $profileInformations['extra']);
					if ($profileInformations['default']) {
						$this->copixdb_defineDefaultProfileName ($profileName);
					}
				}

				if (isset ($_db_default_profile)) {
					$this->copixdb_defineDefaultProfileName ($_db_default_profile);
				}
			}
		}
		
		// Configuration des chemins des thèmes par défaut
		$defaultThemePath = COPIX_PROJECT_PATH.'themes'.DIRECTORY_SEPARATOR;
		$this->_copixtpl_paths = array($defaultThemePath => $defaultThemePath);
	}

	/**
	 * Retourne un objet contenant des infos sur la paramètre (propriétés : name, id
	 *
	 * @param string $pId Nom du paramètre, sous la forme [module|]name
	 * @return object
	 */
	private static function _getParamInfos ($pId) {
		if (($pos = strpos ($pId, '|')) === false) {
			$module = CopixContext::get () . '|';
			$pId = $module . $pId;
		}else {
			$module = substr ($pId, 0, $pos) . '|';
		}
		if ($module == '|') {
			$module = 'default|';
			$pId = 'default' . $pId;
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
	 * Retourne la valeur du paramètre $pId
	 *
	 * @param string $pId Nom du paramètre, sous la forme [module|]name
	 * @return mixed
	 */
	public static function get ($pId) {
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
		return CopixConfig::instance ()->_getModuleConfig ($pModuleName . '|')->getParams ();
	}

	/**
	 * Modifie la valeur d'un paramètre
	 *
	 * @param string $pId Nom du paramètre, sous la forme [module|]name
	 * @param mixed $pValue Nouvelle valeur
	 */
	public static function set ($pId, $pValue) {
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
		$this->_configModule[$pKind] = new CopixModuleConfig ($pKind);
		return $this->_configModule[$pKind];
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
			throw new CopixException (_i18n ('copix:errors.unknownMode'));
		}
		switch ($this->_mode = $pMode){
			case self::DEVEL:
				$this->compile_check = true;
				$this->force_compile = false;
				$this->copixauth_cache = false;
				$this->cacheEnabled = true;
				$this->apcEnabled   = true;
				$this->i18n_missingKeyLaunchException = true;
				break;
			case self::PRODUCTION:
				$this->compile_check = false;
				$this->force_compile = false;
				$this->copixauth_cache = true;
				$this->cacheEnabled = true;
				$this->apcEnabled   = true;
				$this->i18n_missingKeyLaunchException = false;
				break;
			case self::FORCE_INITIALISATION:
				$this->compile_check = false;
				$this->force_compile = false;
				$this->copixauth_cache = false;
				$this->cacheEnabled = false;
				$this->apcEnabled   = false;
				$this->i18n_missingKeyLaunchException = true;
		}
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
	 * Récupération du mode de fonctionnement du framework
	 *
	 * @return int Mode de fonctionnement actuellement configuré
	 */
	public function getMode () {
		return $this->_mode;
	}
}
?>
