<?php
$imgFolder = _resource ('backup|img/folder.png');
$imgFile = _resource ('backup|img/file.png');
?>
<div id="backupBrowser">
	<?php $currentPath = null ?>
	<?php foreach ($pathParts as $name) { $currentPath .= $name . '/'; ?><a href="#" onclick="loadPath ('<?php echo $currentPath ?>')"><?php echo $name ?></a>/<?php } ?>
	<div style="width: 550px; height: 300px; overflow: scroll" id="backupBrowserFiles">
		<table class="CopixTable">
			<tr>
				<th style="width: 1px"></th>
				<th style="width: 1px"></th>
				<th>Nom</th>
			</tr>
			<?php foreach ($dirs as $dir) { ?>
				<tr <?php _eTag ('trclass') ?>>
					<td><input type="checkbox" name="browserFiles" value="<?php echo $dir ?>" /></td>
					<td>
						<a href="#" onclick="loadPath ('<?php echo str_replace ('\\', '\\\\', $dir) . '/' ?>')">
							<img src="<?php echo $imgFolder ?>" alt="Répertoire" title="Répertoire" />
						</a>
					</td>
					<td><?php echo CopixFile::extractFileName ($dir) ?></td>
				</tr>
			<?php } ?>
			<?php foreach ($files as $file) { ?>
				<tr <?php _eTag ('trclass') ?>>
					<td><input type="checkbox" name="browserFiles" value="<?php echo $file ?>" /></td>
					<td><img src="<?php echo $imgFile ?>" alt="Répertoire" title="Répertoire" /></td>
					<td><?php echo CopixFile::extractFileName ($file) ?></td>
				</tr>
			<?php } ?>
		</table>
	</div>
	
	<br />
	<center>
		<?php _eTag ('button', array ('type' => 'button', 'name' => 'chooseFiles', 'caption' => 'Ajouter', 'extra' => 'onclick="addFiles ()"')) ?>
		<?php _eTag ('button', array ('type' => 'button', 'name' => 'cancelFiles', 'caption' => 'Annuler', 'extra' => 'onclick="cancelFiles ()"')) ?>
	</center>
</div>