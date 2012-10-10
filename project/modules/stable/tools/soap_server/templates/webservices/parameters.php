<?php if (count ($ppo->parameters) == 0) { ?>
	Aucun paramètre.
<?php } else { ?>
	<form id="formParameters">
	<table class="CopixTable">
		<tr>
			<th style="width: 110px">Nom</th>
			<th style="width: 40px">Type</th>
			<th>Valeur</th>
		</tr>
		<?php
		$alternate = 'temp';
		foreach ($ppo->parameters as $name => $type) {
			$alternate = ($alternate == null) ? $alternate = ' class="alternate"' : null;
			?>
			<tr<?php echo $alternate ?>>
				<td><?php echo $name ?></td>
				<td><?php echo $type ?></td>
				<td style="text-align: center"><textarea rows="5" name="parameters[]" style="width: 98%"></textarea></td>
			</tr>
		<?php } ?>
	</table>
	</form>
<?php } ?>