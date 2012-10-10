<?php $isFirst = true ?>

<?php
if (count ($ppo->tables) > 0) {
	_eTag ('beginblock', array ('title' => 'Tables restaurées', 'isFirst' => $isFirst));
	$isFirst = false;
	?>
	<table class="CopixTable">
		<tr>
			<th>Nom</th>
			<th style="width: 120px">Enregistrements</th>
			<th></th>
		</tr>
		<?php foreach ($ppo->tables as $name => $table) { ?>
			<tr <?php _eTag ('trclass') ?>>
				<td><?php echo $name ?></td>
				<td style="text-align: right"><?php echo $table['count'] ?> / <?php echo $table['records'] ?></td>
				<td class="action">
					<?php if ($table['error'] == null) { ?>
						<img src="<?php echo _resource ('img/tools/valid.png') ?>" />
					<?php } else { ?>
						<img src="<?php echo _resource ('img/tools/error.png') ?>" alt="Erreur" title="<?php echo str_replace ('"', "''", $table['error']) ?>" />
					<?php } ?>
				</td>
			</tr>
		<?php } ?>
	</table>
	<?php _eTag ('endblock') ?>
<?php } ?>

<?php
if (count ($ppo->files) > 0) {
	_eTag ('beginblock', array ('title' => 'Fichiers restaurés', 'isFirst' => $isFirst));
	$isFirst = false;
	?>
	<table class="CopixTable">
		<tr>
			<th style="width: 150px">Racine de Copix</th>
			<td><?php echo $ppo->filesPath ?></td>
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
		foreach ($ppo->files as $file) {
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

<br />
<?php _eTag ('back', array ('url' => 'backup||restore')) ?>