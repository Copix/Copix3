<?php
$TITLE = 'Base de données';
$ETAPE = 2;
require_once ('include.php');
require_once ('theme/header.php');

// recherche des drivers disponibles
$driversPDO = array ('mysql' => 'PDO MySQL', 'pdsql' => 'PDO PostGre SQL', 'sqlite' => 'PDO SQLite');
$driversAvailables = array ();
if (function_exists ('mysql_connect')) {
	$driversAvailables['mysql'] = 'MySQL';
}
if (class_exists ('PDO', false)) {
	$driversPDOAvailabes = PDO::getAvailableDrivers ();
	foreach ($driversPDO as $driver => $caption) {
		if (in_array ($driver, $driversPDOAvailabes)) {
			$driversAvailables['pdo_' . $driver] = $caption;
		}
	}
}

?>

<?php
$errors = array ();
if (@$_GET['errors'] == 'true' && count ($databaseErrors) > 0) {
	$errors = $databaseErrors;
	?>
	<h2 class="error">Erreurs</h2>
	<ul class="errors">
		<?php foreach ($errors as $error) { ?>
			<li><?php echo $error ?></li>
		<?php } ?>
	</ul>
<?php } ?>

<h2 class="first">Serveur de base de données</h2>

<form action="savedatabase.php" method="post">

<table class="CopixVerticalTable">
	<tr>
		<th>Adresse ou IP</th>
		<td>
			<input type="text" name="host" value="<?php echo getSession ('database_host', 'localhost') ?>"
			<?php if (isset ($errors['host'])) { echo 'class="inputTextError"'; } else { echo 'class="inputText"'; } ?> />
		</td>
		<td class="help"><?php help ('localhost pour la plupart des hébergements (free, 1&1, OVH, etc).') ?></td>
	</tr>
	<tr>
		<th>Driver</th>
		<td>
			<select name="driver">
				<?php foreach ($driversAvailables as $name => $caption) { ?>
					<option value="<?php echo $name ?>" <?php if ($name == getSession ('database_driver')) { echo 'selected="selected"'; } ?>><?php echo $caption ?></option>
				<?php } ?>
			</select>
		</td>
		<td class="help"><?php help ('Il est préférable d\'utiliser les drivers PDO, si disponibles.') ?></td>
	</tr>
	<tr>
		<th>Base de données</th>
		<td>
			<input type="text" name="database" value="<?php echo getSession ('database_name') ?>"
			<?php if (isset ($errors['name'])) { echo 'class="inputTextError"'; } else { echo 'class="inputText"'; } ?> />
		</td>
		<td class="help"><?php help ('La base de données doit exister, Copix ne fera pas de CREATE DATABASE.') ?></td>
	</tr>
</table>

<h2>Connexion à la base de données</h2>
<table class="CopixVerticalTable">
	<tr>
		<th>Identifiant</th>
		<td>
			<input type="text" class="inputText" name="login" value="<?php echo getSession ('database_login', 'root') ?>"
			<?php if (isset ($errors['login'])) { echo 'class="inputTextError"'; } else { echo 'class="inputText"'; } ?> />
		</td>
		<td class="help"><?php help ('Login de l\'utilisateur pour se connecter à la base de données.') ?></td>
	</tr>

	<tr class="alternate">
		<th>Mot de passe</th>
		<td>
			<input type="password" name="password" value="<?php echo getSession ('database_password') ?>"
			<?php if (isset ($errors['password'])) { echo 'class="inputTextError"'; } else { echo 'class="inputText"'; } ?> />
		</td>
		<td class="help"><?php help ('Mot de passe de l\'utilisateur pour se connecter à la base de données.') ?></td>
	</tr>
</table>

<br />
<center><input type="submit" value=">> Configuration" /></center>
</form>

<? require ('theme/footer.php'); ?>