<?php _eTag ('error', array ('message' => $ppo->errors)) ?>

<?php _eTag ('beginblock', array ('title' => 'Informations générales', 'isFirst' => true)) ?>

<form action="<?php echo _url ('backup|profiles|doEdit') ?>" method="POST" id="backupProfile">
<input type="hidden" name="idErrors" value="<?php echo $ppo->idErrors ?>" />
<input type="hidden" name="profile" value="<?php echo $ppo->profile->getId () ?>" />
<table class="CopixVerticalTable">
	<tr <?php _eTag ('trclass') ?>>
		<th style="width: 150px">Nom <span class="required">*</span></th>
		<th style="width: 1px"></th>
		<td><?php _eTag ('inputtext', array ('name' => 'caption', 'value' => $ppo->profile->getCaption ())) ?></td>
	</tr>
	<tr>
		<th>Nom du fichier <span class="required">*</span></th>
		<th></th>
		<td><?php _eTag ('inputtext', array ('name' => 'fileName', 'value' => $ppo->profile->getFileName ())) ?></td>
	</tr>
	<tr <?php _eTag ('trclass') ?>>
		<th>Type de sauvegarde <span class="required">*</span></th>
		<th><?php _eTag ('popupinformation', array (), 'Indique de quelle manière sera stockée la sauvegarde.') ?></th>
		<td>
			<?php _eTag ('select', array ('emptyShow' => false, 'name' => 'type', 'values' => $ppo->types, 'selected' => $ppo->profile->getIdType (), 'extra' => 'onchange="javascript: onChangeType (this.value)"')) ?>
			<span id="typeOptionsEditorLoading"></span>
		</td>
	</tr>
</table>
<?php _eTag ('endblock') ?>

<div id="typeOptionsEditor"><?php echo CopixZone::process ('backup|TypeOptionsEditor', array ('type' => $ppo->profile->getType ())) ?></div>

<?php _eTag ('beginblock', array ('title' => 'Base de données')) ?>
<table class="CopixVerticalTable">
	<tr <?php _eTag ('trclass') ?>>
		<th style="width: 176px">Profil de connexion</th>
		<td><?php _eTag ('select', array ('emptyShow' => false, 'name' => 'dbprofile', 'values' => $ppo->dbprofiles, 'selected' => $ppo->profile->getDbProfile (), 'extra' => 'onchange="javascript: onChangeDbProfile (this.value);"')) ?></td>
	</tr>
	<tr <?php _eTag ('trclass') ?>>
		<th>Tables à sauvegarder</th>
		<td>
			<input type="checkbox" name="saveAllTables" <?php if ($ppo->profile->saveAllTables ()) { echo 'checked="checked"'; } ?> id="saveAllTables" value="1" />
			<label for="saveAllTables"> Sauvegarder toutes les tables</label>
			<br />
			<div id="listTables"><?php echo CopixZone::process ('backup|ListTables', array ('dbprofile' => $ppo->profile->getDbProfile (), 'selected' => $ppo->profile->getTables ())) ?></div>
		</td>
	</tr>
</table>
<?php _eTag ('endblock') ?>

<?php _eTag ('beginblock', array ('title' => 'Fichiers')) ?>
<table class="CopixTable">
	<tr>
		<th style="width: 150px">Racine de Copix</th>
		<th style="width: 1px"><?php _eTag ('popupinformation', array (), 'Le répertoire d\'installation de Copix') ?></th>
		<td><?php _eTag ('inputtext', array ('name' => 'filesPath', 'value' => $ppo->profile->getFilesPath (), 'style' => 'width: 99%')) ?></td>
	</tr>
</table>
<br />
<table class="CopixTable" id="tableFiles">
	<tr>
		<th style="width: 16px"></th>
		<th>Nom</th>
		<th style="width: 16px"></th>
	</tr>
</table>
<br />
<?php _eTag ('button', array ('type' => 'button', 'caption' => 'Ajouter un répertoire ou un fichier', 'img' => 'backup|img/folder.png', 'id' => 'showBrowser')) ?>
<?php _eTag ('endblock') ?>

<br />
<center><input type="submit" value="Valider" /></center>
</form>

<?php
$params = array (
	'id' => 'backupBrowser',
	'title' => 'Ajouter un répertoire ou un fichier',
	'clicker' => 'showBrowser'
);
_eTag ('copixwindow', $params, _tag ('copixzone', array ('process' => 'backup|browser')));
?>

<?php _eTag ('back', array ('url' => 'backup|profiles|')) ?>

<script type="text/javascript">
function onChangeType (pType) {
	Copix.setLoadingHTML ($ ('typeOptionsEditorLoading'));
	$ ('type').set ('disabled', 'disabled');
	new Request.HTML ({
		update: 'typeOptionsEditor',
		url: '<?php echo _url ('backup|profiles|getTypeOptionsEditor') ?>',
		onComplete: function () {
			$ ('typeOptionsEditorLoading').innerHTML = '';
			$ ('type').set ('disabled', '');
		}
	}).post ({'type': pType});
}

function onChangeDbProfile (pProfile) {
	new Request.HTML ({
		update: 'listTables',
		url: '<?php echo _url ('backup|profiles|getTableList') ?>'
	}).post ({'dbprofile': pProfile});
}
</script>

<?php
$js = array ();
foreach ($ppo->profile->getFiles () as $file) {
	$kind = (is_dir ($ppo->profile->getFilesPath () . $file)) ? 'dir' : 'file';
	$js[] = str_replace ('\\', '\\\\', 'addFile (\'' . $file . '\', \'' . $kind . '\');');
}
if (count ($js) > 0) {
	CopixHTMLHeader::addJSDOMReadyCode (implode ("\n", $js));
}
?>