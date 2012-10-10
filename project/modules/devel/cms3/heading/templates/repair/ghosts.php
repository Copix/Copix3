<?php
function _getInfosContent ($pObject) {
	$toReturn = '<table class="CopixTable"><tr><th style="width: 150px">Propriété</th><th class="last">Valeur</th></tr>';
	foreach ($pObject as $name => $value) {
		$toReturn .= '<tr ' . _tag ('trclass') . '><td>' . $name . '</td><td>' . htmlentities ($value, ENT_COMPAT, 'UTF-8') . '</td></tr>';
	}
	$toReturn .= '</table>';
	return $toReturn;
}

$iconLog = _resource ('admin|img/icon/log.png');
$iconDelete = _resource ('img/tools/delete.png');

foreach ($ppo->ghosts as $type => $categories) {
	_eTag ('beginblock', array ('title' => $type));

	if (count ($categories['ghosts']['specific']) > 0) {
		?>
		<h3>Eléments uniquement dans la table spécifique</h3>
		<table class="CopixTable">
			<tr>
				<th style="width: 100px">id_helt</th>
				<th>public_id_hei</th>
				<th class="last" colspan="3"></th>
			</tr>
			<?php
			$id = $categories['id'];
			foreach ($categories['ghosts']['specific'] as $ghost) {
				?>
				<tr <?php _eTag ('trclass', array ('id' => $type . 'specific')) ?>>
					<td><?php echo $ghost->$id ?></td>
					<td><?php echo $ghost->public_id_hei ?></td>
					<td class="action"><?php _eTag ('popupinformation', array ('handler' => 'clickdelay'), _getInfosContent ($ghost)) ?></td>
					<td class="action">
						<a href="<?php echo _url ('heading|actionslogs|', array ('id_helt' => $ghost->$id, 'type_hei' => $type)) ?>"><img src="<?php echo $iconLog ?>" title="Actions sur l'élément" /></a>
					</td>
					<td class="action">
						<a href="<?php echo _url ('heading|repair|DeleteGhost', array ('id_helt' => $ghost->$id, 'type_hei' => $type)) ?>"><img src="<?php echo $iconDelete ?>" title="Supprimer" /></a>
					</td>
				</tr>
			<?php } ?>
		</table>
	<?php } ?>
	<?php if (count ($categories['ghosts']['general']) > 0) { ?>
		<h3>Eléments uniquement dans la table générale (cms_headingelementinformations)</h3>
		<table class="CopixTable">
			<tr>
				<th style="width: 100px">id_helt</th>
				<th style="width: 100px">public_id_hei</th>
				<th >caption_hei</th>
				<th class="last" colspan="3"></th>
			</tr>
			<?php foreach ($categories['ghosts']['general'] as $ghost) { ?>
				<tr <?php _eTag ('trclass', array ('id' => $type . 'general')) ?>>
					<td><?php echo $ghost->id_helt ?></td>
					<td><?php echo $ghost->public_id_hei ?></td>
					<td><?php echo $ghost->caption_hei ?></td>
					<td class="action"><?php _eTag ('popupinformation', array ('handler' => 'clickdelay'), _getInfosContent ($ghost)) ?></td>
					<td class="action">
						<a href="<?php echo _url ('heading|actionslogs|', array ('public_id_hei' => $ghost->public_id_hei)) ?>"><img src="<?php echo $iconLog ?>" title="Actions sur l'élément" /></a>
					</td>
					<td class="action">
						<a href="<?php echo _url ('heading|repair|DeleteGhostHEI', array ('id_helt' => $ghost->id_helt, 'type_hei' => $ghost->type_hei)) ?>"><img src="<?php echo $iconDelete ?>" title="Supprimer" /></a>
					</td>
				</tr>
			<?php } ?>
		</table>
	<?php } ?>
	<?php _eTag ('endblock') ?>
<?php } ?>