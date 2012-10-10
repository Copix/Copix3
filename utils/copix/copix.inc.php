<?php
/**
* @package   copix
* @author   Croes Gérald, Jouanneau Laurent
* @copyright CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

spl_autoload_register (array('Copix', 'autoload'));

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
   function __construct ($pMsg){
   	   parent::__construct ($pMsg);
   	   _log ($pMsg.'-'.$this->getTraceAsString (), 'exception', CopixLog::EXCEPTION);
   }   
}

/**
 * Classe utilitaire qui permet l'inclusion des fichiers et bibliothéques Copix
 * @package copix
 * @subpackage	core
 */
class Copix {
	/**
	 * Bibliothéques déjà inclues
	 * @var	array
	 */
	static private $_libraries = array ();
	
	/**
	 * Fichiers déjà inclus
	 * @var	array 
	 */
	static private $_included = array ();
	
	/**
	 *  Chargement automatique des classes
	 */
	public static function autoload ($pClassName) {
		$strClassName = strtolower($pClassName);
		if ((strpos ($strClassName, "copix") === 0) || (strpos ($strClassName, "icopix") === 0)) {
			$result = self::RequireClass ($pClassName);
			return $result;
		} else {
			return false;
		}
	}

	/**
	 * Inclusion unique d'un fichier
	 * @param	string	$pPath le chemin du fichier que l'on souhaites inclure.
	 * @return	boolean	le fichier est ou non connu 
	 */
	public static function RequireOnce ($pPath){
		if (! isset (self::$_included[strtolower($pPath)])){
            return self::$_included[strtolower($pPath)] = include ($pPath);
		}
        return self::$_included[strtolower($pPath)];
	}
	
	/**
	 * Inclusion de librairies Copix
	 * @param	string	$pClassName le nom de la classe que l'on souhaites inclure
	 * @return	boolean	le fichier est ou non connu
	 * @todo Zone, Services, HTMLHeader, Cache, ClassesFactory, I18N, EventNotifier, Db, DBQueryParam, DAOFactory, Auth, User, Log
	 */
	public static function RequireClass ($pClassName){
		$pClassName = strtolower ($pClassName);

		if (isset (self::$_libraries[$pClassName])){
			return true;
		}
		
	 	$utilsClasses = array (
	 	    'copixzone'=>'CopixZone', 
	 		'copixfileselector'=>'CopixFileSelector',
	 		'copixpluginregistry'=>'CopixPluginRegistry',
	 		'copixtpl'=>'CopixTpl',
	 		'copixmodule'=>'CopixModule',
	 		'copixmoduleconfig'=>'CopixModuleConfig',
	 		'copixservices'=>'CopixServices',
	 		'copixhtmlheader'=>'CopixHTMLHeader',
	 		'copixfile'=>'CopixFile',
	 		'copixcsv'=>'CopixCsv',
	 		'copixtimer'=>'CopixTimer',
	 		'copixemailer'=>'CopixEMailer',
	 		'copixtextemail'=>'CopixEMailer',
	 		'copixhtmlemail'=>'CopixEMailer',
	 		'copixdatetime'=>'CopixDateTime',
	 		'copixformatter'=>'CopixFormatter',
	 		'copixhttpheader'=>'CopixHTTPHeader',
	 		'copixfilter'=>'CopixFilter',
	 		'copixmimetypes'=>'CopixMIMETypes',
	 		'copixphpgenerator'=>'CopixPHPGenerator',
	 		'copixmodulefileselector'=>'CopixFileSelector',
	 		'copixi18nbundle'=>'CopixI18NBundle',
	 		'copixuploadedfile'=>'CopixUploadedFile',
			'copixselectorfactory'=>'CopixFileSelector',
			'copixurlhandler'=>'CopixUrl',
	 		'copixerrorobject'=>'CopixErrorObject',
	 		'copixclassesfactory'=>'CopixClassesFactory',
			'copixpluginregistry'=>'CopixPluginRegistry',
	 		'copixi18n'=>'CopixI18N',
			'copixsession'=>'CopixSession', 
			'copixsessionobject'=>'CopixSession',
			'copixclassproxy'=>'CopixClassProxy', 
			'copixcontextproxy'=>'CopixContextProxy',
	 		'copixxmlserializer'=>'CopixXMLSerializer');

		if (isset ($utilsClasses[$pClassName])){
 			self::$_libraries[$pClassName] = true;
 			return Copix::RequireOnce (COPIX_UTILS_PATH.$utilsClasses[$pClassName].'.class.php');
 		}
			
		$classes = array (
	 		'copixlistenerfactory'=>COPIX_PATH.'events/CopixListenerFactory.class.php',
	 		'copixformfactory'=>COPIX_PATH . 'forms/CopixFormFactory.class.php',
	 		'copixuser'=>COPIX_PATH.'auth/CopixUser.class.php',
	 		'copixusercredentials'=>COPIX_PATH.'auth/CopixCurrentUserCredentials.class.php',
	 		'copixcache'=>COPIX_PATH . 'cache/CopixCache.class.php',
	 		'copixeventnotifier'=>COPIX_PATH . 'events/CopixEventNotifier.class.php',
	 		'copixevent'=>COPIX_PATH . 'events/CopixEventNotifier.class.php',
	 		'copixdb'=>COPIX_PATH  . 'db/CopixDb.class.php',
	 		'copixdbprofile'=>COPIX_PATH  . 'db/CopixDb.class.php',
	 		'copixdbqueryparam'=>COPIX_PATH  . 'db/CopixDbQueryParam.class.php',
	 		'copixdbexception'=>COPIX_PATH  . 'db/CopixDb.class.php',
	 		'copixdaofactory'=>COPIX_PATH . 'dao/CopixDAOFactory.class.php',
	 		'copixauth'=>COPIX_PATH . 'auth/CopixAuth.class.php',
	 		'copixauthexception'=>COPIX_PATH . 'auth/CopixAuth.class.php',
	 		'copixuser'=>COPIX_PATH.'auth/CopixUser.class.php',
	 		'copixuserexception'=>COPIX_PATH.'auth/CopixUser.class.php',
	 		'copixlog'=>COPIX_PATH . 'log/CopixLog.class.php',
			'copixdbconnection'=>COPIX_PATH  . 'db/CopixDbConnection.class.php',
			'copixdbpdoconnection'=>COPIX_PATH  . 'db/CopixDbPDOConnection.class.php',
			'copixform'=>COPIX_PATH.'forms/CopixForm.class.php',
			'copixcredentialhandlerfactory'=>COPIX_PATH.'auth/CopixCredentialHandlerFactory.class.php',
			'copixgrouphandlerfactory'=>COPIX_PATH.'auth/CopixGroupHandlerFactory.class.php',
			'icopixgrouphandler'=>COPIX_PATH.'auth/CopixGroupHandlerFactory.class.php',
			'copixuserhandlerfactory'=>COPIX_PATH.'auth/CopixUserHandlerFactory.class.php',
			'copixcredentialexception'=>COPIX_PATH.'auth/CopixCredentialHandlerFactory.class.php',
			'icopixcredentialhandler'=>COPIX_PATH.'auth/CopixCredentialHandlerFactory.class.php',
			'copixdaosearchparams'=>COPIX_PATH.'dao/CopixDAOSearchParams.class.php',
			'copixdaodefinition'=>COPIX_PATH.'dao/CopixDAODefinition.class.php',
			'copixpropertyfordao'=>COPIX_PATH.'dao/CopixDAODefinition.class.php',
			'copixlist'=>COPIX_PATH.'lists/CopixList.class.php',
			'copixfield'=>COPIX_PATH.'forms/CopixForm.class.php',
			'copixlistfactory'=>COPIX_PATH.'lists/CopixListFactory.class.php',
			'copixdatasourcefactory'=>COPIX_PATH.'datasource/CopixDatasourceFactory.class.php',
			'icopixdatasource'=>COPIX_PATH.'datasource/CopixDatasourceFactory.class.php',
			'copixdatasourceexception'=>COPIX_PATH.'datasource/CopixDatasourceFactory.class.php',
	 		'copixuserhandler'=>COPIX_PATH.'auth/CopixUserHandler.class.php',
	 		'copixdaodatasource'=>COPIX_PATH.'datasource/CopixDaoDatasource.class.php',
	 		'copixldapprofil'=>COPIX_PATH.'ldap/CopixLdapProfil.class.php',
		    'copixdbpdoresultsetiterator'=>COPIX_PATH.'db/CopixDbPDOResultSetIterator.class.php',
  			'icopixmoduleinstaller'=>COPIX_PATH.'utils/CopixModuleInstaller.class.php'
			);
			

 		if (isset ($classes[$pClassName])){
 			self::$_libraries[$pClassName] = true;
 			Copix::RequireOnce ($classes[$pClassName]);
 		} else {
 			unset(self::$_libraries[$pClassName]);
 			return false;
 		}
	}
}

//Définition de constantes.
define ('COPIX_VERSION_MAJOR', 3);
define ('COPIX_VERSION_MINOR', 0);
define ('COPIX_VERSION_FIX', 1);

define ('COPIX_VERSION_RC', null);
define ('COPIX_VERSION_BETA', null);
define ('COPIX_VERSION_CVS', false);


$copixVersion = COPIX_VERSION_MAJOR . '.' . COPIX_VERSION_MINOR . '.' . COPIX_VERSION_FIX;
if (!is_null (COPIX_VERSION_RC)) {
	$copixVersion .= ' RC ' . COPIX_VERSION_RC;
}
if (!is_null (COPIX_VERSION_BETA)) {
	$copixVersion .= ' BETA ' . COPIX_VERSION_BETA;
}
if (!is_null (COPIX_VERSION_CVS)) {
	$copixVersion .= ' CVS ';
}
define ('COPIX_VERSION', $copixVersion);

define ('COPIX_PATH', dirname (__FILE__).'/');
define ('COPIX_CORE_PATH', COPIX_PATH.'core/');
define ('COPIX_UTILS_PATH', COPIX_PATH.'utils/');

define ('COPIX_ACTIONGROUP_DIR'    , 'actiongroups/');
define ('COPIX_DESC_DIR'     , 'desc/');
define ('COPIX_ZONES_DIR'    , 'zones/');
define ('COPIX_TEMPLATES_DIR', 'templates/');
define ('COPIX_STATIC_DIR'   , 'static/');
define ('COPIX_CLASSES_DIR'  , 'classes/');
define ('COPIX_RESOURCES_DIR', 'resources/');
define ('COPIX_PLUGINS_DIR'    , 'plugins/');
define ('COPIX_INSTALL_DIR', 'install/');
define ('COPIX_SMARTY_PATH', COPIX_PATH.'../smarty/');

Copix::RequireOnce (COPIX_CORE_PATH . 'shortcuts.lib.php');
//Copix::RequireOnce (COPIX_CORE_PATH . 'CopixErrorHandler.class.php');
Copix::RequireOnce (COPIX_CORE_PATH . 'CopixRequest.class.php');
Copix::RequireOnce (COPIX_CORE_PATH . 'CopixConfig.class.php');
Copix::RequireOnce (COPIX_CORE_PATH . 'CopixAction.class.php');
Copix::RequireOnce (COPIX_CORE_PATH . 'CopixController.class.php');
Copix::RequireOnce (COPIX_CORE_PATH . 'CopixActionGroup.class.php');
Copix::RequireOnce (COPIX_CORE_PATH . 'CopixUrl.class.php');
Copix::RequireOnce (COPIX_CORE_PATH . 'CopixContext.class.php');
?>