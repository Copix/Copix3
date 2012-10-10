<?php
/**
 * @package copix
 * @author Croës Gérald, Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

$thisPath = dirname (__FILE__) . '/';

// ----------------------------------------------------
// Chemins vers les répertoires utilisés par Copix
// ----------------------------------------------------

// répertoire du projet
define ('COPIX_PROJECT_PATH', $thisPath . '..'.DIRECTORY_SEPARATOR);
// répertoire temporaire
define ('COPIX_TEMP_PATH', COPIX_PROJECT_PATH . '..'.DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR);
// répertoire contenant les caches des DAO, templates smarty, resources i18n, etc
// peut être un sous répertoire de COPIX_TEMP_PATH 
define ('COPIX_CACHE_PATH', COPIX_TEMP_PATH . 'cache'.DIRECTORY_SEPARATOR);
// répertoire où seront stockés les log de type Fichier
define ('COPIX_LOG_PATH', COPIX_TEMP_PATH . 'log'.DIRECTORY_SEPARATOR);
// répertoire des fichiers écrits par les modules et le framework, mais qui ne sont pas temporaires
define ('COPIX_VAR_PATH', COPIX_PROJECT_PATH . '..'.DIRECTORY_SEPARATOR.'var'.DIRECTORY_SEPARATOR);
// répertoire contenant les classes utilitaires de Copix
define ('COPIX_PATH', $thisPath . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'utils' . DIRECTORY_SEPARATOR . 'copix' . DIRECTORY_SEPARATOR);
// répertoire contenant les classes utilitaires de Copix
define ('COPIX_UTILS_PATH', COPIX_PATH . 'utils'.DIRECTORY_SEPARATOR);
// répertoire contenant Smarty
define ('COPIX_SMARTY_PATH', COPIX_PATH . '..'.DIRECTORY_SEPARATOR.'smarty'.DIRECTORY_SEPARATOR);
// répertoire contenant le compresseur Javascript
define ('COPIX_JSXS_PATH', COPIX_PATH . '..'.DIRECTORY_SEPARATOR.'jsxs'.DIRECTORY_SEPARATOR);

// ----------------------------------------------------
// Chemins vers certains fichiers de Copix et du projet
// ----------------------------------------------------

// fichier contenant la configuration de CopixConfig à utiliser
define ('COPIX_CONFIG_FILE', $thisPath . 'copix.conf.php');
// fichiercontenant la classe Copix
define ('COPIX_INC_FILE', COPIX_PATH . 'copix.inc.php');
// fichier contenant les chemins vers toutes les classes de Copix
define ('COPIX_CLASSPATHS_FILE', COPIX_PATH . 'CopixClassPaths.inc.php');

// ----------------------------------------------------
// Nom des répertoires principaux
// ----------------------------------------------------

define ('COPIX_TEMPLATES_DIR', 'templates'.DIRECTORY_SEPARATOR);
define ('COPIX_CLASSES_DIR', 'classes'.DIRECTORY_SEPARATOR);
define ('COPIX_RESOURCES_DIR', 'resources'.DIRECTORY_SEPARATOR);
define ('COPIX_PLUGINS_DIR', 'plugins'.DIRECTORY_SEPARATOR);
define ('COPIX_INSTALL_DIR', 'install'.DIRECTORY_SEPARATOR);
define ('COPIX_WWW_DIR', 'www'.DIRECTORY_SEPARATOR);
define ('COPIX_SMARTYPLUGIN_DIR', 'smarty_plugins'.DIRECTORY_SEPARATOR);