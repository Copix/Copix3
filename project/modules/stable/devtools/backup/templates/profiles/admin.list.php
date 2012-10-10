<?php _eTag ('beginblock', array ('title' => 'Profils de sauvegarde', 'isFirst' => true)) ?>
<?php if (count ($ppo->profiles) == 0) { ?>
	Pas de profil de sauvegarde existant.
	<br />
<?php } else { ?>
	<table class="CopixTable">
		<tr>
			<th style="width: 300px">Nom</th>
			<th>Type</th>
			<th>Tables</th>
			<th>Fichiers</th>
			<th colspan="3"></th>
		</tr>
		<?php foreach ($ppo->profiles as $profile) { ?>
			<tr <?php _eTag ('trclass', array ('highlight' => $profile->getId () == $ppo->highlight)) ?>>
				<td><a href="<?php echo _url ('backup|profiles|edit', array ('profile' => $profile->getId ())) ?>"><?php echo $profile->getCaption () ?></a></td>
				<td><?php echo $profile->getType ()->getCaption () ?></td>
				<td><?php echo count ($profile->getTables ()) ?></td>
				<td><?php echo count ($profile->getFiles ()) ?></td>
				<td class="action">
					<a href="<?php echo _url ('backup|profiles|edit', array ('profile' => $profile->getId ())) ?>"
					   ><img src="<?php echo _resource ('img/tools/update.png') ?>" alt="Modifier" title="Modifier"
					/></a>
				</td>
				<td class="action">
					<a href="<?php echo _url ('backup||', array ('profile' => $profile->getId ())) ?>"
					   ><img src="<?php echo _resource ('backup|img/backup.png') ?>" alt="Lancer la sauvegarde" title="Lancer la sauvegarde"
					/></a>
				</td>
				<td class="action">
					<a href="<?php echo _url ('backup|profiles|delete', array ('profile' => $profile->getId ())) ?>"
					   ><img src="<?php echo _resource ('img/tools/delete.png') ?>" alt="supprimer" title="Supprimer"
					/></a>
				</td>
			</tr>
		<?php } ?>
	</table>
<?php } ?>
	
<?php _eTag ('endblock') ?>

<table style="width: 100%">
	<tr>
		<td>
			<a href="<?php echo _url ('backup|profiles|edit') ?>"
			   ><img src="<?php echo _resource ('img/tools/add.png') ?>" alt="Ajouter un profil" title="Ajouter un profil" /> Ajouter un profil de sauvegarde
			</a>
		</td>
		<td align="right"><?php _eTag ('back', array ('url' => 'admin||')) ?></td>
	</tr>
</table>