<?php _eTag ('beginblock', array ('title' => 'Informations', 'isFirst' => true)) ?>
<table class="CopixVerticalTable">
	<tr <?php _eTag ('trclass') ?>>
		<th colspan="2">Répertoire</th>
		<td><?php echo $ppo->infos['path'] ?></td>
	</tr>
	<tr <?php _eTag ('trclass') ?>>
		<th colspan="2">Droits</th>
		<td><?php echo _filter ('Permissions')->get ($ppo->permissions) ?> (<?php echo _filter ('OctalPermissions')->get ($ppo->permissions) ?>)</td>
	</tr>
	<tr <?php _eTag ('trclass') ?>>
		<th style="width: 100px">Statut du lock</th>
		<th style="width: 1px">
			<?php
			$message = 'Le lock permet d\'interdire l\'accès en écriture au répertoire temporaire.';
			$message .= '<br />Ca évite que le même fichier soit généré en même temps par plusieurs instances de la même page.';
			_eTag ('popupinformation', array (), $message);
			?>
		</th>
		<td>
			<?php if ($ppo->infos['locked']) { ?>
				<span style="color: red">Ecriture bloquée depuis le <?php echo date (CopixI18N::getDateTimeFormat (), $ppo->infos['locked_since']) ?></span>
				<br />
				<?php _eTag ('button', array ('caption' => 'Débloquer l\'accès en écriture', 'img' => _resource ('img/tools/unlock.png'), 'url' => 'admin|temp|unlock')) ?>
			<?php } else { ?>
				<span style="color: green">Accès en écriture autorisé</span>
			<?php } ?>
		</td>
	</tr>
	<tr <?php _eTag ('trclass') ?>>
		<th class="last" colspan="2">Fichiers</th>
		<td id="td_files">
			<img src="<?php echo _resource ('img/tools/search.png') ?>" alt="Rechercher les fichiers" title="Rechercher les fichiers" style="cursor: pointer" onclick="javascript: getFiles ()" />
		</td>
	</tr>
</table>
<?php _eTag ('endblock') ?>

<?php _eTag ('beginblock', array ('title' => 'Suppression des fichiers temporaires')) ?>
<table style="margin-left: auto; margin-right: auto">
	<tr>
		<td style="width: 120px; text-align: center">
			<a href="<?php echo _url ('admin|temp|clear') ?>">
				<img src="<?php echo _resource ('admin|img/temp/clear.png') ?>" alt="Supprimer tous les fichiers temporaires" title="Supprimer tous les fichiers temporaires" />
				<br />
				Tout supprimer
			</a>
		</td>
		<td style="width: 120px; text-align: center">
			<a href="<?php echo _url ('admin|temp|clearModule') ?>">
				<img src="<?php echo _resource ('admin|img/temp/clearModule.png') ?>" alt="Supprimer les fichiers temporaires d'un module" title="Supprimer les fichiers temporaires d'un module" />
				<br />
				Par module
			</a>
		</td>
	</tr>
</table>
<?php _eTag ('endblock') ?>

<br />
<?php _eTag ('back', array ('url' => 'admin||')) ?>

<script type="text/javascript">
function getFiles () {
	Copix.setLoadingHTML ($ ('td_files'));
	new Request.HTML ({
		url: Copix.getActionURL ('admin|temp|getFiles'),
		update: $ ('td_files')
	}).send ();
}
</script>