<?php if ($restore) { ?>
	<form action="<?php echo _url ('backup||doRestore') ?>" method="POST">
	<input type="hidden" name="backupFilesPath" value="<?php echo $backupFilesPath ?>" />
	<?php
} else {
	_eTag ('notification', array ('title' => 'Sauvegarde effectuée', 'message' => $infos->getMessage ()));
}
?>

<?php _eTag ('beginblock', array ('title' => 'Informations', 'isFirst' => true)) ?>
<table class="CopixVerticalTable">
	<tr <?php _eTag ('trclass') ?>>
		<th style="width: 150px">Profil</th>
		<td style="width: 350px"><?php echo $infos->getProfile () ?> (<?php echo $infos->getIdProfile () ?>)</td>
		<th style="width: 150px">Utilisateur</th>
		<td><?php echo $infos->getUser () ?>@<?php echo $infos->getUserHandler () ?></td>
	</tr>
	<tr <?php _eTag ('trclass') ?>>
		<th>Date</th>
		<td><?php echo $infos->getDate () ?></td>
		<th>Adresse</th>
		<td><?php echo $infos->getURL () ?></td>
	</tr>
</table>
<?php _eTag ('endblock') ?>

<?php if ($infos->countTables () > 0) { ?>
	<?php _eTag ('beginblock', array ('title' => 'Base de données')) ?>
	<table class="CopixVerticalTable">
		<tr>
			<th style="width: 150px">Profil de connexion <?php if ($restore) { ?><span class="required">*</span><?php } ?></th>
			<td style="width: 350px">
				<?php
				if ($restore) {
					echo '<select name="dbProfile">';
					echo '<option value="---">---</option>';
					foreach ($dbprofiles as $profile) {
						echo '<option value="' . $profile . '">' . $profile  .'</option>';
					}
					echo '</select>';
				} else {
					echo $infos->getDbProfile ();
				}
				?>
			</td>
			<th style="width: 150px">Driver</th>
			<td><?php echo $infos->getDbDriver () ?></td>
		</tr>
	</table>

	<br />
	<table class="CopixTable">
		<tr>
			<?php if ($restore) { ?>
				<th style="width: 16px"><input type="checkbox" name="restoreTables" checked="checked" id="restoreTables" /></th>
			<?php } ?>
			<th>Table</th>
			<th style="width: 120px">Enregistrements</th>
		</tr>
		<?php foreach ($infos->getTables () as $table => $records) { ?>
			<tr <?php _eTag ('trclass') ?>>
				<?php if ($restore) { ?>
					<td>
						<input type="checkbox" name="table_<?php echo $table ?>" checked="checked" id="table_<?php echo $table ?>" />
					</td>
				<?php } ?>
				<td><label for="table_<?php echo $table ?>"><?php echo $table ?></label></td>
				<td style="text-align: right"><?php echo $records ?></td>
			</tr>
		<?php } ?>
	</table>
	<?php _eTag ('endblock') ?>
<?php } ?>
	
<?php if ($infos->countFiles () > 0) { ?>
	<?php _eTag ('beginblock', array ('title' => 'Fichiers')) ?>
	<table class="CopixTable">
		<tr>
			<th style="width: 150px">Racine de Copix <?php if ($restore) { ?><span class="required">*</span><?php } ?></th>
			<td>
				<?php
				if ($restore) {
					_eTag ('inputtext', array ('name' => 'filesPath', 'value' => $infos->getFilesPath (), 'style' => 'width: 99%'));
				} else {
					echo $infos->getFilesPath ();
				}
				?>
			</td>
		</tr>
	</table>
	<br />
	<table class="CopixTable">
		<tr>
			<th>Répertoire</th>
			<th style="width: 70px">Fichiers</th>
			<th style="width: 70px">Taille</th>
		</tr>
		<?php
		$folders = array ();
		foreach ($infos->getFiles () as $file) {
			if (!array_key_exists ($file->getPath (), $folders)) {
				$folders[$file->getPath ()] = array (
					'files' => array (),
					'count' => 0,
					'size' => 0
				);
			}
			$folders[$file->getPath ()]['files'][] = $file;
			$folders[$file->getPath ()]['count']++;
			$folders[$file->getPath ()]['size'] += $file->getSize ();
		}
		
		foreach ($folders as $path => $folder) {
			?>
			<tr>
				<td><?php echo $path ?></td>
				<td style="text-align: right"><?php echo $folder['count'] ?></td>
				<td style="text-align: right"><?php echo _filter ('OctetsToText')->get ($folder['size']) ?></td>
			</tr>
			<?php
		}
		?>
	</table>
	<?php _eTag ('endblock') ?>
<?php } ?>

<?php if ($restore) { ?>
	<br />
	<center>
		<input type="submit" value="Restaurer la sauvegarde" />
	</center>
	</form>
<?php } ?>