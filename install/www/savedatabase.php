<?php
$ETAPE = 2;
require ('include.php');

// sauvegarde des données saisies
setSession ('database_host', $_POST['host']);
setSession ('database_driver', $_POST['driver']);
setSession ('database_name', $_POST['database']);
setSession ('database_login', $_POST['login']);
setSession ('database_password', $_POST['password']);

// vérification des données
$errors = getDatabaseErrors ();

if (count ($errors) > 0) {
	header ('Location: database.php?errors=true');
	exit ();
}

header ('Location: config.php');