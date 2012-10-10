<?php
session_start ();

// affichage d'une erreur et fin du script
function error ($pMessage) {
	$TITLE = 'Erreur';
	$ETAPE = 0;
	$verificationsErrors = array ('Unknow error');
	$databaseErrors = array ('Unknow error');
	$configErrors = array ('Unknow error');
	require_once ('theme/header.php');
	echo '<h2 class="error">Erreur</h2>';
	echo $pMessage;
	require ('theme/footer.php');
	exit ();
}

// inclusion des fichiers de config
require ('../config/config.php');
if (!is_readable (CONFIG_PATH_FILE)) {
	error ('Le fichier de configuration "' . CONFIG_PATH_FILE . '" ne peut pas être lu.');
}
require (CONFIG_PATH_FILE);
require ('../config/installmodules.php');
foreach ($installModules as $name => $path) {
	if (!is_readable ($path . 'module.xml')) {
		error ('Le module <b>' . $name . '</b> n\'est pas dans le répertoire indiqué (' . $path . ')');
	}
}

// retourne une valeur de la session
function getSession ($pVar, $pDefault = null) {
	return (isset ($_SESSION['install_copix'][$pVar])) ? $_SESSION['install_copix'][$pVar] : $pDefault;
}

// définit une valeur dans la session
function setSession ($pVar, $pValue) {
	$_SESSION['install_copix'][$pVar] = $pValue;
}

// affiche un icone d'aide, avec le texte en alt
function help ($pText) {
	echo '<img src="theme/img/help.png" alt="' . $pText . '" title="' . $pText . '" />';
}

// retourne les erreurs de la section vérifications
function getVerificationsErrors () {
	// création des répertoires, qui peuvent ne pas exister sur une install vierge
	if (!is_dir (COPIX_CACHE_PATH)) {
		@mkdir (COPIX_CACHE_PATH);
	}
	if (!is_dir (COPIX_LOG_PATH)) {
		@mkdir (COPIX_LOG_PATH);
	}

	$tempPath = _realPath (COPIX_VAR_PATH);
	$cachePath = _realPath (COPIX_CACHE_PATH);
	$varPath = _realPath (COPIX_VAR_PATH);
	$logPath = _realPath (COPIX_LOG_PATH);

	// test des droits
	$toReturn = array ();
	test_rights ($tempPath, $toReturn, 'temp_rights');
	test_rights ($cachePath, $toReturn, 'cache_rights');
	test_rights ($varPath, $toReturn, 'var_rights');
	test_rights ($logPath, $toReturn, 'log_rights');
	if (!is_readable (COPIX_UTILS_PATH)) {
		$toReturn['utils_rights'] = 'Le répertoire n\'existe pas ou n\'a pas les droits de lecture.';
	}
	if (!is_readable (COPIX_SMARTY_PATH)) {
		$toReturn['smarty_rights'] = 'Le répertoire n\'existe pas ou n\'a pas les droits de lecture.';
	}
	if (!is_readable (COPIX_INC_FILE)) {
		$toReturn['copixinc_rights'] = 'Le répertoire n\'existe pas ou n\'a pas les droits de lecture.';
	}
	if (!is_readable (COPIX_CLASSPATHS_FILE)) {
		$toReturn['classpaths_rights'] = 'Le fichier n\'existe pas ou n\'a pas les droits de lecture.';
	}

	// version de php
	if (version_compare (PHP_VERSION, '5.1.6', '>=') < 0) {
		$toReturn['version'] = false;
	}

	// présence de simplexml
	if (!function_exists ('simplexml_load_file')) {
		$toReturn['simplexml'] = 'SimpleXML n\'est pas installé.';
	}

	return $toReturn;
}

// retourne les erreurs de la section database
function getDatabaseErrors () {
	$toReturn = array ();
	
	if (getSession ('database_host') == null) {
		$toReturn['host'] = 'Vous devez indiquer l\'adresse ou l\'IP du serveur.';
	}
	if (getSession ('database_name') == null) {
		$toReturn['name'] = 'Vous devez indiquer le nom de la base de données, qui doit déja exister.';
	}
	if (getSession ('database_login') == null) {
		$toReturn['login'] = 'Vous devez indiquer l\'identifiant de l\'utilisateur qui permettra de se connecter au serveur.';
	}

	if (count ($toReturn) == 0) {
		$connexion = db_connect ();
		if (is_string ($connexion)) {
			$toReturn['error'] = $connexion;
		}
	}
	
	return $toReturn;
}

function getConfigErrors () {
	$toReturn = array ();

	require ('../config/installmodules.php');
	if (in_array ('auth', getSession ('config_modules', array_keys ($installModules)))) {
		if (getSession ('config_admin_login') == null) {
			$toReturn['login'] = 'Vous devez indiquer l\'identifiant du compte administrateur.';
		}
		if (getSession ('config_admin_password') == null) {
			$toReturn['password'] = 'Vous devez indiquer le mot de passe du compte administrateur.';
		}
	}

	return $toReturn;
}

// test les droits d'écriture sur un répertoire
function test_rights ($pPath, &$pErrors, $pKey) {
	$id = uniqid ();
	if (!is_dir ($pPath)) {
		$pErrors[$pKey] = 'Répertoire ' . $pPath . ' inexistant.';
		return;
	}

	// création de fichier
	$filePath = $pPath . 'test_rights_' . $id . '.test';
	if (@file_put_contents ($filePath, 'content') === false) {
		$pErrors[$pKey] = 'Ecriture du fichier ' . $filePath . ' impossible.';
		return;
	}

	// suppression de fichier
	if (@unlink ($filePath) == false) {
		$pErrors[$pKey] = 'Suppression du fichier ' . $filePath . ' impossible.';
		return;
	}

	// création de répertoire
	$dirPath = $pPath . 'test_rights_' . $id . '/';
	if (@mkdir ($dirPath, 0755) === false) {
		$pErrors[$pKey] = 'Création du répertoire ' . $dirPath . ' impossible.';
		return;
	}

	// création de fichier dans ce répertoire
	$filePath = $dirPath . 'test_rights_' . $id . '.test';
	if (@file_put_contents ($filePath, 'content') === false) {
		$pErrors[$pKey] = 'Ecriture du fichier ' . $filePath . ' impossible.';
		return;
	}

	// suppression de fichier
	if (@unlink ($filePath) == false) {
		$pErrors[$pKey] = 'Suppression du fichier ' . $filePath . ' impossible.';
		return;
	}

	// suppression du répertoire
	if (@rmdir ($dirPath) === false) {
		$pErrors[$pKey] = 'Suppression du répertoire ' . $dirPath . ' impossible.';
		return;
	}
}

// retourne le realpath, ou 'Répertoire inexistant' si le répertoire n'existe pas
function _realPath ($pPath) {
	if (function_exists ('realpath')) {
		$toReturn = realpath ($pPath);
		if (is_dir ($toReturn)) {
			$toReturn .= DIRECTORY_SEPARATOR;
		}
		if ($toReturn == false) {
			$toReturn = 'Répertoire inexistant';
		}
	} else {
		$toReturn = (is_dir ($pPath)) ? $pPath : 'Répertoire inexistant';
	}
	return $toReturn;
}

// connexion à la base avec les infos en session
function db_connect () {
	$host = getSession ('database_host');
	$database = getSession ('database_name');
	$login = getSession ('database_login');
	$password = getSession ('database_password');
	$driver = getSession ('database_driver');
	$toReturn = null;

	switch ($driver) {
		case 'mysql' :
			$toReturn = @mysql_connect ($host, $login, $password);
			if ($toReturn === false) {
				$toReturn = mysql_error ();
			}
			if (mysql_select_db ($database) === false) {
				$toReturn = mysql_error ();
			}
			break;

		case 'pdo_mysql' :
		case 'pdo_sqlite' :
			try {
				$toReturn = new PDO (substr ($driver, 4) . ':host=' . $host . ';dbname=' . $database, $login, $password);
			} catch (Exception $e) {
				$toReturn = $e->getMessage ();
			}
			break;
	}
	return $toReturn;
}

// execute une requête
function db_query ($pConnexion, $pQuery, $pModule = null) {
	$error = null;

	switch (getSession ('database_driver')) {
		case 'mysql' :
			mysql_query ($pQuery);
			$error = utf8_encode (mysql_error ());
			break;
		default :
			if (($query = $pConnexion->prepare ($pQuery)) == false) {
				$errorInfo = $pConnexion->errorInfo ();
				$error = $errorInfo[2];
			} else if ($query->execute () == false) {
				$errorInfo = $query->errorInfo ();
				$error = $errorInfo[2];
			}
	}

	if ($error != null) {
		$message = 'Erreurs lors de l\'execution de la requête : ' . $error;
		$message .= '<br />';
		if ($pModule != null) {
			$message .= '<br /><b>Module</b> : ' . $pModule;
		}
		$message .= '<br /><b>Requête</b> :<br />' . $pQuery;
		error ($message);
	}
}

$verificationsErrors = getVerificationsErrors ();
$databaseErrors = getDatabaseErrors ();
$configErrors = getConfigErrors ();

// on vérifie qu'on n'a pas d'erreur dans les etapes précédentes
if ($ETAPE == 1) {
	$errors = array ();
} else if ($ETAPE == 2) {
	$errors = $verificationsErrors;
} else if ($ETAPE == 3) {
	$errors = array_merge ($verificationsErrors, $databaseErrors);
} else {
	$errors = array_merge ($verificationsErrors, $databaseErrors, $configErrors);
}
if (count ($errors) > 0) {
	header ('Location: index.php');
	exit ();
}