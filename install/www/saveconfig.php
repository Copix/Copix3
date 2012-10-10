<?php
$ETAPE = 2;
require ('include.php');

// sauvegarde des données saisies
setSession ('config_overwrite', ($_POST['overwrite'] == 'true'));
$modules = array ();
foreach ($_POST as $key => $value) {
	if (substr ($key, 0, 7) == 'module_') {
		$modules[] = substr ($key, 7);
	}
}
setSession ('config_modules', $modules);
if (in_array ('auth', $modules)) {
	setSession ('config_admin_login', $_POST['login']);
	setSession ('config_admin_password', $_POST['password']);
}

// vérification des données
$errors = getConfigErrors ();
if (count ($errors) > 0) {
	header ('Location: config.php?errors=true');
	exit ();
}

header ('Location: install.php');