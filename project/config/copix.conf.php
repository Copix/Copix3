<?php
/** 
* @package copix
* @author Croës Gérald
* @copyright CopixTeam
* @link http://copix.org
* @license http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
*/

//Objet de configuration
$config = CopixConfig::instance ();

// ---------------------------------------------
// Gestion des erreurs 
//     & configuration de l'environnement
// ---------------------------------------------
error_reporting (E_ALL | E_STRICT);
$config->copixerrorhandler_enabled = false;
$config->setMode (CopixConfig::DEVEL);//valeurs possibles DEVEL, PRODUCTION, FORCE_INITIALISATION

// ---------------------------------------------
// Configuration du framework Mootools
// ---------------------------------------------
$config->mootools_compatibility_version = false;//Dans Copix 3.1 on n'utilise plus le script de compatibilité 1.1 de mootools

// ---------------------------------------------
// Mode de gestion des URL
// ---------------------------------------------
$config->significant_url_mode = 'prepend'; // "default" (index.php?module=x&desc=y&action=z...) ou "prepend" (index.php/module/desc/action/)

// ---------------------------------------------
// I18N
// ---------------------------------------------
$config->i18n_availables = array ('fr', 'en');
$config->i18n_missingKeyLaunchException = false;

// Redirection (a décommenter si le module est installé)
// $config->notFoundDefaultRedirectTo = ('404||');

// ---------------------------------------------
// Configuration des répertoires des thèmes
// ---------------------------------------------
$config->copixtheme_addPath (COPIX_PROJECT_PATH . 'themes' . DIRECTORY_SEPARATOR);

// ---------------------------------------------
// Configuration des répertoires de module
// ---------------------------------------------
$config->arModulesPath = array (
	COPIX_PROJECT_PATH . 'modules/devel/bench/',
	COPIX_PROJECT_PATH . 'modules/devel/cms/',
	COPIX_PROJECT_PATH . 'modules/devel/cms3/',
	COPIX_PROJECT_PATH . 'modules/devel/devtools/',
	COPIX_PROJECT_PATH . 'modules/devel/moocms/',
	COPIX_PROJECT_PATH . 'modules/devel/standard/',
	COPIX_PROJECT_PATH . 'modules/devel/tools/',
	COPIX_PROJECT_PATH . 'modules/devel/tutorials/',
	COPIX_PROJECT_PATH . 'modules/devel/webtools/',
	COPIX_PROJECT_PATH . 'modules/stable/devtools/',
	COPIX_PROJECT_PATH . 'modules/stable/standard/',
	COPIX_PROJECT_PATH . 'modules/stable/tests/',
	COPIX_PROJECT_PATH . 'modules/stable/tools/',
	COPIX_PROJECT_PATH . 'modules/stable/tutorials/',
	COPIX_PROJECT_PATH . 'modules/stable/webtools/', 
	COPIX_PROJECT_PATH . 'modules/contribs/'
);
