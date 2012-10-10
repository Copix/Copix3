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

//Divers
$config->significant_url_mode = 'prepend'; // "default" (index.php?module=x&desc=y&action=z...) ou "prepend" (index.php/module/desc/action/)

//I18N
$config->default_language = 'fr';
$config->default_country  = 'FR';
$config->default_charset = 'UTF-8';

//Template principal
$config->mainTemplate   = 'default|main.php';

//Gestionnaire de compilation
$config->force_compile  = false;
$config->compile_check  = true;

//gestion du cache
$config->cacheEnabled = true;
$config->apcEnabled   = false;

$config->cache = array ();

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
$config->copixauth_registerUserHandler (array ('name'=>'auth|dbuserhandler',
										 'required'=>false));
										
$config->copixauth_registerCredentialHandler (array ('name'=>'admin|installcredentialhandler',
										'stopOnSuccess'=>true,
										'stopOnFailure'=>false,
										'handle'=>'all'
										));

$config->copixauth_registerCredentialHandler (array ('name'=>'auth|dbcredentialhandler', 
										'stopOnSuccess'=>true,
										'stopOnFailure'=>false,
										'handle'=>'all'
										));

$config->copixauth_registerCredentialHandler (array ('name'=>'auth|dbmodulecredentialhandler',
                              'stopOnSuccess'=>true,
                              'stopOnFailure'=>false,
                              'handle'=>'module'
                              ));

$config->copixauth_registerGroupHandler ('auth|dbgrouphandler');

?>