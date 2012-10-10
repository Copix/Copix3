<?php
/**
 * @package copix
 * @subpackage log
 * @author Landry Benguigui
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Gestion des logs
 *
 * @package copix
 * @subpackage log
 */
class CopixLog {
	/**
	 * On conserve les éléments autorisés
	 *
	 * @var array
	 */
	private static $_enabled = array ();
	
	/**
	 * Liste des strategies déja instanciées
	 *
	 * @var array
	 */
	private static $_strategy = array ();
	
	/**
	 * Cache des profils activés
	 *
	 * @var array "type" => niveau mini
	 */
	private static $_typeLevels = array ();
	
	/**
	 * Cache des types de log
	 *
	 * @var array
	 */
	private static $_cacheTypes = null;
	
	/**
	 * Cache des stratégies de log disponibles
	 *
	 * @var array
	 */
	private static $_cacheStrategies = null;
	
	/**
	 * Log verbeux (on veut voir énorméement de choses)
	 */
	const VERBOSE = 0;

	/**
	 * Log informatif uniquement
	 */
	const INFORMATION = 1;
	 
	/**
	 * Ce niveau représente les éléments peu important mais qui pouraient améliorer les choses en étant résolu
	 */
	const NOTICE = 2;
	
	/**
	 * Les avertissement qui ne remettent pas en cause le fonctionnement mais restent importants à résoudre
	 */
	const WARNING = 3;
	
	/**
	 * Les exceptions dans Copix sont générées avec ce niveau
	 */
	const EXCEPTION = 4;
	
	/**
	 * Un élément important n'a pas pu être fourni
	 */
	const ERROR = 5;
	
	/**
	 * Le niveau le plus grave
	 */
	const FATAL_ERROR = 6;
	
	/**
	 * Element qui indique qu'un log est déja en cours (et empêche de lancer un nouveau log entrainant des appels récursifs infinis)
	 *
	 * @var boolean
	 */
	private static $_lock = false;

	/**
	 * Cache des niveaux, pour ne pas rappeler des _i18n en masse pour le caption de chaque level
	 *
	 * @var array
	 */
	private static $_levels = false;
	
	/**
	 * Appelle la fonction log de la stratégie qui convient
	 *
	 * @param string $pMessage Message à loger
	 * @param string $pType Type de message
	 * @param int $pLevel Niveau du log, utiliser les constantes de CopixLog
	 * @param array $pExtras Informations supplémentaires
	 */
	public static function log ($pMessage, $pType = 'default', $pLevel = CopixLog::INFORMATION, $pExtras = array ()) {
		if (!self::$_lock) {
			self::$_lock = true;
			try {
				$profils = array ();
				foreach (self::getProfiles ($pType, true) as $profil) {
					$levelOk = (is_array ($profil['level'])) ? in_array ($pLevel, $profil['level']) : $profil['level'] <= $pLevel;
					if ($levelOk) {
						$profils[] = $profil;
					}
				}
				if (count ($profils) > 0) {
					self::_fillExtra ($pExtras);
					$date = date ('YmdHis');
					foreach ($profils as $profil) {
						try {
							$strategy = self::_getStrategy ($profil['strategy']);
							if ($strategy->isWritable ($profil)) {
								$strategy->log ($profil['name'], $pType, $pLevel, $date, $pMessage, $pExtras);
							}
						} catch (Exception $e) {
							// Perd le log ET ignore l'exception
						}
					}
				}
			} catch (Exception $e) {
				self::$_lock = false;
				throw $e;
			}
			self::$_lock = false;
		}
	}
	
	/**
	 * Indique si le type de log est activé de donnera lieu à un log
	 * Permet d'éviter de faire des calculs compliqués pour un log qui ne sera pas enregistré
	 *
	 * <code>
	 * if (CopixLog::isEnabled ('monTypeDeLog', CopixLog::NOTICE)) {
	 *   $msgLog = calculComplique ($param1, $param2);
	 *   CopixLog::log ($msgLog, 'monTypeDeLog', CopixLog::NOTICE);
	 * }
	 * </code>
	 *
	 * @param string $pType Type de log
	 * @param integer $pLeveL Niveau de Log souhaité
	 * @return boolean
	 */
	public static function isEnabled ($pType, $pLevel = CopixLog::INFORMATION) {
		static $_typeLevels = array ();

		if (!isset ($_typeLevels[$pType])) {
			$minLevel = self::FATAL_ERROR + 1;
			$profils = self::getProfiles ($pType);
			foreach ($profils as $profil) {
				if (self::_enabled ($profil['name']) && $profil['level'] < $minLevel) {
					$minLevel = $profil['level'];
				}
			}
			$_typeLevels[$pType] = $minLevel;
		}
		return $_typeLevels[$pType] <= $pLevel;
	}
	
	/**
	 * Conservée pour compatibilité avec Copix 3.0.x
	 *
	 * @deprecated
	 * @see CopixLog::get
	 */
	public static function getLog ($pProfile, $pCount = 20) {
		return self::get ($pProfile, 0, $pCount);
	}
	
	/**
	 * Retourne le contenu du log
	 *
	 * @param string $pProfile Nom du profil
	 * @param int $pStart Index du premier élément à retourner
	 * @param int $pCount Nombre d'éléments à retourner, null pour tous
	 * @return CopixLogData[]
	 */
	public static function get ($pProfile, $pStart = 0, $pCount = null) {
		$profil = CopixConfig::instance ()->copixlog_getProfile ($pProfile);
		$strategy = self::_getStrategy ($profil['strategy']);
		if ($strategy->isReadable ($pProfile)) {
			return $strategy->get ($pProfile, $pStart, $pCount);
		} else {
			throw new CopixLogException (_i18n ('copix:log.error.cantGet'));
		}
	}

	/**
	 * Conservée pour compatibilité avec Copix 3.0.x
	 *
	 * @deprecated
	 * @see CopixLog::delete
	 */
	public static function deleteProfile ($pProfile) {
		self::delete ($pProfile);
	}
	
	/**
	 * Supprime le contenu d'un log
	 *
	 * @param string $pProfile Nom du profil de log dont on veut supprimer le contenu
	 */
	public static function delete ($pProfile) {
		$profil = CopixConfig::instance ()->copixlog_getProfile ($pProfile);
		$strategy = self::_getStrategy ($profil['strategy']);
		if ($strategy->isReadable ($pProfile) && $strategy->isWritable ($pProfile)) {
			$strategy->delete ($pProfile);
		}
	}

	/**
	 * Retourne les profils qui gèrent un type d'information donné
	 *
	 * @param string $pType Type d'information dont on souhaites récupérer les gestionnaires
	 * @param boolean $pOnlyEnabled Indique si on veut seulement les profils actifs
	 * @return array
	 */
	public static function getProfiles ($pType, $pOnlyEnabled = false) {
		$profiles = CopixConfig::instance()->copixlog_getProfileFromType ($pType);
		if ($pOnlyEnabled === false) {
			return $profiles;
		}
		$toReturn = array ();
		foreach ($profiles as $profile) {
			if (self::_enabled ($profile['name'])) {
				$toReturn[] = $profile;
			}
		}
		return $toReturn;
	}
	
	/**
	 * Retourne l'instance de la stratégie adéquate
	 *
	 * @param string $pClasse Classe qui va loger
	 * @return CopixLogStrategy
	 */
	private static function _getStrategy ($pClasse) {
		$pClasse = strtolower ($pClasse);
		if ($pClasse == '') {
			throw new CopixLogException (_i18n ('copix:log.error.undefinedStrategy'));
		}
		if (isset (self::$_strategy[$pClasse])) {
			return self::$_strategy[$pClasse];
		}
		if (in_array ($pClasse, array ('file', 'db', 'system', 'session', 'firebug', 'page', 'email', 'firephp', 'developertool'))) {
			$strategyClassName = 'CopixLog' . $pClasse . 'Strategy';
			return self::$_strategy[$pClasse] = new $strategyClassName ();
		} else {
			return self::$_strategy[$pClasse] = _ioClass ($pClasse);
		}
	}

	/**
	 * Indique si le profil demandé effectuera un log
	 *
	 * @param string $pProfil Nom du profil
	 * @return boolean
	 */
	private static function _enabled ($pProfil = 'default') {
		if (isset (self::$_enabled[$pProfil])) {
			return self::$_enabled[$pProfil];
		}
		$config = CopixConfig::instance ();
		//On regarde si le type est pris en charge
		self::$_enabled[$pProfil] = false;
		
		if (($typeInformations = $config->copixlog_getProfile ($pProfil)) !== null) {
			if ($typeInformations['enabled']) {
				self::$_enabled[$pProfil] = true;
			}
		}
		return self::$_enabled[$pProfil];
	}
	
	/**
	 * Ajoute des informations supplémentaires à $pExtras si elles ne sont pas indiquées
	 *
	 * @param array $pExtras Tableau des informations de log actuel
	 */
	private static function _fillExtra (&$pExtras) {
		$arTrace = CopixDebug::getDebugBacktrace (2, array(__FILE__));
		$trace = reset ($arTrace);
		while ($trace && ((isset ($trace['class']) && in_array ($trace['class'], array ('CopixLog', 'CopixErrorHandler'))) || ($trace['function'] == '_log'))) {
			$trace = next ($arTrace);
		}
		$info = array ();
		$info['id'] = uniqid ();
		$info['file'] = !empty ($trace['file']) ? $trace['file'] : '';
		$info['line'] = !empty ($trace['line']) ? $trace['line'] : '';
		$info['class_name'] = isset ($trace['class']) ? $trace['class'] : '';
		$info['function_name'] = isset ($trace['function']) ? $trace['function'] : '';
		$info['request_uri'] = (isset ($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : '';
		$info['referer'] = (isset ($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : '';
		$info['host'] = (isset ($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : '';
		$info['user_agent'] = (isset ($_SERVER['HTTP_USER_AGENT'])) ? $_SERVER['HTTP_USER_AGENT'] : '';
		$info['page_id'] = CopixPage::getPageId ();
		$pExtras = array_merge ($info, $pExtras);

		//Détermine l'utilisateur si pas donné
		if (!isset ($pExtras['user'])) {
			$users = _currentUser ()->getIdentities ();
			foreach ($users as &$user) {
				$user[] = _currentUser ()->getHandlerResponse ($user[0])->getLogin ();
			}
			$pExtras['user'] = $users;
		}
	}
	
	/**
	 * Retourne les stratégies disponibles pour la sauvegarde des logs
	 *
	 * @return array
	 */
	public static function getStrategies () {
		if (self::$_cacheStrategies === null) {
			$temp =  array ();

			// stratégies de copix
			$temp['db'] = new CopixLogStrategyDescription ('copix:CopixLogDbStrategy', _i18n ('copix:log.CopixLogdbStrategy.caption'), _i18n ('copix:log.CopixLogdbStrategy.description'));
			$temp['file'] = new CopixLogStrategyDescription ('copix:CopixLogFileStrategy', _i18n ('copix:log.CopixLogfileStrategy.caption'), _i18n ('copix:log.CopixLogfileStrategy.description'));
			$temp['email'] = new CopixLogStrategyDescription ('copix:CopixLogEmailStrategy', _i18n ('copix:log.CopixLogemailStrategy.caption'), _i18n ('copix:log.CopixLogemailStrategy.description'));
			$temp['firebug'] = new CopixLogStrategyDescription ('copix:CopixLogFirebugStrategy', _i18n ('copix:log.CopixLogfireBugStrategy.caption'), _i18n ('copix:log.CopixLogfireBugStrategy.description'));
			$temp['page'] = new CopixLogStrategyDescription ('copix:CopixLogPageStrategy', _i18n ('copix:log.CopixLogpageStrategy.caption'), _i18n ('copix:log.CopixLogpageStrategy.description'));
			$temp['session'] = new CopixLogStrategyDescription ('copix:CopixLogSessionStrategy', _i18n ('copix:log.CopixLogsessionStrategy.caption'), _i18n ('copix:log.CopixLogsessionStrategy.description'));
			$temp['system'] = new CopixLogStrategyDescription ('copix:CopixLogSystemStrategy', _i18n ('copix:log.CopixLogsystemStrategy.caption'), _i18n ('copix:log.CopixLogsystemStrategy.description'));
			$temp['firephp'] = new CopixLogStrategyDescription ('copix:CopixLogFirePHPStrategy', _i18n ('copix:log.CopixLogfirePHPStrategy.caption'), _i18n ('copix:log.CopixLogfirePHPStrategy.description'));
			
			// stratégies ajoutées via des modules
			foreach (CopixModule::getList () as $module) {
				$temp = array_merge ($temp, CopixModule::getInformations ($module)->getLogStrategies ());
			}
			
			// tri
			$tri = array ();
			foreach ($temp as $strategy) {
				$tri[$strategy->getCaption ()] = $strategy->getId ();
			}
			ksort ($tri);
			foreach ($tri as $id) {
				self::$_cacheStrategies[$id] = $temp[$id];
			}
		}
		return self::$_cacheStrategies;
	}
	
	/**
	 * Retourne les niveaux de logs
	 *
	 * @return array
	 */
	public static function getLevels () {
		// cache pour ne pas rappeler _i18n inutilement, qui prend du temps au final
		if (self::$_levels === false) {
			self::$_levels = array (
				_ppo (array ('id' => self::VERBOSE, 'caption' => _i18n ('copix:log.VERBOSE'))),
				_ppo (array ('id' => self::INFORMATION, 'caption' => _i18n ('copix:log.INFORMATION'))),
				_ppo (array ('id' => self::NOTICE, 'caption' => _i18n ('copix:log.NOTICE'))),
				_ppo (array ('id' => self::WARNING, 'caption' => _i18n ('copix:log.WARNING'))),
				_ppo (array ('id' => self::EXCEPTION, 'caption' => _i18n ('copix:log.EXCEPTION'))),
				_ppo (array ('id' => self::ERROR, 'caption' => _i18n ('copix:log.ERROR'))),
				_ppo (array ('id' => self::FATAL_ERROR, 'caption' => _i18n ('copix:log.FATAL_ERROR')))
			);
		}
		return self::$_levels;
	}
	
	/**
	 * Retourne le nom du niveau, en prenant en compte la langue
	 *
	 * @param int $pLevel Constante de CopixLog
	 * @return string
	 */
	public static function getLevel ($pLevel) {
		$levels = self::getLevels ();
		foreach ($levels as $levelInfos) {
			if ($levelInfos->id == $pLevel) {
				return $levelInfos->caption;
			}
		}
	}

	/**
	 * Retourne la liste des types de message supportés dans les logs
	 *
	 * @return string[]
	 */
	public static function getTypes () {
		if (self::$_cacheTypes === null) {
			// types définis dans Copix
			$types = array ('default', 'email', 'xml', 'modules', 'plugin', 'query', 'debug', 'copix', 'copixtimer', 'errors');
			foreach ($types as $type) {
				self::$_cacheTypes[$type] = new CopixLogType ('copix:' . $type);
				self::$_cacheTypes[$type]->setCaption (_i18n ('copix:log.type.' . $type));
			}
	
			// types définis dans les modules
			foreach (CopixModule::getList () as $module) {
				self::$_cacheTypes = array_merge (self::$_cacheTypes, CopixModule::getInformations ($module)->getLogTypes ());
			}

			ksort (self::$_cacheTypes);
		}
		
		return self::$_cacheTypes;
	}

	/**
	 * Retourne la stratégie configurée pour le profil $pProfile
	 *
	 * @param string $pProfile Nom du profil
	 * @return CopixLogStrategy
	 */
	private static function _getStrategyFromProfile ($pProfile) {
		$profile = CopixConfig::instance ()->copixlog_getProfile ($pProfile);
		return self::_getStrategy ($profile['strategy']);
	}

	/**
	 * Retourne la taille prise par les logs du profil $pProfile, en octet
	 *
	 * @param string $pProfile Nom du profil
	 * @param boolean $pFormat Indique si on veut formater le retour ou renvoyer la taille en byte
	 * @return int
	 */
	public static function getSize ($pProfile, $pFormat = true) {
		$toReturn = self::_getStrategyFromProfile ($pProfile)->getSize ($pProfile);
		if ($pFormat && $toReturn != null) {
			$toReturn = _filter ('OctetsToText')->get ($toReturn);
		}
		return $toReturn;
	}

	/**
	 * Retourne le nombre de logs effectués via le profil $pProfile
	 *
	 * @param string $pProfile Nom du profil
	 * @return int
	 */
	public static function count ($pProfile) {
		return self::_getStrategyFromProfile ($pProfile)->count ($pProfile);
	}

	/**
	 * Indique si le profil de log $pProfile autorise la lecture des logs
	 *
	 * @param string $pProfile Nom du profil
	 * @return boolean
	 */
	public static function isReadable ($pProfile) {
		return self::_getStrategyFromProfile ($pProfile)->isReadable ($pProfile);
	}

	/**
	 * Indique si le profil de log $pProfile autorise l'écriture des logs
	 *
	 * @param string $pProfile Nom du profil
	 * @return obolean
	 */
	public static function isWritable ($pProfile) {
		return self::_getStrategyFromProfile ($pProfile)->isWritable ($pProfile);
	}
	
	/**
	 * Retourne des informations sur la stratégie de log
	 *
	 * @param string $pName Nom de la stratégie, si elle ne contient pas de | elle sera cherchée dans Copix
	 * @return CopixLogStrategy
	 */
	public static function getStrategyDescription ($pName) {
		$pos = strpos ($pName, '|');
		if ($pos !== false) {
			$description = CopixModule::getInformations (substr ($pName, 0, $pos));
			foreach ($description->getLogStrategies () as $strategy) {
				if (strtolower ($strategy->getId ()) == strtolower ($pName)) {
					return $strategy;
				}
			}
		} else {
			return new CopixLogStrategyDescription ('copix:' . $pName, _i18n ('copix:log.CopixLog' . $pName . 'Strategy.caption'), _i18n ('copix:log.CopixLog' . $pName . 'Strategy.description'));
		}
	}

	/**
	 * Retourne l'HTML pour la configuration des informations spécifiques à la stratégie
	 *
	 * @param array $pProfile Informations sur le profil
	 * @return string
	 */
	public static function getConfigEditor ($pProfile) {
		return self::_getStrategy ($pProfile['strategy'])->getConfigEditor ($pProfile);
	}

	/**
	 * Indique si la configuration de la stratégie est valide
	 *
	 * @param array $pProfile Informations sur le profil
	 * @param array $pConfig Configuration
	 * @return mixed
	 */
	public static function isValidConfig ($pProfile, $pConfig) {
		return self::_getStrategy ($pProfile['strategy'])->isValidConfig ($pProfile, $pConfig);
	}
}