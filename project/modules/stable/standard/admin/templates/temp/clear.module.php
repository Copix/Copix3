<form action="<?php echo _url ('admin|temp|doClearModule') ?>" method="POST">
<?php _eTag ('beginblock', array ('title' => 'Fichiers temporaires par module', 'isFirst' => true)) ?>
<table class="CopixVerticalTable">
	<tr>
		<th style="width: 100px" class="last">Module</th>
		<td><?php _eTag ('select', array ('name' => 'moduleName', 'values' => $ppo->modules)) ?></td>
	</tr>
</table>
<?php _eTag ('endblock') ?>

<br />
<table style="width: 100%">
	<tr>
		<td style="width: 20%"></td>
		<td style="text-align: center">
			<?php _eTag ('button', array ('caption' => 'Supprimer les fichiers temporaires', 'img' => _resource ('img/tools/refresh.png'))) ?>
		</td>
		<td style="width: 20%"><?php _eTag ('back', array ('url' => 'admin|temp|')) ?></td>
	</tr>
</table>

</form>