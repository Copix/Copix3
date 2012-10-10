<?php
$TITLE = 'Vérifications';
$ETAPE = 1;
require_once ('include.php');
require_once ('theme/header.php');
?>

<h2 class="first">PHP</h2>
<table class="CopixTable">
	<tr>
		<th>Configuration</th>
		<th>Requise</th>
		<th>Actuelle</th>
		<th></th>
	</tr>
	<tr>
		<td>Version de PHP</td>
		<td>5.1.6 minimum</td>
		<td><?php echo PHP_VERSION ?></td>
		<td class="result">
			<?php if (!isset ($verificationsErrors['version'])) { ?>
				<div class="success"></div>
			<?php } else { ?>
				<div class="error"></div>
			<?php } ?>
		</td>
	</tr>
	<tr class="alternate">
		<td>SimpleXML</td>
		<td>Installé</td>
		<td><?php echo (isset ($verificationsErrors['simplexml'])) ? 'Non installé' : 'Installé' ?></td>
		<td class="result">
			<?php if (!isset ($verificationsErrors['simplexml'])) { ?>
				<div class="success"></div>
			<?php } else { ?>
				<div class="error" title="<?php echo $verificationsErrors['simplexml'] ?>"></div>
			<?php } ?>
		</td>
	</tr>
</table>

<h2>Droits</h2>
<?php
function html_right ($pPath, $pName, $pResult, $pType, $pAlternate = false) {
	?>
	<tr <?php if ($pAlternate) { echo 'class="alternate"'; } ?>>
		<td><?php help ($pPath) ?></td>
		<td><?php echo $pName ?></td>
		<td><?php echo $pType ?></td>
		<td>
			<?php
			if (is_writable ($pPath)) {
				echo 'Ecriture';
			} else if (is_readable ($pPath)) {
				echo 'Lecture';
			} else {
				echo 'Aucun';
			}
			?>
		</td>
		<td class="result">
			<?php if ($pResult === null) { ?>
				<div class="success"></div>
			<?php } else { ?>
				<div class="error" title="[<?php echo $pPath ?>] <?php echo $pResult ?>"></div>
			<?php } ?>
		</td>
	</tr>
	<?php
}
?>
<table class="CopixTable">
	<tr>
		<th></th>
		<th>Répertoire ou fichier</th>
		<th>Requis</th>
		<th>Actuel</th>
		<th></th>
	</tr>
	<?php
	html_right (_realPath (COPIX_TEMP_PATH), 'COPIX_TEMP_PATH', @$verificationsErrors['temp_rights'], 'Ecriture');
	html_right (_realPath (COPIX_CACHE_PATH), 'COPIX_CACHE_PATH', @$verificationsErrors['cache_rights'], 'Ecriture', true);
	html_right (_realPath (COPIX_VAR_PATH), 'COPIX_VAR_PATH', @$verificationsErrors['var_rights'], 'Ecriture');
	html_right (_realPath (COPIX_LOG_PATH), 'COPIX_LOG_PATH', @$verificationsErrors['log_rights'], 'Ecriture', true);
	html_right (_realpath (COPIX_UTILS_PATH), 'COPIX_UTILS_PATH', @$verificationsErrors['utils_rights'], 'Lecture', true);
	html_right (_realpath (COPIX_SMARTY_PATH), 'COPIX_SMARTY_PATH', @$verificationsErrors['smarty_rights'], 'Lecture');
	html_right (_realpath (COPIX_INC_FILE), 'COPIX_INC_FILE', @$verificationsErrors['copixinc_rights'], 'Lecture', true);
	html_right (_realpath (COPIX_CLASSPATHS_FILE), 'COPIX_CLASSPATHS_FILE', @$verificationsErrors['classpaths_rights'], 'Lecture');
	?>
</table>

<br />
<?php if (count ($verificationsErrors) == 0) { ?>
	<center><input type="button" onclick="javascript: document.location = 'database.php'" value=">> Base de données" /></center>
<?php } else { ?>
	<div class="errorVerification">
		Des erreurs se sont produites durant la vérification des configurations requises pour installer Copix.
		<br />
		Veuillez corriger ces problèmes, et rafraichir cette page.
	</div>
<?php } ?>

<? require ('theme/footer.php'); ?>