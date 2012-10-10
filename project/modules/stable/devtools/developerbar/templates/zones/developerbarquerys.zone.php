<table class="CopixTable">
	<tr>
		<th></th>
		<th>Requête</th>
	</tr>
	<?php
	$alternate = null;
	foreach ($values as $query) {
		$alternate = ($alternate == null) ? 'class="alternate"' : null;
		?>
		<tr <?php echo $alternate ?>>
			<td>
				<?php
				$content = '<table class="CopixVerticalTable">';
				$alternateExtras = null;
				foreach ($query['extras'] as $name => $value) {
					$alternateExtras = ($alternateExtras == null) ? 'class="alternate"' : null;
					$content .= '<tr ' . $alternateExtras . '><th>' . $name . '</th><td>' . DeveloperBar::dump ($value, true) . '</td></tr>';
				}
				$content .= '</table>';
				_eTag ('popupinformation', array ('img' => _resource ('developerbar|icons/information.png')), $content);
				?>
			</td>
			<td><?php echo $query['message'] ?></td>
		</tr>
	<?php } ?>
</table>