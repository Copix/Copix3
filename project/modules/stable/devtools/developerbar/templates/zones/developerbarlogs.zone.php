<table class="CopixTable">
	<tr>
		<th></th>
		<th>Type</th>
		<th>Message</th>
	</tr>
	<?php
	$alternate = null;
	foreach ($values as $log) {
		$alternate = ($alternate == null) ? 'class="alternate"' : null;
		?>
		<tr <?php echo $alternate ?>>
			<td>
				<?php
				$content = '<table class="CopixVerticalTable">';
				$content .= '<tr><th>Niveau</th><td>' . CopixLog::getLevel ($log['level']) . '</td></tr>';
				$alternateExtras = null;
				foreach ($log['extras'] as $name => $value) {
					$alternateExtras = ($alternateExtras == null) ? 'class="alternate"' : null;
					$content .= '<tr ' . $alternateExtras . '><th>' . $name . '</th><td>' . $value . '</td></tr>';
				}
				$content .= '</table>';
				_eTag ('popupinformation', array ('img' => _resource ('developerbar|icons/information.png')), $content);
				?>
			</td>
			<td><?php echo $log['type'] ?></td>
			<td><?php echo $log['message'] ?></td>
		</tr>
	<?php } ?>
</table>