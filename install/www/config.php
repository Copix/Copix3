<?php
$TITLE = 'Installation';
$ETAPE = 3;
require_once ('include.php');
require_once ('theme/header.php');
?>

<?php
$errors = array ();
if (@$_GET['errors'] == 'true' && count ($configErrors) > 0) {
	$errors = $configErrors;
	?>
	<h2 class="error">Erreurs</h2>
	<ul class="errors">
		<?php foreach ($errors as $error) { ?>
			<li><?php echo $error ?></li>
		<?php } ?>
	</ul>
<?php } ?>

<form action="saveconfig.php" method="POST">

<?php if (SHOW_MODULES_LIST == false) { ?>
	<div style="display: none">
<?php } ?>
<h2>Modules à installer</h2>
Les modules sélectionnés seront installés avec Copix :
<table style="width: 100%;">
	<?php
	$col = 0;
	$isFirst = true;
	foreach ($installModules as $module => $path) {
		$col++;
		if ($col == 1) {
			echo ($isFirst) ? '<tr>' : '</tr><tr>';
		} else if ($col == 3) {
			$col = 0;
		}
		$isFirst = false;
		?>
		<td style="width: 33%">
			<input type="checkbox" checked="checked" id="module_<?php echo $module ?>" name="module_<?php echo $module ?>"
			/><label for="module_<?php echo $module ?>"> <?php echo $module ?></label>
		</td>
	<?php } ?>
	</tr>
</table>
<?php if (SHOW_MODULES_LIST == false) { ?>
	</div>
<?php } ?>

<h2>Fichiers de configuration</h2>
Des fichiers de configuration vont être créés dans COPIX_VAR_PATH.
<br />
Cependant, il est possible que vous ne vouliez pas que les fichiers existants soient écrasés.
<br />
<input type="checkbox" checked="checked" id="overwrite" name="overwrite" value="true"
/><label for="overwrite"> Ecraser les fichiers de configuration</label>

<?php if (in_array ('auth',array_keys ($installModules))) { ?>
	<h2>Compte administrateur</h2>
	<table class="CopixVerticalTable">
		<tr>
			<th>Identifiant</th>
			<td>
				<input type="text" style="width: 98%" name="login" value="<?php echo getSession ('config_admin_login', 'admin') ?>"
				<?php if (isset ($errors['login'])) { echo 'class="inputTextError"'; } else { echo 'class="inputText"'; } ?> />
			</td>
		</tr>
		<tr>
			<th>Mot de passe</th>
			<td>
				<input type="text" style="width: 98%" name="password" value="<?php echo getSession ('config_admin_password') ?>"
				<?php if (isset ($errors['password'])) { echo 'class="inputTextError"'; } else { echo 'class="inputText"'; } ?> />
			</td>
		</tr>
	</table>
<?php } ?>

<br /><br />
<center>
	<input type="submit" value=">> Installer Copix" />
</center>
</form>

<? require ('theme/footer.php'); ?>