<?php
$TITLE = 'Installation';
$ETAPE = 4;
require_once ('include.php');
require_once ('theme/header.php');

// connexion à la base
$connexion = db_connect ();
$driver = getSession ('database_driver');

?>
<h2>Installation des modules</h2>
<table class="CopixVerticalTable">
	<?php
	$modulesToInstall = getSession ('config_modules');
	foreach ($modulesToInstall as $module) {
		
		// execution des scripts SQL
		$nbrQueries = 0;
		$installDir = $installModules[$module] . COPIX_INSTALL_DIR . 'scripts/';
		if ($hwnd = @opendir ($installDir)) {
			while (($file = readdir ($hwnd)) !== false) {
				if ($file == 'install.' . $driver . '.sql' || $file == 'prepareinstall.' . $driver . '.sql') {
					// code copié de CopixDbConnection::doSQLSscript, l'idée étant de ne surtout rien appeler de Copix lors de l'install
					$lines = file ($installDir . $file);
					$cmdSQL = '';
					foreach ((array)$lines as $key => $line) {
						// la ligne n'est ni vide ni commentaire
						if ((!preg_match ('/^\s*#/', $line)) && (strlen (trim ($line)) > 0)) {
							$cmdSQL .= $line;
							// Si on est à la ligne de fin de la commande on l'execute
							if (preg_match ('/;\s*$/', $line)) {
								// On nettoie la commande du ";" de fin et on l'execute
								$cmdSQL = preg_replace ('/;\s*$/', '', $cmdSQL);
								db_query ($connexion, $cmdSQL, $module);

								$nbrQueries++;
								$cmdSQL = '';
							}
						}
					}
				}
			}
		}

		// recherche de la version
		$xml = simplexml_load_file ($installModules[$module] . 'module.xml');
		$attributes = $xml->general->default->attributes ();
		$version = (isset ($attributes['version'])) ? $attributes['version'] : null;

		// ajout dans copixmodule
		$query = 'INSERT INTO copixmodule (name_cpm, version_cpm) VALUES (\'' . $module . '\', \'' . $version . '\')';
		db_query ($connexion, $query, $module);
		?>
		<tr>
			<th><?php echo $module ?></th>
			<td><?php echo ($nbrQueries <= 1) ? $nbrQueries . ' requête' : $nbrQueries . ' requêtes' ?></td>
			<td class="result"><div class="success"></div></td>
		</tr>
	<?php } ?>
</table>

<h2>Configuration</h2>
<table class="CopixVerticalTable">

	<?php
	// modification du mot de passe de l'admin
	if (in_array ('auth', $modulesToInstall)) {
		$query = 'UPDATE dbuser SET login_dbuser = \'' . getSession ('config_admin_login') . '\', password_dbuser = \'' . md5 (getSession ('config_admin_password')) . '\'';
		db_query ($connexion, $query);
		?>
		<tr>
			<th>Compte administrateur</th>
			<td><?php echo getSession ('config_admin_login') ?> - <?php echo getSession ('config_admin_password') ?></td>
			<td class="result"><div class="success"></div></td>
		</tr>
		<?php
	}

	// création des fichiers de configuration
	function write_config ($pFilePath, $pContent) {
		$pContent = '<?php' . "\n" . $pContent . "\n" . '?>';
		if (!file_put_contents ($pFilePath, $pContent)) {
			error ('Le fichier de configuration "' . $pFilePath . '" n\'a pu être écrit.');
		}
	}

	if (getSession ('config_overwrite', true)) {
		$configPath = COPIX_VAR_PATH . 'config/';
		if (!is_dir ($configPath)) {
			if (!mkdir ($configPath, 0755)) {
				error ('La création du répertoire "' . $configPath . '" n\'a pu être effectuée.');
			}
		}

		// credentials handlers
		$handlers = array (
			'admin|installcredentialhandler' => array (
				'name' => 'admin|installcredentialhandler',
				'stopOnSuccess' => true,
				'stopOnFailure' => false
			),
			'auth|dbcredentialhandler' => array (
				'name' => 'auth|dbcredentialhandler',
				'stopOnSuccess' => true,
				'stopOnFailure' => false
			),
			'auth|dbmodulecredentialhandler' => array (
				'name' => 'auth|dbmodulecredentialhandler',
				'stopOnSuccess' => true,
				'stopOnFailure' => false,
				'handle' => array (0 => 'module')
			)
		);
		$content = '$_credential_handlers = ' . var_export ($handlers, true) . ';';
		write_config ($configPath . 'credential_handlers.conf.php', $content);

		// group handlers
		$handlers = array ('auth|dbgrouphandler' => array (
			'name' => 'auth|dbgrouphandler',
			'required' => null
		));
		$content = '$_group_handlers = ' . var_export ($handlers, true) . ';';
		write_config ($configPath . 'group_handlers.conf.php', $content);

		// user handlers
		$handlers = array ('auth|dbuserhandler' => array (
			'name' => 'auth|dbuserhandler',
			'required' => false,
			'rank' => null
		));
		$content = '$_user_handlers = ' . var_export ($handlers, true) . ';';
		write_config ($configPath . 'user_handlers.conf.php', $content);
		?>
		<tr>
			<th>Connexions</th>
			<td>*_handlers.conf.php</td>
			<td class="result"><div class="success"></div></td>
		</tr>
		<?php

		// caches
		$caches = array ('default' => array (
			'name' => 'default',
			'enabled' => true,
			'strategy' => 'file',
			'dir' => 'default',
			'link' => '',
			'duration' => 0
		));
		$content = '$_cache_types = ' . var_export ($caches, true) . ';';
		write_config ($configPath . 'cache_profiles.conf.php', $content);
		?>
		<tr>
			<th>Caches</th>
			<td>cache_profiles.conf.php</td>
			<td class="result"><div class="success"></div></td>
		</tr>
		<?php

		// plugins
		$plugins = array (0 => 'default|configure', 1 => 'default|magicquotes');
		$content = '$_plugins = ' . var_export ($plugins, true) . ';';
		write_config ($configPath . 'plugins.conf.php', $content);
		?>
		<tr>
			<th>Plugins</th>
			<td>plugins.conf.php</td>
			<td class="result"><div class="success"></div></td>
		</tr>
		<?php

		// base de données
		$profiles = array ('copix' => array (
			'driver' => $driver,
			'connectionString' => 'dbname=' . getSession ('database_name'),
			'user' => getSession ('database_login'),
			'password' => getSession ('database_password'),
			'extra' => array (),
			'default' => true,
			'available' => true,
			'errorNotAvailable' => '',
		));
		$content = '$_db_profiles = ' . var_export ($profiles, true) . ';';
		$content .= "\n\n" . '$_db_default_profile = \'copix\';';
		write_config ($configPath . 'db_profiles.conf.php', $content);
		?>
		<tr>
			<th>Base de données</th>
			<td>db_profiles.conf.php</td>
			<td class="result"><div class="success"></div></td>
		</tr>
		
		<?php
		// i18n_handlers
		$handlers = array ('i18nlocalhandler'=> array ('name' => 'i18nlocalhandler', 'context' => 'default')); 
		$content = '$_i18n_handlers = ' . var_export ($handlers, true) . ';';
		write_config ($configPath . 'i18n_handlers.conf.php', $content);
		?>
		<tr>
			<th>I18N</th>
			<td>i18n_handlers.conf.php</td>
			<td class="result"><div class="success"></div></td>
		</tr>
		<?php
	}
?>
</table>

<?php
// suppression du cache des modules, pour que la liste soit remise à jour
@unlink (COPIX_CACHE_PATH . 'php/copixmodule.php');
@unlink (COPIX_CACHE_PATH . 'php/copixallmodule.php');
?>

<br /><br />
<center>
	<img src="theme/img/install_finished.png" /> Copix a été installé avec succès.
	<br />
	<font color="red">Pensez à supprimer le répertoire install/, qui représente une très grosse faille de sécurité une fois Copix installé.</font>
</center>

<? require ('theme/footer.php'); ?>