<h2 class="first">Fichiers de log</h2>
<table class="CopixTable">
	<tr>
		<th>Fichier</th>
		<th style="width: 60px">Taille</th>
		<th colspan="2"></th>
	</tr>
	<?php
	foreach ($ppo->files as $file) {
		$alternate = _tag ('cycle', array ('values' => ',class="alternate"'));
		if ($file->getId () == $ppo->highlight) {
			$alternate = 'class="highlight"';
		}
		?>
		<tr <?php echo $alternate ?>>
			<td><a href="<?php echo _url ('logreader||edit', array ('file' => $file->getId ())) ?>"><?php echo $file->getFileName () ?></a></td>
			<td><?php echo $file->getSize (true) ?></td>
			<td class="action">
				<a href="<?php echo _url ('logreader||show', array ('file' => $file->getId ())) ?>">
					<img title="Voir le contenu" alt="Voir le contenu" src="<?php echo _resource ('img/tools/show.png') ?>"/>
				</a>
			</td>
			<td class="action">
				<a href="<?php echo _url ('logreader||delete', array ('file' => $file->getId ())) ?>">
					<img title="Supprimer" alt="supprimer" src="<?php echo _resource ('img/tools/delete.png') ?>"/>
				</a>
			</td>
		</tr>
	<?php } ?>
</table>

<br />
<table style="width: 100%">
	<tr>
		<td>
			<a href="<?php echo _url ('logreader||edit') ?>">
				<img title="Ajouter un fichier de log" alt="Ajouter un fichier de log" src="<?php echo _resource ('img/tools/add.png') ?>"/>
				Ajouter un fichier de log
			</a>
		</td>
		<td><?php _eTag ('back', array ('url' => 'admin||')) ?></td>
	</tr>
</table>