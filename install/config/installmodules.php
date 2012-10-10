<?php
// modules à installer par défaut
// le module qui créé la table copixmodule doit être le premier, ici admin
$installModules = array (
	'admin' => COPIX_PROJECT_PATH . 'modules/stable/standard/admin/',
	'auth' => COPIX_PROJECT_PATH . 'modules/stable/standard/auth/',
	'default' => COPIX_PROJECT_PATH . 'modules/stable/standard/default/',
	'generictools' => COPIX_PROJECT_PATH . 'modules/stable/standard/generictools/'
);