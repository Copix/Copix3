<?php
/**
* @package		copix
* @author		Croès Gérald
* @copyright	CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
*/

/**
 * @ignore
 */
$config = CopixConfig::instance ();

//Paramètrage pré-configuré
$config->setMode (CopixConfig::DEVEL);//valeurs possibles DEVEL, PRODUCTION, FORCE_INITIALISATION

// Gestionnaire d'erreurs désativé par défaut.
$config->copixerrorhandler_enabled = false;

//Divers
$config->significant_url_mode = 'prepend'; // "default" (index.php?module=x&desc=y&action=z...) ou "prepend" (index.php/module/desc/action/)

//I18N
$config->default_language = 'fr';
$config->default_country  = 'FR';
$config->default_charset = 'UTF-8';

//Template principal
$config->mainTemplate   = 'default|main.php';
//---------------------------------------------
//Configuration des répertoires de module
//---------------------------------------------
$config->arModulesPath = array (
	COPIX_PROJECT_PATH.'modules/public/stable/standard/', 
	COPIX_PROJECT_PATH.'modules/public/stable/webtools/',
	COPIX_PROJECT_PATH.'modules/public/stable/tools/',
	COPIX_PROJECT_PATH.'modules/public/stable/tutorials/',
	COPIX_PROJECT_PATH.'modules/public/devel/bench/',
	COPIX_PROJECT_PATH.'modules/public/devel/cms/',
	COPIX_PROJECT_PATH.'modules/public/devel/devtools/',
	COPIX_PROJECT_PATH.'modules/public/devel/moocms/',
	COPIX_PROJECT_PATH.'modules/public/devel/standard/',
	COPIX_PROJECT_PATH.'modules/public/devel/tools/',
	COPIX_PROJECT_PATH.'modules/public/devel/tutorials/',
	COPIX_PROJECT_PATH.'modules/public/devel/webtools/',
	COPIX_VAR_PATH.'modules/'
);

//---------------------------------------------
//Configuration des gestionnaires de droit
//---------------------------------------------
//Code temporairement placé ici devrait se trouver dans CopixConfig

//On enregistre ce handler de droit en dure car sinon on ne l'as pas dans la liste quand le framework n'est pas installÃ©
$config->copixauth_registerCredentialHandler (array ('name'=>'admin|installcredentialhandler',
										'stopOnSuccess'=>true,
										'stopOnFailure'=>false,
										'handle'=>'all'
										));

// Configuration des userhandler
$handlers = CopixModule::getParsedModuleInformation ('copix|userhandlers','/moduledefinition/userhandlers/userhandler', array ('CopixAuthParserHandler', 'parseUserHandler'));
if (file_exists (COPIX_VAR_PATH . 'config/user_handlers.conf.php')) {
	require (COPIX_VAR_PATH . 'config/user_handlers.conf.php');
	if (isset ($_user_handlers)) {
		foreach ($_user_handlers as $handler) {
			if (isset ($handlers[$handler])) {
				$config->copixauth_registerUserHandler ($handlers[$handler]);
			}
		}
	}
}

// Configuration des grouphandler
$handlers = CopixModule::getParsedModuleInformation ('copix|grouphandlers','/moduledefinition/grouphandlers/grouphandler', array ('CopixAuthParserHandler', 'parseGroupHandler'));
if (file_exists (COPIX_VAR_PATH . 'config/group_handlers.conf.php')) {
	require (COPIX_VAR_PATH . 'config/group_handlers.conf.php');
	if (isset ($_group_handlers)) {
		foreach ($_group_handlers as $handler) {
			if (isset ($handlers[$handler])) {
				$config->copixauth_registerGroupHandler ($handlers[$handler]);
			}
		}
	}
}

// Configuration des credentialhandler
$handlers = CopixModule::getParsedModuleInformation ('copix|credentialhandlers','/moduledefinition/credentialhandlers/credentialhandler', array ('CopixAuthParserHandler', 'parseCredentialHandler'));
if (file_exists (COPIX_VAR_PATH . 'config/credential_handlers.conf.php')) {
	require (COPIX_VAR_PATH . 'config/credential_handlers.conf.php');
	if (isset ($_credential_handlers)) {
		foreach ($_credential_handlers as $handler) {
			if (isset ($handlers[$handler])) {
				$config->copixauth_registerCredentialHandler ($handlers[$handler]);
			}
		}
	}
}
?>