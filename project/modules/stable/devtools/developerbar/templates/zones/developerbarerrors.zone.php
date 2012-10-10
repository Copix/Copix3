<table class="CopixTable">
	<tr>
		<th></th>
		<th>Erreur</th>
	</tr>
	<?php
	$alternate = null;
	foreach ($values as $error) {
		$alternate = ($alternate == null) ? 'class="alternate"' : null;
		?>
		<tr <?php echo $alternate ?>>
			<td>
				<?php
				$content = '<table class="CopixVerticalTable">';
				$alternateExtras = null;
				foreach ($error['extras'] as $name => $value) {
					$alternateExtras = ($alternateExtras == null) ? 'class="alternate"' : null;
					$content .= '<tr ' . $alternateExtras . '><th>' . $name . '</th><td>' . $value . '</td></tr>';
				}
				$content .= '</table>';
				_eTag ('popupinformation', array ('img' => _resource ('developerbar|icons/information.png')), $content);
				?>
			</td>
			<td><?php echo $error['message'] ?></td>
		</tr>
	<?php } ?>
</table>