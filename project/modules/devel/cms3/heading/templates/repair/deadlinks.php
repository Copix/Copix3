<?php
function _getInfosContent ($pObject) {
	$toReturn = '<table class="CopixTable"><tr><th style="width: 150px">Propriété</th><th class="last">Valeur</th></tr>';
	foreach ($pObject as $name => $value) {
		if (!is_object ($value)) {
			$toReturn .= '<tr ' . _tag ('trclass') . '><td>' . $name . '</td><td>' . htmlentities ($value, ENT_COMPAT, 'UTF-8') . '</td></tr>';
		}
	}
	$toReturn .= '</table>';
	return $toReturn;
}

$iconeHeading = '<img src="' . _resource ('heading|img/headings.png') . '" width="16px" height="16px" />';

foreach ($ppo->deadLinks as $type => $links) {
	_eTag ('beginblock', array ('title' => $type));
	?>
	<table class="CopixTable">
		<tr>
			<th style="width: 60px">public_id</th>
			<th style="width: 100px">linked_public_id</th>
			<th style="width: 180px">Nom</th>
			<th>Erreur</th>
			<th class="last" colspan="3"></th>
		</tr>
		<?php foreach ($links as $link) { ?>
			<tr <?php _eTag ('trclass') ?>>
				<td><?php echo $link['element']->public_id_hei ?></td>
				<td><?php echo $link['linked_public_id_hei'] ?></td>
				<td><?php echo $link['element']->caption_hei ?></td>
				<td><?php echo $link['error'] ?></td>
				<td class="action"><?php _eTag ('popupinformation', array ('handler' => 'clickdelay'), _getInfosContent ($link['element'])) ?></td>
				<td class="action">
					<a href="<?php echo _url ('heading|element|', array ('heading' => $link['element']->parent_heading_public_id_hei, 'selected' => array ($link['element']->id_helt . '|' . $link['element']->type_hei))) ?>"><img src="<?php echo _resource ('heading|img/headings.png') ?>" width="16px" height="16px" /></a>
				</td>
				<td class="action">
					<a href="<?php echo _url ('heading|element|prepareEdit', array ('type' => $type, 'id' => $link['element']->id_helt, 'heading' => $link['element']->parent_heading_public_id_hei)) ?>"><img src="<?php echo _resource ('img/tools/update.png') ?>" /></a>
				</td>
			</tr>
		<?php } ?>
	</table>
	<?php _eTag ('endblock'); ?>
<?php } ?>