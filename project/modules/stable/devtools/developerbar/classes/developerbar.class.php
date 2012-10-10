<?php
/**
 * Diverses méthodes pour la barre des développeurs
 */
class DeveloperBar {
	/**
	 * Identifiant unique par page
	 * 
	 * @var string
	 */
	private static $_id = null;

	/**
	 * Début de calcul pour le temps global, en milliseconde
	 *
	 * @var int
	 */
	private static $_globalTimer = null;
	
	/**
	 * Temps global, en millisecondes
	 *
	 * @var int
	 */
	private static $_globalTime = null;
	
	/**
	 * Début de calcul pour le temps de l'action, en milliseconde
	 *
	 * @var int
	 */
	private static $_actionTimer = null;
	
	/**
	 * Temps de l'action, en millisecondes
	 *
	 * @var int
	 */
	private static $_actionTime = null;
	
	/**
	 * Temps de Copix (global - action), en millisecondes
	 *
	 * @var int
	 */
	private static $_copixTime = null;
	
	/**
	 * Tous les logs, sauf le type query
	 *
	 * @var array
	 */
	private static $_logs = array ();
	
	/**
	 * Tous les logs de type query
	 *
	 * @var array
	 */
	private static $_querys = array ();

	/**
	 * Tous les logs de type errors
	 *
	 * @var array
	 */
	private static $_errors = array ();

	/**
	 * Retourne l'identifiant de la bar pou rla page courante
	 *
	 * @return string
	 */
	public static function getId () {
		return ((self::$_id == null) ? self::$_id = 'devbar_' . time () . '|' . uniqid () : self::$_id);
	}
	
	/**
	 * Démarre le timer pour calculer le temps d'execution global
	 */
	public static function startGlobalTimer () {
		self::$_globalTimer = microtime (true);
	}
	
	/**
	 * Arrête le timer pour calculer le temps d'execution global
	 */
	public static function endGlobalTimer () {
		self::$_globalTime = microtime (true) - self::$_globalTimer;
		self::$_copixTime = self::getGlobalTime (false) - self::getActionTime (false);
	}
	
	/**
	 * Démarre le timer pour calculer le temps d'execution de l'action
	 */
	public static function startActionTimer () {
		self::$_actionTimer = microtime (true);
	}
	
	/**
	 * Arrête le timer pour calculer le temps d'execution de l'action
	 */
	public static function endActionTimer () {
		self::$_actionTime = microtime (true) - self::$_actionTimer;
	}
	
	/**
	 * Retourne le temps d'execution de l'action, en millisecondes
	 *
	 * @param boolean $pRound Arrondit le retour à 3 chiffres après la virgule, ou renvoi le nombre sans arrondir
	 * @return int
	 */
	public static function getActionTime ($pRound = true) {
		return ($pRound) ? number_format (self::$_actionTime, 3, ',', ' ') : self::$_actionTime;
	}
	
	/**
	 * Retourne le temps d'execution global, en millisecondes
	 *
	 * @param boolean $pRound Arrondit le retour à 3 chiffres après la virgule, ou renvoi le nombre sans arrondir
	 * @return int
	 */
	public static function getGlobalTime ($pRound = true) {
		return ($pRound) ? number_format (self::$_globalTime, 3, ',', ' ') : self::$_globalTime;
	}
	
	/**
	 * Retourne le temps d'execution de Copix, en millisecondes
	 *
	 * @param boolean $pRound Arrondit le retour à 3 chiffres après la virgule, ou renvoi le nombre sans arrondir
	 * @return int
	 */
	public static function getCopixTime ($pRound = true) {
		return ($pRound) ? number_format (self::$_copixTime, 3, ',', ' ') : self::$_copixTime;
	}
	
	/**
	 * Retourne les requêtes
	 *
	 * @param boolean $pFormat Si null, prend la config developerbar|querysFormat
	 * @return array
	 */
	public static function getQuerys ($pFormat = null) {
		if ($pFormat === false || ($pFormat === null && !CopixUserPreferences::get ('developerbar|querysFormat'))) {
			return self::$_querys;
		} else {
			return self::formatQuerys (self::$_querys);
		}
	}

	/**
	 * Retourne les requêtes formattées
	 *
	 * @param array $pQuerys Requêtes, peut être un retour de self::getQuerys
	 * @return array
	 */
	public static function formatQuerys ($pQuerys) {
		foreach ($pQuerys as &$query) {
			// mots clefs avec la classe developerBarQueryKeyWord
			$keyWords = array ('set character set', 'select ', 'update ', 'insert ', ' and ', ' or ', ' in ', ' not in ', ' date_format', ' asc', ' desc');
			$keyWordsWithBR = array (' from ', ' where ', ' left join ', ' right join ', ' order by ');
			foreach ($keyWords as $keyWord) {
				$query['message'] = str_ireplace ($keyWord, '<span class="developerBarQueryKeyWord">' . strtoupper ($keyWord) . '</span>', $query['message']);
			}
			foreach ($keyWordsWithBR as $keyWord) {
				$query['message'] = str_ireplace ($keyWord, '<br /><span class="developerBarQueryKeyWord">' . strtoupper ($keyWord) . '</span>', $query['message']);
			}

			// remplacement des binds par leur valeur
			if (array_key_exists ('binds', $query['extras']) && is_array ($query['extras']['binds'])) {
				foreach ($query['extras']['binds'] as $name => $value) {
					if (is_string ($value)) {
						$value = "'" . $value . "'";
					}
					$query['message'] = str_replace ($name, $value, $query['message']);
				}
			}

			// chaine de caractère avec la classe developerBarQueryString
			$query['message'] = preg_replace ('/\'([^\']*)\'/', '\'<span class="developerBarQueryString">$1</span>\'', $query['message']);
		}
		return $pQuerys;
	}
	
	/**
	 * Commence à loger tous les messages via la stratégie DeveloperBarLog
	 */
	public static function startLog () {
		$params = array (
			'name' => 'developerbar' . uniqid (),
			'enabled' => true,
			'handle' => 'all',
			'strategy' => 'developerbar|developerbarlog',
			'level' => '1',
		);
		CopixConfig::instance ()->copixLog_registerProfile ($params);
	}
	
	/**
	 * Envoi le log à la DeveloperBar
	 *
	 * @param string $pProfile Nom du profil
	 * @param string $pType Type de log
	 * @param int $pLevel Niveau de log, utiliser les constantes de CopixLog
	 * @param string $pDate Date et heure du log, format YmdHis
	 * @param string $pMessage Message à loger
	 * @param array $pExtras Informations supplémentaires
	 */
	public static function addLog ($pProfile, $pType, $pLevel, $pDate, $pMessage, $pExtras) {
		$log = array ();
		$log['message'] = $pMessage;
		$log['profile'] = $pProfile;
		$log['type'] = $pType;
		$log['level'] = $pLevel;
		$log['date'] = $pDate;
		$log['extras'] = $pExtras;
		if ($pType == 'query') {
			self::$_querys[] = $log;
		} else if ($pType == 'errors') {
			self::$_errors[] = $log;
		} else {
			self::$_logs[] = $log;
		}
	}
	
	/**
	 * Retourne tous les logs, sauf ceux de type query
	 *
	 * @param boolean $pFormat Indique si on veut formatermessage et extras avec dump
	 * @return array
	 */
	public static function getLogs ($pFormat = false) {
		return self::_getLog (self::$_logs, $pFormat);
	}

	/**
	 * Retourne les logs de type errors
	 *
	 * @param boolean $pFormat Indique si on veut formatermessage et extras avec dump
	 * @return array
	 */
	public static function getErrors ($pFormat = false) {
		return self::_getLog (self::$_errors, $pFormat);
	}

	/**
	 * Retourne les logs avec des dump de message et extras ou non
	 *
	 * @param array $pLogs Logs à formater
	 * @param boolean $pFormat Indique si on veut formater les paramètres message et extras avec dump
	 */
	private static function _getLog ($pLogs, $pFormat) {
		if (!$pFormat) {
			return $pLogs;
		}
		foreach ($pLogs as &$log) {
			$log['message'] = self::dump ($log['message'], true);
			foreach ($log['extras'] as &$value) {
				$value = self::dump ($value, true);
			}
		}
		return $pLogs;
	}

	/**
	 * Définit quel contenu sera affiché par défaut
	 *
	 * @param string $pContent Nom du bloc
	 */
	public static function setShow ($pContent) {
		CopixSession::set ('show', $pContent, 'developerbar');
	}

	/**
	 * Retourne le contenu à afficher par défaut
	 *
	 * @return string
	 */
	public static function getShow () {
		return CopixSession::get ('show', 'developerbar');
	}

	/**
	 * Effectue un dump de la valeur donnée
	 *
	 * @param mixed $pValue Valeur à dumper
	 * @param <type> $pReturn Indique si on veut retourner le dump, ou faire un affichage direct
	 */
	public static function dump ($pValue, $pReturn = false) {
		$function = CopixUserPreferences::get ('developerbar|dumpFunction');
		if ($pReturn) {
			if ($function == '_dump') {
				return CopixDebug::getDump ($pValue);
			} else if ($function == 'var_dump') {
				ob_start ();
				echo '<pre style="margin: 0px; padding: 0px;">';
				var_dump ($pValue);
				echo '</pre>';
				return ob_get_clean ();
			} else if ($function == 'var_export' || $function == 'print_r') {
				return '<pre style="margin: 0px; padding: 0px;">' . $function ($pValue, true) . '</pre>';
			}
		} else {
			if ($function == '_dump') {
				echo CopixDebug::getDump ($pValue);
			} else {
				echo '<pre style="margin: 0px; padding: 0px;">';
				$function ($pValue);
				echo '</pre>';
			}
		}
	}
	
	/**
	 * Retourne l'HTML à afficher pour la barre
	 *
	 * @return string
	 */
	public static function getHTML ($pIsMain = true) {
		$params = array ();
		$cache = array ();
		$params['isMain'] = $pIsMain;

		// logs
		self::_setParams ('logs', $params, self::getLogs (true), $cache);

		// erreurs
		self::_setParams ('errors', $params, self::getErrors (true), $cache);

		// requêtes non formatées, pour ne pas prendre du temps CPU inutile si on n'affiche jamais les requêtes
		self::_setParams ('querys', $params, self::getQuerys (false), $cache);

		// variables serveur
		$params['vars'] = array ();
		$params['vars_ajax'] = CopixUserPreferences::get ('developerbar|varsAjax');
		self::_setParams ('get', $params['vars'], $_GET, $cache, 'developerbar|varsAjax');
		self::_setParams ('post', $params['vars'], $_POST, $cache, 'developerbar|varsAjax');
		self::_setParams ('cookie', $params['vars'], $_COOKIE, $cache, 'developerbar|varsAjax');
		self::_setParams ('server', $params['vars'], $_SERVER, $cache, 'developerbar|varsAjax');

		// session
		$params['vars']['session'] = array ();
		if (!$params['vars_ajax']) {
			$array = &$params['vars']['session'];
		} else {
			$array = &$cache['$_developerbar_session'];
		}
		foreach (CopixSession::getNamespaces () as $namespace) {
			$array[$namespace] = array ();
			foreach (CopixSession::getVariables ($namespace) as $name => $value) {
				$array[$namespace][$name] = self::dump ($value, true);
			}
		}

		// timers
		$params['timers']['global'] = self::getGlobalTime ();
		$params['timers']['copix'] = self::getCopixTime ();
		$params['timers']['action'] = self::getActionTime ();

		// mémoire
		$params['memory']['limit'] = str_replace ('M', ' Mo', ini_get ('memory_limit'));
		// avant PHP 5.2.1, il fallait compiler PHP avec l'option --enable-memory-limit pour avoir accès à ces fonctions
		if (function_exists ('memory_get_usage')) {
			$params['memory']['script'] = number_format (floor (memory_get_peak_usage (true) / 1024), 0, null, ' ');
			$params['memory']['php'] = number_format (floor (memory_get_usage (true) / 1024), 0, null, ' ');
		} else {
			$params['memory']['script'] = '-';
			$params['memory']['php'] = '-';
		}

		// contenu affiché par défaut
		$params['show'] = self::getShow ();

		// génération du cache si besoin
		if (count ($cache) > 0) {
			$php = new CopixPHPGenerator ();
			$content = null;
			foreach ($cache as $var => $value) {
				if ($var == '$_developerbar_session') {
					foreach ($value as $namespace => $vars) {
						foreach ($vars as $varName => $varValue) {
							$content .= $php->getVariableDeclaration ($var . '[\'' . $namespace . '\'][\'' . $varName . '\']', $varValue) . $php->getEndLine ();
						}
					}
				} else {
					$content .= $php->getVariableDeclaration ($var, $value) . $php->getEndLine ();
				}
			}
			CopixFile::write (self::getCacheFilePath (self::getId ()), $php->getPHPTags ($content));
		}
		
		return CopixZone::process ('developerbar|developerbar', $params);
	}

	/**
	 * Met les informations globales dans $pParams, ou en cache dans $pCache si o nveut un chargement ajax
	 *
	 * @param string $pType Type d'informations (logs, querys, etc)
	 * @param array $pParams Paramètres à passer au template pour getHTML
	 * @param mixed $pValues Valeurs
	 * @param array $pCache Cache à écrire pour les chargements ajax
	 * @param string $pConfig Configuration du module à aller chercher pour l'appel ajax
	 */
	private static function _setParams ($pType, &$pParams, $pValues, &$pCache, $pConfig = null, $pCacheVarName = null) {
		$pParams[$pType . '_count'] = count ($pValues);
		$config = ($pConfig == null) ? 'developerbar|' . $pType . 'Ajax' : $pConfig;
		$pParams[$pType . '_ajax'] = CopixUserPreferences::get ($config);
		if (!$pParams[$pType . '_ajax']) {
			$pParams[$pType] = $pValues;
		} else {
			$varName = ($pCacheVarName == null) ? '$_developerbar_' . $pType : $pCacheVarName;
			$pCache[$varName] = $pValues;
		}
	}

	/**
	 * Retourne le chemin vers le fichier de cache $pId
	 *
	 * @param string $pId Identifiant du fichier
	 * @return string
	 */
	public static function getCacheFilePath ($pId) {
		list ($time, $name) = explode ('|', $pId);

		// suppression des anciens fichiers de cache (1h de cache)
		foreach (CopixFile::findFiles (COPIX_TEMP_PATH . 'developerbar/') as $file) {
			if (is_dir ($file) && intval (CopixFile::extractFileName ($file)) < ($time - 3600)) {
				CopixFile::removeDir ($file);
			}
		}
		
		return COPIX_TEMP_PATH . 'developerbar/' . $time . '/' . $name . '.php';
	}

	/**
	 * Retourne la valeur de la variable demandée dans le fichier de cache
	 *
	 * @param string $pId Identifiant du cache
	 * @param string $pVar Nom de la variable
	 */
	public static function getCacheVar ($pId, $pVar) {
		$path = self::getCacheFilePath ($pId);
		if (!file_exists ($path)) {
			throw new DeveloperBarException ('Le fichier de cache "' . $path . '" n\'existe pas.');
		}
		require ($path);
		if (!isset (${$pVar})) {
			throw new DeveloperBarException ('La variable de cache "' . $pVar . '" n\'existe pas.');
		}
		return ${$pVar};
	}
}