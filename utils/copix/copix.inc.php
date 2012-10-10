<?php
/**
* @package   copix
* @author   Croes Gérald, Jouanneau Laurent
* @copyright CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Classe de base pour toutes les exceptions qui seront levées par Copix et les services Copix
 * @package	copix
 * @subpackage	core
 */
class CopixException extends Exception {
   /**
    * Utilise le système de log pour tracer les exceptions
    * @param 	string	$pMsg	le message d'erreur qui viens avec l'exception
    */
   function __construct ($pMsg, $pCode = 0){
   	   parent::__construct ($pMsg, $pCode);
   }   
}

/**
 * Interface d'un gestionnaire d'erreur.
 *
 */
interface ICopixErrorHandler {
	
	/**
	 * Indique au gestionnaire d'erreur qu'il peut gérer les E_STRICT.
	 */
	public function processStricts();
	
	/**
	 * Reçoit une erreur.
	 *
	 * @param integer $pErrNo Code d'erreur.
	 * @param string $pErrMsg Message d'erreur.
	 * @param string $pFilename Nom du fichier ayant provoqué l'erreur.
	 * @param integer $pLinenum Ligne du fichier ayant provoquée l'erreur.
	 * @param array $pVars Variables locales.
	 */
	public function handle($pErrNo, $pErrMsg, $pFilename, $pLinenum, $pVars);
	
}

/**
 * Classe utilitaire qui permet l'inclusion des fichiers et bibliothéques Copix
 * @package copix
 * @subpackage	core
 */
class Copix {

	/**
	 * Fichiers déjà inclus
	 * @var	array 
	 */
	static private $_included = array ();

	/**
	 * Gestionnaire d'erreur en cours.
	 *
	 * @var ICopixErrorHandler
	 */
	static private $_errorHandler = null;
	
	/**
	 * Met en place un nouveau gestionnaire d'erreur.
	 *
	 * @param ICopixErrorHandler $pErrorHandler Nouveau gestionnaire d'erreur.
	 */
	static public function setErrorHandler(ICopixErrorHandler $pErrorHandler) {
		self::$_errorHandler = $pErrorHandler;
		set_error_handler(array($pErrorHandler, 'handle'));
	}
 
	/**
	 * Inclusion unique d'un fichier
	 * @param	string	$pPath le chemin du fichier que l'on souhaites inclure.
	 * @return	boolean	le fichier est ou non connu 
	 */
	public static function RequireOnce ($pPath){
		$path = strtolower($pPath);
		if (! isset (self::$_included[$path])){
			if (file_exists ($pPath)) {
				self::$_included[$path] = true;
				self::$_included[$path] = include_once($pPath);
				if(self::$_errorHandler !== null) {
					self::$_errorHandler->processStricts();
				}
			} else {
				self::$_included[$path] = false;
			}
		}
        return self::$_included[$path];
	}
	
	/**
	 * Inclusion de librairies Copix
	 * @param	string	$pClassName le nom de la classe que l'on souhaites inclure
	 * @return	boolean	le fichier est ou non connu
	 * @see CopixAutoloader
	 * @todo Zone, Services, HTMLHeader, Cache, ClassesFactory, I18N, EventNotifier, Db, DBQueryParam, DAOFactory, Auth, User, Log
	 */
	public static function RequireClass ($pClassName){
		// Tente d'abord de déclencher un autoloading 
		if(!class_exists($pClassName, true)) { 
			// Essaie quand même de charger la classe
			// au cas où CopixAutoloader ne soit plus enregistré comme autoloader
			if(!CopixAutoloader::getInstance()->load($pClassName)) {  
				throw new Exception("Class $pClassName not found");
			}
		}
	}
}

//Définition de constantes.
define ('COPIX_VERSION_MAJOR', 3);
define ('COPIX_VERSION_MINOR', 0);
define ('COPIX_VERSION_FIX', 3);

define ('COPIX_VERSION_RC', null);
define ('COPIX_VERSION_BETA', null);
define ('COPIX_VERSION_DEV', null);


$copixVersion = COPIX_VERSION_MAJOR . '.' . COPIX_VERSION_MINOR . '.' . COPIX_VERSION_FIX;
if (!is_null (COPIX_VERSION_RC)) {
	$copixVersion .= ' RC ' . COPIX_VERSION_RC;
}
if (!is_null (COPIX_VERSION_BETA)) {
	$copixVersion .= ' BETA ' . COPIX_VERSION_BETA;
}
if (!is_null (COPIX_VERSION_DEV)) {
	$copixVersion .= ' DEV ';
}
define ('COPIX_VERSION', trim ($copixVersion));

define ('COPIX_PATH', dirname (__FILE__).'/');
define ('COPIX_CORE_PATH', COPIX_PATH.'core/');
define ('COPIX_UTILS_PATH', COPIX_PATH.'utils/');

define ('COPIX_ACTIONGROUP_DIR'    , 'actiongroups/');
define ('COPIX_DESC_DIR'     , 'desc/');
define ('COPIX_ZONES_DIR'    , 'zones/');
define ('COPIX_TEMPLATES_DIR', 'templates/');
define ('COPIX_CLASSES_DIR'  , 'classes/');
define ('COPIX_RESOURCES_DIR', 'resources/');
define ('COPIX_PLUGINS_DIR'    , 'plugins/');
define ('COPIX_INSTALL_DIR', 'install/');
define ('COPIX_SMARTY_PATH', COPIX_PATH.'../smarty/');

/**
 * Chemin du fichier contenant les chemins des classes.
 */
define('COPIX_CLASSPATHS_FILE', COPIX_PATH.'CopixClassPaths.inc.php');

Copix::RequireOnce (COPIX_CORE_PATH . 'CopixAutoloader.class.php');

Copix::RequireOnce (COPIX_CORE_PATH . 'shortcuts.lib.php');
//Copix::RequireOnce (COPIX_CORE_PATH . 'CopixErrorHandler.class.php');
/* N'est plus nécessaire avec l'autoloader 
Copix::RequireOnce (COPIX_CORE_PATH . 'CopixRequest.class.php');
Copix::RequireOnce (COPIX_CORE_PATH . 'CopixConfig.class.php');
Copix::RequireOnce (COPIX_CORE_PATH . 'CopixAction.class.php');
Copix::RequireOnce (COPIX_CORE_PATH . 'CopixController.class.php');
Copix::RequireOnce (COPIX_CORE_PATH . 'CopixActionGroup.class.php');
Copix::RequireOnce (COPIX_CORE_PATH . 'CopixUrl.class.php');
Copix::RequireOnce (COPIX_CORE_PATH . 'CopixContext.class.php');
*/
?>