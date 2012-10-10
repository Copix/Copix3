<?php
/**
 * @package copix
 * @subpackage log
 * @author    Landry Benguigui
 * @copyright 2001-2008 CopixTeam
 * @link      http://copix.org
 * @license	  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Interface de base pour la gestion des logs
 * @package copix
 * @subpackage log
 */
interface ICopixLogStrategy {
	public function log    ($pProfil, $pType, $pLevel, $pDate, $pMessage, $pArExtra);
	public function getLog ($pProfil);
	public function deleteProfile ($pProfil);
}

/**
 * Gestion des exceptions de type log
 *
 * @package		copix
 * @subpackage	log
 */
class CopixLogException extends CopixException { }

/**
 * @package copix
 * @subpackage log 
 */
class CopixLog{
	
	/**
	 * On conserve les éléments autorisés
	 * @var array
	 */
	private static $_enabled = array ();
	
	/**
	 * Liste des strategies déja instanciées
	 * @var array
	 */
	private static $_strategy = array ();
	
	/**
	 * Cache des profils activés.
	 *
	 * @var array "type" => niveau mini
	 */
	private static $_typeLevels = array();
	
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
	 * Element qui indique qu'un log est déja en cours (et empêche de lancer un nouveau log entrainant des
	 *  appels récursifs infinis.
	 * @var boolean
	 */
	private static $_lock = false;
	
	/**
	 * Appelle la fonction log de la stratégie qui convient
	 *
	 * @param String $chaine 
	 * @param String $level
	 */
	public static function log ($chaine, $pType = "default", $pLevel = self::INFORMATION, $arExtra = array ()) {
		if (! self::$_lock){
			self::$_lock = true;
			try {
				$profils = array();
				foreach (self::getProfiles ($pType) as $profil) {
					if ($profil['level'] <= $pLevel && self::_enabled ($profil['name'])) {
						$profils[] = $profil;
					}
				}
				if (count ($profils) > 0) {
				    self::_fillExtra ($arExtra);
					foreach ($profils as $profil){
						try {
			    			self::_getStrategy ($profil['strategy'])->log ($profil['name'], $pType, $pLevel, date ('YmdHis'), $chaine, $arExtra);
						} catch(Exception $e) {
							// Perd le log ET ignore l'exception
						}
				    }			    	
			    }
			}catch (Exception $e){
				self::$_lock = false;
				throw $e;
			}
			self::$_lock = false;
		}
	}
	
	/**
	 * Teste si le type de log est activé de donnera lieu à un log.
	 * 
	 * Permet d'éviter de faire des calculs compliqués pour un log qui ne sera pas enregistré. Exemple:
	 * 
	 * <code>
	 * if(CopixLog::isEnabled('monTypeDeLog', CopixLog::NOTICE)) {
	 *   $msgLog = calculComplique($param1, $param2);
	 *   CopixLog::log($msgLog, 'monTypeDeLog', CopixLog::NOTICE);
	 * }
	 * </code>
	 *
	 * @param string $pType Type de log.
	 * @param integer $pLeveL Niveau de Log souhaité.
	 * @return boolean Vrai si le type de log est bien activé.
	 */
	public static function isEnabled($pType, $pLevel = self::INFORMATION) {
		if(!isset($_typeLevels[$pType])) {
			$minLevel = self::FATAL_ERROR + 1;
			$profils = self::getProfiles ($pType);
			foreach ($profils as $profil){
				if (self::_enabled ($profil['name']) && $profil['level'] < $minLevel){
					$minLevel = $profil['level'];
				}
		    }
		    $_typeLevels[$pType] = $minLevel;
		}		
	    return $_typeLevels[$pType] <= $pLevel;
	}
	
	/**
	 * Appelle la fonction getLog de la stratégie qui convient
	 * 
	 * @param String $pProfil nom du profil configurer dans copixConfig
	 * @param String $pNbItems nombre d'items affichés
	 * @return Iterator log demandé
	 */
	public static function getLog ($pProfil, $pNbItems = 20){
		$profil = CopixConfig::instance ()->copixlog_getProfile ($pProfil);
		return self::_getStrategy ($profil['strategy'])->getLog ($pProfil, $pNbItems);
	}

	/**
	 * Supression du contenu d'un "fichier de log" 
	 * @param String $pProfil nom du profil à supprimer
	 */
	public static function deleteProfile ($pProfil){
		$profil = CopixConfig::instance ()->copixlog_getProfile ($pProfil);	
		self::_getStrategy ($profil['strategy'])->deleteProfile ($pProfil);		
	}

	/**
	 * Récupération des profils qui gèrent un type d'information donné
	 * @param 	string	$pType	le type d'information dont on souhaites récupérer les gestionnaires
	 * @return array 
	 */
	public static function getProfiles ($pType){
		return CopixConfig::instance()->copixlog_getProfileFromType ($pType);    	
	}
	
	/**
	 * Cette fonction retourne l'instance de la stratégie adéquat
	 *
	 * @param String $pProfil nom du profil configurer dans copixConfig
	 * @param String $pLevel niveau du log demandé
	 * @return stratégie qui convient
	 */
	private static function _getStrategy ($pClasse){
		$pClasse = strtolower ($pClasse);
		if ($pClasse == '') {
			throw new Exception ('Pas de stratégie définie');
		}
		if (isset (self::$_strategy[$pClasse])){
			return self::$_strategy[$pClasse];
		}
		switch ($pClasse){
			case 'file':
				Copix::RequireOnce (COPIX_PATH.'log/CopixLogFileStrategy.class.php');
				return self::$_strategy[$pClasse] = new CopixLogFileStrategy ();
				
			case 'db':
				Copix::RequireOnce (COPIX_PATH.'log/CopixLogDbStrategy.class.php');
				return self::$_strategy[$pClasse] = new CopixLogDbStrategy ();
				
			case 'system':
				Copix::RequireOnce (COPIX_PATH.'log/CopixLogSystemStrategy.class.php');
				return self::$_strategy[$pClasse] = new CopixLogSystemStrategy ();
				
			case 'session':
				Copix::RequireOnce (COPIX_PATH.'log/CopixLogSessionStrategy.class.php');
				return self::$_strategy[$pClasse] = new CopixLogSessionStrategy ();
				
			case 'firebug':
				Copix::RequireOnce (COPIX_PATH.'log/CopixLogFirebugStrategy.class.php');
				return self::$_strategy[$pClasse] = new CopixLogFireBugStrategy ();
				
			case 'page':
				Copix::RequireOnce (COPIX_PATH.'log/CopixLogPageStrategy.class.php');
				return self::$_strategy[$pClasse] = new CopixLogPageStrategy ();
				
			case 'email':
				Copix::RequireOnce (COPIX_PATH.'log/CopixLogEmailStrategy.class.php');
				return self::$_strategy[$pClasse] = new CopixLogEmailStrategy ();
								
			default:
				return self::$_strategy[$pClasse] = _ioClass ($pClasse);
		}
	}
	
	/**
	 * cette fonction determine si le type/level demandé est valide
	 *
	 * @param String $pType nom du profil configurer dans copixConfig
	 * @return boolean correct ou non
	 */
	private static function _enabled ($pProfil = 'default') {
		if(isset(self::$_enabled[$pProfil])){
    		return self::$_enabled[$pProfil];
    	}		
		$config = CopixConfig::instance ();
		//On regarde si le type est pris en charge
		self::$_enabled[$pProfil] = false;
		
		if (($typeInformations = $config->copixlog_getProfile ($pProfil)) !== null){			
			if ($typeInformations['enabled']) {
				self::$_enabled[$pProfil] = true;
			}
		}		
    	return self::$_enabled[$pProfil];
    }
    
    /**
     * Saisie des informations supplémentaires si besoin
     * @param	array	$pArExtra	tableau des informations de log actuel
     * @return void 
     */
    private static function _fillExtra (& $pArExtra){   
    	$arTrace = CopixDebug::debug_backtrace(2, array(__FILE__), true);
    	$trace = reset($arTrace);
    	while($trace && ((isset($trace['class']) && in_array($trace['class'], array('CopixLog', 'CopixErrorHandler'))) || ($trace['function'] == '_log'))) {
    		$trace = next($arTrace);
    	}
    	$info = array ();
    	$info['file'] = !empty($trace['file']) ? $trace['file'] : '';
    	$info['line'] = !empty($trace['line']) ? $trace['line'] : '';
    	//$trace = next($arTrace);
        $info['classname'] = isset ($trace['class']) ? $trace['class'] : '';
        $info['functionname'] = isset ($trace['function']) ? $trace['function'] : '';
        $info['request_uri'] = (isset ($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : '';
    	$pArExtra = array_merge ($info, $pArExtra);

	   	//Détermine l'utilisateur si pas donné
    	if (!isset ($pArExtra['user'])){
    		$pArExtra['user'] = CopixAuth::getCurrentUser ()->getLogin ();
    	}
    }
    
    /**
     * Retourne la liste des stratégies disponibles pour la sauvegarde des logs
     * @todo trouver un système pour que cette méthode retourne également les classes du FWK
     *  qui héritent de ICopixLogStrategy
     * @return array of object
     */
    public static function getStrategies (){
    	$file = new StdClass ();
    	$file->id = 'file';
    	$file->caption = _i18n ('copix:log.CopixLogFileStrategy');
    	
    	$db = new StdClass ();
    	$db->id = 'db';
    	$db->caption = _i18n ('copix:log.CopixLogDBStrategy');

    	$system = new StdClass ();
    	$system->id = 'system';
    	$system->caption = _i18n ('copix:log.CopixLogSystemStrategy');

    	$session = new StdClass ();
    	$session->id = 'session';
    	$session->caption = _i18n ('copix:log.CopixLogSessionStrategy');
    	
    	$firebug = new StdClass ();
    	$firebug->id = 'firebug';
    	$firebug->caption = _i18n ('copix:log.CopixLogFireBugStrategy');
    	 
    	$page = new StdClass ();
    	$page->id = 'page';
    	$page->caption = _i18n ('copix:log.CopixLogPageStrategy');

    	$email = new StdClass ();
    	$email->id = 'email';
    	$email->caption = _i18n ('copix:log.CopixLogEmailStrategy');
    	    	
    	return array ($file, $db, $page, $system, $session, $firebug, $email);    	    	
    }
    
    /**
     * Récupère les niveaux de logs définis avec id / libellé
     * @return array
     */
    public static function getLevels (){
    	$toReturn = array ();

    	$level = new StdClass ();
    	$level->id = self::INFORMATION;
    	$level->caption = _i18n ('copix:log.INFORMATION');
    	$toReturn[] = $level;
    	
    	$level = new StdClass ();
    	$level->id = self::NOTICE;
    	$level->caption = _i18n ('copix:log.NOTICE');
    	$toReturn[] = $level;
    	
    	$level = new StdClass ();
    	$level->id = self::WARNING;
    	$level->caption = _i18n ('copix:log.WARNING');
    	$toReturn[] = $level;
    	
    	$level = new StdClass ();
    	$level->id = self::EXCEPTION;
    	$level->caption = _i18n ('copix:log.EXCEPTION');
    	$toReturn[] = $level;
    	 
    	$level = new StdClass ();
    	$level->id = self::ERROR;
    	$level->caption = _i18n ('copix:log.ERROR');
    	$toReturn[] = $level;
    	
    	$level = new StdClass ();
    	$level->id = self::FATAL_ERROR;
    	$level->caption = _i18n ('copix:log.FATAL_ERROR');
    	$toReturn[] = $level;

    	return $toReturn;
    }
    
    /**
     * Retourne le niveau en string, en prenant en compte la langue
     *
     * @param int $pLevel Constante de CopixLog
     * @return string
     */
    static public function getLevel ($pLevel) {
    	$levels = self::getLevels ();
    	foreach ($levels as $levelInfos) {
    		if ($levelInfos->id == $pLevel) {
    			return $levelInfos->caption;
    		}
    	}
    }
}
?>