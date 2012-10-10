<?php _eTag ('error', array ('message' => $ppo->errors)) ?>

<h2 class="first">Informations</h2>
<form action="<?php echo _url ('logreader||doEdit') ?>" method="POST">
<input type="hidden" name="file" value="<?php echo $ppo->file->getId () ?>" />
<table class="CopixVerticalTable">
	<tr>
		<th>Type <span class="required">*</span></th>
		<td><?php _eTag ('select', array ('values' => $ppo->types, 'name' => 'type', 'emptyShow' => false, 'selected' => $ppo->file->getType ())) ?></td>
		<td><?php _eTag ('popupinformation', array (), 'Choisir le bon type permettra d\'épurer l\'affichage des logs.') ?></td>
	</tr>
	<tr class="alternate">
		<th style="width: 100px">Fichier <span class="required">*</span></th>
		<td><input type="text" name="path" id="path" class="inputText" style="width: 100%" value="<?php echo $ppo->file->getFilePath () ?>" onchange="isFileExists ()" /></td>
		<td style="width: 20px"><div id="fileExists"></div></td>
	</tr>
	<tr>
		<th>Rotation</th>
		<td><input type="text" name="rotation" class="inputText" style="width: 100%" value="<?php echo $ppo->file->getRotationFilePath () ?>" /></td>
		<td><?php _eTag ('popupinformation', array (), 'Chemin et nom pour les fichiers de rotation (généralement compressés).<br />Utiliser * pour indiquer l\'index de la rotation.') ?></td>
	</tr>
</table>

<br />
<center>
	<input type="submit" class="inputSubmit" value="<?php echo ($ppo->mode == 'add') ? 'Ajouter' : 'Modifier' ?>" />
</center>
</form>

<br />
<table style="width: 100%">
	<tr>
		<td>
			<?php if ($ppo->mode == 'edit') { ?>
				<a href="<?php echo _url ('logreader||show', array ('file' => $ppo->file->getId ())) ?>">
					<img title="Voir le contenu" alt="Voir le contenu" src="<?php echo _resource ('img/tools/show.png') ?>"/>
					Voir le contenu
				</a>
			<?php } ?>
		</td>
		<td><?php _eTag ('back', array ('url' => 'logreader||')) ?></td>
	</tr>
</table>

<script type="text/javascript">
function isFileExists () {
	$ ('fileExists').innerHTML = '<img src="<?php echo _resource ('img/tools/load.gif') ?>" />';
	new Request.HTML ({
		url: '<?php echo _url ('logreader||FileExists', array ('path' => null)) ?>' + $ ('path').value,
		update: $ ('fileExists'),
		onFailure: function(response) { $ ('fileExists').innerHTML = ''; }
	}).post ();
}

isFileExists ();
</script>