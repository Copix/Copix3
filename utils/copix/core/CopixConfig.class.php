<?php
/**
* @package		copix
* @subpackage	core
* @author		Croës Gérald, Bertrand Yan
* @copyright 	CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* fichier de configuration principal du framework
* definit une classe dont les propriétés representent tout les paramètres
* du framework, avec leurs valeurs par défaut.
* Pour indiquer des valeurs spécifiques, il faut le faire via le fichier
* de configuration copix.conf.php
* @package   copix
* @subpackage core
*/
class CopixConfig {
	/**
	 * Constante pour représenter le mode de fonctionnement ou tout sera forcé à l'initialisation 
	 */
	const FORCE_INITIALISATION = 0;

	/**
	 * Constante pour représenter le mode de fonctionnement en développement
	 */
	const DEVEL = 1;
	
	/**
	 * Constante pour représenter le mode de fonctionnement en production
	 */
	const PRODUCTION = 2;
	
	/* ========================================= Authentification */
    /**
     * Liste des gestionnaires enregistrés.
     * @var array
     */
    private $_arUserHandlers = array ();
    
    /**
     * Liste des gestionnaires de groupes enregistrés
     * @var array
     */
	private $_arGroupHandlers = array ();
	
	/**
	 * Liste des gestionnaires de droits enregistrés.
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
     * @param mixed	$pHandlerDefinition string (nom handler) ou array pour décrire le handler
     * @return void
     */
    public function copixauth_registerUserHandler ($pHandlerDefinition){
    	//Si le handler n'est pas un tableau, alors on crée le tableau
    	if (!is_array ($pHandlerDefinition)){
			$pHandlerDefinition = array ('name'=>$pHandlerDefinition);    		
    	}
    	$this->_arUserHandlers[$pHandlerDefinition['name']] = $pHandlerDefinition; 
    }
    
    /**
     * Indique si le handler d'utilisateur donné est présent
     * 
     * @param	string	$pHandlerName	le nom du handler à tester
     * @return 	boolean
     */
    public function copixauth_isRegisteredUserHandler ($pHandlerName){
    	return isset ($this->_arUserHandlers[$pHandlerName]);
    }
    
    /**
     * Supression de tous les gestionnaires utilisateurs enregistrés
     */
    public function copixauth_clearUserHandlers (){
    	$this->_arUserHandlers = array ();
    }
    
    /**
     * Retourne la liste des gestionnaires utilisateurs enregistrés
     * @return array
     */
    public function copixauth_getRegisteredUserHandlers (){
    	return $this->_arUserHandlers;
    }
    
    /**
     * Enregistrement de gestionnaires de groupes
     * 
     * @param 	mixed 	$pHandlerDefinition string (nom du handler) ou array pour décrire le handler
     * @return void 
     */
    public function copixauth_registerGroupHandler ($pHandlerDefinition){
    	//Si le handler n'est pas un tableau, alors on crée le tableau
    	if (!is_array ($pHandlerDefinition)){
    		$pHandlerDefinition = array ('name'=>$pHandlerDefinition);
    	}

    	//valeurs par défaut 
		if (!isset ($pHandlerDefinition['required'])){
			$pHandlerDefinition['required'] = false;
		}
		$this->_arGroupHandlers[$pHandlerDefinition['name']] = $pHandlerDefinition;
    }

    /**
     * Indique si le gestionnaire de groupe donné est enregistré
     *
     * @param 	string	$pHandlerName	le nom du groupe à tester
     * @return boolean
     */
    public function copixauth_isRegisteredGroupHandler ($pHandlerName){
    	return isset ($this->_arGroupHandlers[$pHandlerName]);
    }
    
    /**
     * Efface la liste des gestionnaires de groupes enregistrés.
     */
    public function copixauth_clearGroupHandlers (){
    	$this->_arGroupHandlers = array ();
    }
    
    /**
     * Retourne la liste des gestionnaires de groupes enregistrés
     * @return array
     */
    public function copixauth_getRegisteredGroupHandlers (){
    	return $this->_arGroupHandlers;
    }
    
    /**
     * Enregistrement de gestionnaires de droits
     * 
     * @param 	mixed 	$pHandlerDefinition	string (nom du handler) ou array pour décrire le handler
     * @return void
     */
    public function copixauth_registerCredentialHandler ($pHandlerDefinition){
    	//Si le handler n'est pas un tableau, alors on crée le tableau
    	if (!is_array ($pHandlerDefinition)){
    		$pHandlerDefinition = array ('name'=>$pHandlerDefinition);
    	}

    	//paramètres par défaut du handler
		if (!isset ($pHandlerDefinition['stopOnSuccess'])){
			$pHandlerDefinition['stopOnSuccess'] = false;
		}
		if (!isset ($pHandlerDefinition['stopOnFailure'])){
		    $pHandlerDefinition['stopOnFailure'] = true;
		}
		if (!isset ($pHandlerDefinition['handle'])){
		    $pHandlerDefinition['handle'] = 'all';
		}
		if ((!is_array ($pHandlerDefinition['handle'])) && ($pHandlerDefinition['handle'] !== 'all')){
			$pHandlerDefinition['handle'] = array ($pHandlerDefinition['handle']);
		}
		if (!isset ($pHandlerDefinition['handleExcept'])){
			$pHandlerDefinition['handleExcept'] = array ();
		}
		$this->_arCredentialHandlers[$pHandlerDefinition['name']] = $pHandlerDefinition;
    }
    
    /**
     * Indique si le gestionnaire de droit donné est enregistré ou non
     * 
     * @param	string	$pHandlerName	le nom du gestionnaire à tester
     * @return boolean
     */
    public function copixauth_isRegisteredCredentialHandler ($pHandlerName){
    	return isset ($this->_arCredentialHandlers[$pHandlerName]);
    }   
    
    /**
     * Récupère la liste des gestionnaires de droit enregistrés
     * 
     * @return array 
     */
    public function copixauth_getRegisteredCredentialHandlers (){
    	return $this->_arCredentialHandlers;
    }
    
    /**
     * Efface la liste des gestionnaires de droits enregistrés
     * @return void
     */
    public function copixauth_clearCredentialHandlers (){
    	$this->_arCredentialHandlers = array ();
    }
    
    /**
     * Définition des logs par défauts
     * @var array
     */
    private $_arLogDefinition = array();

	/**
	 * Type de log par défaut.
	 */
    private $_copixlog_defaultTypeName = 'default';
    
    /**
     * Retourne la liste des logs configrués.
     * @return array
     */
    public function copixlog_getRegistered (){
    	return array_keys ($this->_arLogDefinition);
    }
    
    /**
     * Enregistrement d'un type de log
     * @param	array	$pLogDefinition	Lé définition du log à enregistrer
     */
    public function copixLog_registerProfile ($pLogDefinition) {
	    //On met toujours la définition du log sous la forme d'un tableau
	    if (!is_array ($pLogDefinition)) {
	        $pLogDefinition = array ('name'=>$pLogDefinition);
	    }
   	 	//La stratégie par défaut est file
	    if (!isset($pLogDefinition['handle'])) {
	        $pLogDefinition['handle'] = 'all';
	    }
	    //La stratégie par défaut est file
	    if (!isset($pLogDefinition['strategy'])) {
	        $pLogDefinition['strategy'] = 'file';
	    }
	    //Le level par défaut est CopixLog::FATAL_ERROR
	    if (!isset($pLogDefinition['level'])) {
	        $pLogDefinition['level'] = CopixLog::INFORMATION;
	    }
	    //log actif ?
		if (!isset ($pLogDefinition['enabled'])){
			$pLogDefinition['enabled'] = true;
		}
	
	    //Sauvegarde des infos sur les logs
	    if (isset($pLogDefinition['name'])) {
	        $this->_arLogDefinition[$pLogDefinition['name']] = $pLogDefinition;
	    }		
    }
    
    /**
     * Retourne les handler capables de gérer le type de log donné
     */
    public function copixlog_getProfileFromType ($pType){
		$arProfil = array ();
		
		foreach ($this->_arLogDefinition as $keys=>$profil){
			if (is_array ($profil['handle'])){
				if (in_array ($pType, $profil['handle'])){
					$arProfil[] = $profil;
				}
			}else{
				if ($profil['handle']=="all" || $profil['handle'] == $pType){
					if(isset ($profil['handleExcept'])) {							
						if (in_array ($pType, is_array ($profil['handleExcept']) ? $profil['handleExcept']: array($profil['handleExcept']))){
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
     * @return mixed les informations de configuration du type de log
     *  	null si le type de log n'est pas configuré
     */
    public function copixlog_getProfile ($pName) {
    	if ($pName !== null){
    		if (isset($this->_arLogDefinition[$pName])) {
    			return $this->_arLogDefinition[$pName];
    		}
    	}
    	return $this->copixlog_getDefaultType ();
    }
    
    /**
     * Récupération de la liste des profiles enregistrés
     * @return array
     */
	public function copixlog_getRegisteredProfiles (){
		return $this->_arLogDefinition;
	} 
    
    /**
     * Retourne les données de configuration pour le type de log configuré par défaut
     * @return array ou null si aucun type de log par défaut
     */
    public function copixlog_getDefaultType (){
    	//si aucun log demandé par défaut, null
    	if (($typeName = $this->copixlog_getDefaultTypeName ()) === null){
    		return null;
    	}
    	//si le type par défaut n'est pas configuré, null
    	if (!isset ($this->_arLogDefinition[$typeName])){
    		return null;
    	}
    	return $this->_arLogDefinition[$typeName];
    }
    
    /**
     * Récupération du log par défaut
     * @return mixed Un log
     */
    public function copixlog_getDefaultTypeName () {
        return $this->_copixlog_defaultTypeName;
    }
    
    /**
     * Permet de changer le log par défaut
     * @param string $pLog
     */
    public function copixlog_setDefaultTypeName ($pLog) {
        $this->_copixlog_defaultTypeName = $pLog;
    }
    
    /**
     * Tableau de définition sur les types de cache
     */
    private $_arCacheDefinition = array();
    
    /**
     * Le nom du type de cache à utiliser par défaut
     */
    private $_copixcache_defaultTypeName = 'default';
    
    /**
     * Enregistrement d'un type de cache
     * @param mixed $pCacheDefinition tableau contenant le système de cache
     */
    public function copixcache_registerType ($pCacheDefinition) {
        //On met toujours la définition du cache sous la forme d'un tableau
        if (!is_array ($pCacheDefinition)) {
            $pCacheDefinition = array('name'=>$pCacheDefinition);
        }
        //La stratégie par défaut est file
        if (!isset ($pCacheDefinition['strategy'])) {
            $pCacheDefinition['strategy'] = 'file';
        }
		//si file et pas de répertoire, on place par défaut comme répertoire le nom du cache
        if ($pCacheDefinition['strategy'] == 'file'&& !isset($pCacheDefinition['dir'])) {
            $pCacheDefinition['dir'] = $pCacheDefinition['name'];
        }
        //cache actif ?
		if (!isset ($pCacheDefinition['enabled'])){
			$pCacheDefinition['enabled'] = true;
		}
		//Liens entre les caches ?
		if (!isset ($pCacheDefinition['link'])){
			$pCacheDefinition['link'] = '';
		}
		//durée ?
		if (!isset ($pCacheDefinition['duration'])){
			$pCacheDefinition['duration'] = 0;
		}

        //Sauvegarde des infos sur le cache
        $this->_arCacheDefinition[$pCacheDefinition['name']] = $pCacheDefinition;
    }
    
    /**
     * Recupération des données de configuration pour un type de cache
     * 
     * @param string $pName Nom du type de cache dont on souhaite récupérer les informations
     * @return mixed les informations de configuration du type de cache
     *  	null si le type de cache n'est pas configuré
     */
    public function copixcache_getType ($pName) {
    	if ($pName !== null){
    		if (isset ($this->_arCacheDefinition[$pName])) {
    			return $this->_arCacheDefinition[$pName];
    		}
    	}
    	
    	return $this->copixcache_getDefaultType ();
    }
    
    /**
     * Retourne les données de configuration pour le type de cache configuré par défaut
     * @return array ou null si aucun type de cache par défaut
     */
    public function copixcache_getDefaultType (){
    	//si aucun cache demandé par défaut, null
    	if (($typeName = $this->copixcache_getDefaultTypeName ()) === null){
    		return null;
    	}
    	//si le type par défaut n'est pas configuré, null
    	if (!isset ($this->_arCacheDefinition[$typeName])){
    		return null;
    	}
    	return $this->_arCacheDefinition[$typeName];
    }
    
    /**
     * Récupération du cache par défaut
     * @return mixed Un cache
     */
    public function copixcache_getDefaultTypeName () {
        return $this->_copixcache_defaultTypeName;
    }
    
    /**
     * Permet de changer le cache par défaut
     * @param string $pCache
     */
    public function copixcache_setDefaultTypeName ($pCache) {
        $this->_copixcache_defaultTypeName = $pCache;
    }
	
      /**
     * Récupération de la liste des profiles enregistrés
     * @return array
     */
	public function copixcache_getRegistered (){
		return array_keys ($this->_arCacheDefinition);
	} 
    
    /**
     * Récupération de la liste des profiles enregistrés
     * @return array
     */
	public function copixcache_getRegisteredProfiles (){
		return $this->_arCacheDefinition;
	} 

    /* ========================================= Chemins ou trouver modules ou plugins */
	/**
	 * Chemin ou l'on doit doit aller chercher les modules. 
	 */
	var $arModulesPath = array ();

	/**
	 * Chemin ou l'on doit aller chercher les plugins
	 */
	var $arPluginsPath = array ();
    
	/* ========================================= paramètres généraux */

	/**
    * indique si le système d'autorisation des modules est activé
    * @var boolean
    */
	var $checkTrustedModules = false;

	/**
    * liste des modules authorisés
    * 'nom_du_module'=>true/false
    * @var array
    */
	var $trustedModules = array();

	/**
    * Le nom de la session pour permettre à plusieurs instances 
    *  de Copix de cohabiter sur le même espace
    * @var string
    */
	var $sessionName = 'Copix';

	/* ========================================= APC on/OFF */

	/**
    * Indique si l'on souhaite profiter des fonctionnalités d'APC
    * @var boolean
    */
	var $apcEnabled = false;

	/* =========================================  CopixDB */

	/**
	 * Nom du profil de connexion par défaut 
	 */
	var $_copixdb_defaultProfileName = null;
	
	/**
	 * Tableau des profils de connexion connus
	 */
	var $_copixdb_profiles = array ();

    /**
     * Ajout un profil de connexion à CopixDB
     * 
     * @param string $pName le nom du profil que l'on souhaites ajouter 
     * @param object $pConnectionString le profil de connexion que l'on souhaites utiliser
     * @param string $pUser le login
     * @param string $pPassword le mot de passe
     * @param array $pOptions un tableau d'options en fonction du driver utilisé.
     */
    public function copixdb_defineProfile ($pName, $pConnectionString, $pUser, $pPassword, $pOptions = array ()){
    	$this->_copixdb_profiles[$pName] = new CopixDBProfile ($pName, $pConnectionString, $pUser, $pPassword, $pOptions); 
   	/*	if (count ($this->_copixdb_profiles) == 1){
    		$this->copixdb_defineDefaultProfileName ($pName);
   	}*/
    }
    
    /**
     * Récupère la liste des profils définis dans CopixDB
     * @param array of string la liste des profiles de connexion définis
     */
    public function copixdb_getProfiles (){
    	return array_keys ($this->_copixdb_profiles);
    }
    
    /**
     * Défini le nom du profil de connexion par défaut
     * @param string $pName le nom du profil de connexion que l'on souhaite mettre par défaut
     */
    public function copixdb_defineDefaultProfileName ($pName){
    	$this->_copixdb_defaultProfileName = $pName;
    }
    
    /**
     * Récupère le nom du profil de connexion par défaut
     */
    public function copixdb_getDefaultProfileName (){
    	return $this->_copixdb_defaultProfileName;
    }
    
    /**
     * Retourne le profil de connexion de nom $pName
     */
    public function copixdb_getProfile ($pName = null){
    	if ($pName === null){
			$pName = $this->copixdb_getDefaultProfileName ();
    		if ($pName === null) throw new CopixDBException  ('Pas de profil par défaut');
    	}
    	if (isset ($this->_copixdb_profiles[$pName])){
    		return $this->_copixdb_profiles[$pName];
    	}
   		throw new CopixDBException ('Le profil de connexion '.$pName." n'existe pas");
    }
    
    /* =========================================  plugins */
	/**
	 * les plugins enregistrés
	 * @var array
	 */
    private $_plugin_registered = array ();
	
	/**
	 * Enregistrement d'un plugin
	 * @param 	string	$pPluginName	le nom du plugin à enregistrer
	 */
    public function plugin_register ($pPluginName){
    	if (!in_array ($pPluginName, $this->_plugin_registered)){
    		$this->_plugin_registered[] = $pPluginName;
    	}
	}
	
	/**
	 * Récupération des plugins enregistrés
	 * @return array
	 */
	public function plugin_getRegistered (){
		return $this->_plugin_registered; 
	}

	/* =========================================  internationalisation */

	/**
    * code langage par defaut
    * @var string
    */
	var $default_language = 'fr';

	/**
    * code pays par defaut
    * @var string
    */
	var $default_country  = 'FR';
	
	/**
	 * Charset à utiliser par défaut
	 * @var string
	 */
	var $default_charset = 'ISO-8859-1';
	
	/**
	 * Indique si l'on souhaites que les paramètres de langue soient pris en compte
	 * lors du calcul des chemins des templates (système de thèmes) et des ressources (en complément
	 * du système de thème).
	 * @see CopixTpl
	 * @var boolean
	 */
	 var $i18n_path_enabled = false;

	/**
    * Indique si l'on souhaite générer un trigger error en cas de clef i18n absente.
    * si null, ne générera pas d'erreur
    * @var boolean
    */
	var $missingKeyTriggerErrorLevel = E_USER_ERROR;

	/* ========================================= Compilateur des fichiers XML et autres */

	/**
    * indique si les compilateurs doivent checker le cache
    * pour savoir si il faut mettre à jour ou pas le cache
    * @var boolean
    */
	var $compile_check  = true;

	/**
    * indique si il faut toujours recompiler
    * @var boolean
    */
	var $force_compile  = false;

	/* =========================================  paramètrages du moteur de template */
	
	/**
    * indique si il faut mettre en cache le resultat du template
    * @var int
    */
	var $template_caching        = 0;

	/**
    * Doit on utiliser des sous répertoires pour la compilation des templates.
    * (Smarty uniquement)
    */
	var $template_use_sub_dirs   = false;

	/**
    * nom du fichier template principal
    * @var string
    */
	var $mainTemplate = 'main.ptpl';

	/**
    * Si une action invalide lance une erreur ou non
    * @deprecated 
    * @see notFoundDefaultRedirectTo
    */
	var $invalidActionTriggersError = false;
	
	/**
	 * Indique vers quel url (copix) on redirige l'utilisateur s'il demande une action non 
	 * prise en charge par le contrôller. 
	 * @var string
	 */
	var $notFoundDefaultRedirectTo = false;
	
	/**
	 * Si la configuration de PHP authorise la surcharge de unserialize_callback_func
	 */
	var $overrideUnserializeCallbackEnabled = true;

	/* ========================================= Paramètrage du système de paramètres dynamiques */
	private $_configModule = array ();

	/* =========================================  support des urls significatifs */
	var $significant_url_mode = 'default'; // "default" ou "prepend"
	var $significant_url_prependIIS_path_key = '__COPIX_SIGNIFICANT_URL__';
	var $stripslashes_prependIIS_path_key    = true;
	var $url_requestedscript_variable = 'SCRIPT_NAME';//(en général SCRIPT_NAME, PHP_SELF, REDIRECT_SCRIPT_URL)
	
	/* =========================================== Divers */
    /**
    * Indique si la fonction realpath est active (false) ou non (true) sur le serveur
    */
	var $realPathDisabled = false;

    /**
     * Avant le demarrage de la session.
     */
    public function afterSessionStart (){
    }

	/**
	 * Singleton
	 */
	private static $_instance = false;

	/**
	 * Constructeur privé pour le singleton
	 */
	private function __construct (){
/*
		//construction des rapprochements par défaut des erreurs.
		$this->errorHandlerActions = array (E_ERROR=>new CopixErrorHandlerAction (true, CopixLog::ERROR), 
											E_WARNING=>new CopixErrorHandlerAction (false, CopixLog::WARNING), 
											E_PARSE=>new CopixErrorHandlerAction (true, CopixLog::FATAL_ERROR), 
											E_NOTICE=>new CopixErrorHandlerAction (false, CopixLog::NOTICE),
											E_CORE_ERROR=>new CopixErrorHandlerAction (true, CopixLog::FATAL_ERROR), 
											E_CORE_WARNING=>new CopixErrorHandlerAction (false, CopixLog::WARNING), 
											E_COMPILE_ERROR=>new CopixErrorHandlerAction (true, CopixLog::FATAL_ERROR),
											E_COMPILE_WARNING=>new CopixErrorHandlerAction (false, CopixLog::WARNING),
											E_USER_ERROR=>new CopixErrorHandlerAction (true, CopixLog::ERROR), 
											E_USER_WARNING=>new CopixErrorHandlerAction (false, CopixLog::WARNING),
											E_USER_NOTICE=>new CopixErrorHandlerAction (false, CopixLog::NOTICE),
											E_STRICT=>new CopixErrorHandlerAction (false, CopixLog::NOTICE), 
											E_RECOVERABLE_ERROR=>new CopixErrorHandlerAction (false, CopixLog::WARNING));
		$this->errorHandlerDefaultAction = new CopixErrorHandlerAction (true, CopixLog::ERROR);
		//set_error_handler (array ('CopixErrorHandler', 'handle'));
*/		
	
		//Configuration des plugins
		if (file_exists (COPIX_VAR_PATH.'config/plugins.conf.php')) {
			require (COPIX_VAR_PATH.'config/plugins.conf.php');
			if (isset ($_plugins)){
				foreach ($_plugins as $pluginName){
					$this->plugin_register ($pluginName);
				}
			}
		}	
		
		//Configuration des profils de log
		if (file_exists (COPIX_VAR_PATH.'config/log_profiles.conf.php')) {
			require (COPIX_VAR_PATH.'config/log_profiles.conf.php');
			if (isset ($_log_profiles)){
				foreach ($_log_profiles as $profile){
					$this->copixlog_registerProfile ($profile);
				}
			}
		}
				
		//Configuration des profils de cache
		if (file_exists(COPIX_VAR_PATH.'config/cache_profiles.conf.php')) {
			require (COPIX_VAR_PATH.'config/cache_profiles.conf.php');
			if (isset ($_cache_types)){
				foreach ($_cache_types as $type){
					$this->copixcache_registerType ($type);
				}
			}
		}

		//Configuration des profils de base de données
		if (file_exists (COPIX_VAR_PATH.'config/db_profiles.conf.php')) {
			require (COPIX_VAR_PATH.'config/db_profiles.conf.php');
			foreach ($_db_profiles as $profileName=>$profileInformations){
				$this->copixdb_defineProfile ($profileName, $profileInformations['driver'].':'.$profileInformations['connectionString'], $profileInformations['user'], $profileInformations['password'], $profileInformations['extra']);
				if ($profileInformations['default']) {
					$this->copixdb_defineDefaultProfileName ($profileName);
				}
			}
			
			if (isset ($_db_default_profile)) {
				$this->copixdb_defineDefaultProfileName ($_db_default_profile);
			}
		}				
	}

	/**
    * Singleton.
    */
	public static function instance (){
		if (CopixConfig::$_instance === false){
			CopixConfig::$_instance = new CopixConfig ();
		}
		return CopixConfig::$_instance;
	}

	/**
    * Retourne la valeur du paramètre $id
    * @param string $id [module]|name
    * @return string
    */
	public static function get ($id) {
		if (($pos = strpos ($id, '|')) === false){
			$module = CopixContext::get ().'|';
			$id    = $module.$id;
		}else{
			$module = substr ($id, 0, $pos).'|';
		}
		if ($module == "|"){
			$module = "default|";
			$id = "default".$id;
		}
		return CopixConfig::instance ()->_getModuleConfig ($module)->get ($id);
	}

	/**
    * Regarde si la paramètre donné existe.
    * @param string $id l'identifiant du paramètre
    */
	public static function exists ($id){
		if (($pos = strpos ($id, '|')) === false){
			$module = CopixContext::get ().'|';
			$id    = $module.$id;
		}else{
			$module = substr ($id, 0, $pos).'|';
		}
		if ($module == "|"){
			$module = "default|";
			$id = "default".$id;
		}
		return CopixConfig::instance ()->_getModuleConfig ($module)->exists ($id); 
	}

	/**
    * gets all parameters
    * @param module - string [module]
    * @return array
    */
	public static function getParams ($moduleName) {
		//Is the module name valid ? or is it the project we wants to get the parameters of ?
		if (! (CopixModule::isEnabled ($moduleName))){
			return array ();
		}
		return CopixConfig::instance ()->_getModuleConfig ($moduleName.'|')->getParams (); 
	}

	/**
    * sets the value of a parameter
    */
	public static function set ($id, $value){
		if (($pos = strpos ($id, '|')) === false){
			$module = CopixContext::get ().'|';
			$id    = $module.$id;
		}else{
			$module = substr ($id, 0, $pos).'|';
		}
		if ($module == "|"){
			$module = "default|";
			$id = "default".$id;
		}
  		return CopixConfig::instance ()->_getModuleConfig ($module)->set ($id, $value); 
	}

	/**
    * gets a CopixModuleConfig. Handle single instance to avoid multiple loadings.
    * @param $kind - the kind of module we wants to load (moduleName, copix:, plugin:name, ..., ...)
    * @return CopixConfigModule
    */
	private function _getModuleConfig ($kind){
		if (isset ($this->_configModule[$kind])){
			return $this->_configModule[$kind];
		}
		$this->_configModule[$kind] = new CopixModuleConfig ($kind);
		return $this->_configModule[$kind];
	}

	/**
    * Gets the real path of a given path
    */
	public static function getRealPath($path){
		$config = CopixConfig::instance ();
		if ($config->realPathDisabled === false){
			return realpath ($path);
		}else{
			$result = array();
			$pathA = preg_split('/[\/\\\]/', $path);
			//$pathA = explode('/', $path);
			if (! $pathA[0]){
			   $result[] = '';
			}
			foreach ($pathA AS $key => $dir) {
				if ($dir == '..') {
					if (end($result) == '..') {
						$result[] = '..';
					} elseif (!array_pop($result)) {
						$result[] = '..';
					}
				} elseif ($dir && $dir != '.') {
					$result[] = $dir;
				}
			}
			if (!end($pathA)){
			   $result[] = '';
			}
    		return implode(CopixConfig::osIsWindows() ? '\\' : '/', $result);
		}
	}

	/**
    * Gets the operating system of the server
    * @return string name of the operating System
    */
	public static function getOSName (){
		static $osString = false;
		if ($osString === false){
			$osString = substr (PHP_OS, 0, (($pos = strpos (PHP_OS, ' ')) === false) ? strlen (PHP_OS) : $pos);
		}
		return $osString;
	}

	/**
    * Says if the OS is windows or not
    * @return boolean
    */
	public static function osIsWindows (){
		static $checked   = false;
		static $isWindows = false;
		if (!$checked){
			$isWindows = (strtoupper (substr(CopixConfig::getOsName (), 0, 3)) === 'WIN');
			$checked = true;
		}
		return $isWindows;
	}
	
	/**
	 * Définition du mode d'utilisation du framework
	 * @param 	int	le mode de configuration à définir (DEVEL, PRODUCTION, FORCE_INITIALISATION)
	 */
	public function setMode ($pMode){
		switch ($pMode){
			case self::DEVEL :
				break;
			case self::PRODUCTION:
				break;
			case self::FORCE_INITIALISATION:
				break;
			default:
				throw new CopixException (CopixI18N::get ('copix:errors.unknownMode'));
				break;
		}
		self::$_mode = $pMode;
	}

	/**
	 * Récupération du mode de fonctionnement demandé
	 * @return int le mode de fonctionnement actuellement configuré
	 */
	public function getMode (){
		return self::$_mode;
	}
	
	/**
	 * Mode de fonctionnement de l'application, par défaut DEVEL.
	 * @var int
	 */
	private static $_mode = self::DEVEL;
}
?>