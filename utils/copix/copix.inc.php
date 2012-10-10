<?php
/**
 * @package copix
 * @author Croes Gérald, Jouanneau Laurent
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Classe de base pour toutes les exceptions qui seront levées par Copix et les services Copix
 *
 * @package	copix
 * @subpackage core
 */
class CopixException extends Exception {
    /**
     * Informations supplémentaires
     *
     * @var array
     */
    private $_extras = array ();

    /**
     * Constructeur
     *
     * @param string $pMsg Texte du message d'erreur
     * @param int $pCode Code du message
     * @param array $pExtras Informations supplémentaires
     */
    public function __construct ($pMsg, $pCode = 0, $pExtras = array ()) {
        parent::__construct ($pMsg, $pCode);
        $extras = array ('Context' => CopixContext::get ());
        $this->_extras = array_merge ($extras, $pExtras);
        uksort ($pExtras, 'strnatcasecmp');
    }

    /**
     * Retourne les informations supplémentaires
     *
     * @return array
     */
    public function getExtras () {
        return $this->_extras;
    }

    /**
     * Retourne une information particulière, ou null si elle n'existe pas
     *
     * @param string $pName Nom de l'information
     * @return mixed
     */
    public function getExtra ($pName, $pDefaultValue = null) {
        return (array_key_exists ($pName, $this->_extras)) ? $this->_extras[$pName] : $pDefaultValue;
    }
}

/**
 * Interface d'un gestionnaire d'erreur
 *
 * @package copix
 * @subpackage core
 */
interface ICopixErrorHandler {
    /**
     * Indique au gestionnaire d'erreur qu'il peut gérer les E_STRICT
     */
    public function processStricts ();

    /**
     * Reçoit une erreur
     *
     * @param integer $pErrNo Code d'erreur
     * @param string $pErrMsg Message d'erreur
     * @param string $pFilename Nom du fichier ayant provoqué l'erreur
     * @param integer $pLinenum Ligne du fichier ayant provoquée l'erreur
     * @param array $pVars Variables locales
     */
    public function handle ($pErrNo, $pErrMsg, $pFilename, $pLinenum, $pVars);
}

/**
 * Classe utilitaire qui permet l'inclusion des fichiers et bibliothéques Copix
 *
 * @package copix
 * @subpackage core
 */
class Copix {
    /**
     * Fichiers déjà inclus
     *
     * @var	array
     */
    private static $_included = array ();

    /**
     * Gestionnaire d'erreur en cours
     *
     * @var ICopixErrorHandler
     */
    private static $_errorHandler = null;

    /**
     * Met en place un nouveau gestionnaire d'erreur.
     *
     * @param ICopixErrorHandler $pErrorHandler Nouveau gestionnaire d'erreur
     */
    public static function setErrorHandler (ICopixErrorHandler $pErrorHandler) {
        self::$_errorHandler = $pErrorHandler;
        set_error_handler (array ($pErrorHandler, 'handle'));
    }

    /**
     * Inclusion unique d'un fichier, et indique si il a été inclu ou non
     *
     * @param string $pPath Chemin du fichier que l'on souhaites inclure
     * @return boolean
     */
    public static function RequireOnce ($pPath) {
        $path = strtolower ($pPath);
        if (!isset (self::$_included[$path])) {
            require ($pPath);
            self::$_included[$path] = true;
            if (self::$_errorHandler !== null) {
                self::$_errorHandler->processStricts ();
            }
        }
        return self::$_included[$path];
    }

    /**
     * Inclusion de librairies Copix
     *
     * @param string $pClassName le nom de la classe que l'on souhaites inclure
     * @return boolean
     * @see CopixAutoloader
     * @todo Zone, Services, HTMLHeader, Cache, ClassesFactory, I18N, EventNotifier, Db, DBQueryParam, DAOFactory, Auth, User, Log
     */
    public static function RequireClass ($pClassName) {
        spl_autoload_call ($pClassName);
        if (!class_exists ($pClassName, false)) {
            throw new Exception ("Class $pClassName not found");
        }
    }

    /**
     * Indique si Copix "semble" installé.
     *
     * @return boolean
     */
    public static function installed () {
        static $result = array ();
        if (array_key_exists ('installed', $result)) {
            return $result['installed'];
        }

        if (CopixConfig::instance ()->getMode() === CopixConfig::PRODUCTION) {
            return $result['installed'] = true;
        }

        try {
            CopixDB::getConnection ();
            return $result['installed'] = CopixModule::isEnabled ('admin');
        }catch (CopixException $e) {
            return $result['installed'] = false;
        }
    }
}

//Définition de constantes.
define ('COPIX_VERSION_MAJOR', 3);
define ('COPIX_VERSION_MINOR', 1);
define ('COPIX_VERSION_FIX', 0);

define ('COPIX_VERSION_RC', null);
define ('COPIX_VERSION_BETA', null);
define ('COPIX_VERSION_DEV', true);

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

Copix::RequireOnce (COPIX_PATH . 'core/shortcuts.lib.php');
Copix::RequireOnce (COPIX_PATH . 'autoload/CopixAutoloader.class.php');
Copix::RequireOnce (COPIX_PATH . 'autoload/CopixDAOAutoloader.class.php');
Copix::RequireOnce (COPIX_PATH . 'autoload/CopixModuleClassAutoloader.class.php');