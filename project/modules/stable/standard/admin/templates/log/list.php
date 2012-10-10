<?php
// pour afficher une fois les profils actifs, et une fois les inactifs, petite astuce
$boucle = array (true, false);
foreach ($boucle as $isEnabled) {
	if ($isEnabled) {
		$title = _i18n ('logs.title.profilesEnabled');
		$icon = 'enable.png';
		$iconTitle = _i18n ('logs.alt.disableProfile');
		$action = 'disable';
	} else {
		$title = _i18n ('logs.title.profilesDisabled');
		$icon = 'disable.png';
		$iconTitle = _i18n ('logs.alt.enableProfile');
		$action = 'enable';
	}

	_eTag ('beginblock', array ('title' => $title, 'isFirst' => $isEnabled));
	?>
	<table class="CopixTable">
		<tr>
			<th style="width: 25%"><?php _eTag ('order', array ('captioni18n' => 'logs.header.profile', 'order' => 'profile', 'value' => $ppo->order)) ?></th>
			<th><?php _eTag ('order', array ('captioni18n' => 'logs.header.types', 'order' => 'type', 'value' => $ppo->order)) ?></th>
			<th style="width: 22%"><?php _eTag ('order', array ('captioni18n' => 'logs.header.strategy', 'order' => 'strategy', 'value' => $ppo->order)) ?></th>
			<th style="width: 8%"><?php _eTag ('order', array ('captioni18n' => 'logs.header.count', 'order' => 'count', 'value' => $ppo->order)) ?></th>
			<th style="width: 8%"><?php _eTag ('order', array ('captioni18n' => 'logs.header.size', 'order' => 'size', 'value' => $ppo->order)) ?></th>
			<th colspan="4" class="last"></th>
		</tr>
		<?php
		foreach ($ppo->profiles as $profile) {
			if ($profile['enabled'] == $isEnabled) {
				?>				
				<tr <?php _eTag ('trclass', array ('highlight' => ($ppo->highlight == $profile['name']))) ?>>
					<td><a href="<?php echo _url ('admin|log|edit', array ('profile' => $profile['name'])) ?>"><?php echo $profile['name'] ?></a></td>
					<td>
						<?php
						if (is_array ($profile->handle)) {
							$types = array ();
							foreach ($profile->handle as $handle) {
								if (array_key_exists ($handle, $ppo->types)) {
									$types[] = _tag ('popupinformation', array ('text' => $handle, 'displayimg' => false), $ppo->types[$handle]->getCaption ());
								} else {
									$types[] = $handle;
								}
							}
							echo implode (', ', $types);
						} else {
							echo $profile->handle;
						}
						?>
					</td>
					<td><?php echo $profile['strategyCaption'] ?></td>
					<td style="text-align: right"><?php echo $profile['count'] ?></td>
					<td style="text-align: right"><?php echo $profile['size'] ?></td>
					<td class="action">
						<?php if ($profile['isEditable']) { ?>
							<a href="<?php echo _url ('admin|log|edit', array ('profile' => $profile['name'])) ?>"
								><img src="<?php echo _resource ('img/tools/update.png') ?>" alt="<?php echo _i18n ('logs.alt.editProfile') ?>" title="<?php echo _i18n ('logs.alt.editProfile') ?>"
							/></a>
						<?php } ?>
					</td>
					<td class="action">
						<?php if ($profile['isReadable']) { ?>
							<a href="<?php echo _url ('admin|log|show', array ('profile' => $profile['name'])) ?>"
								><img src="<?php echo _resource ('img/tools/show.png') ?>" alt="<?php echo _i18n ('logs.alt.show') ?>" title="<?php echo _i18n ('logs.alt.show') ?>"
							/></a>
						<?php } ?>
					</td>
					<td class="action">
						<a href="<?php echo _url ('admin|log|' . $action, array ('profile' => $profile['name'])) ?>"
							><img src="<?php echo _resource ('img/tools/' . $icon) ?>" alt="<?php echo $iconTitle ?>" title="<?php echo $iconTitle ?>"
						/></a>
					</td>
					<td class="action">
						<a href="<?php echo _url ('admin|log|delete', array ('profile' => $profile['name'])) ?>"
							><img src="<?php echo _resource ('img/tools/delete.png') ?>" alt="<?php echo _i18n ('logs.alt.deleteProfile') ?>" title="<?php echo _i18n ('logs.alt.deleteProfile') ?>"
						/></a>
					</td>
				</tr>
				<?php
			}
		}
		?>
	</table>
	<br />

	<?php if (!$isEnabled) { ?>
		<table style="width: 100%">
			<tr>
				<td>
					<a href="<?php echo _url ('admin|log|edit') ?>"
						><img src="<?php echo _resource ('img/tools/add.png') ?>" alt="<?php echo _i18n ('logs.alt.addProfile') ?>" title="<?php echo _i18n ('logs.alt.addProfile') ?>"
					/> <?php echo _i18n ('logs.action.addProfile') ?></a>
				</td>
				<td style="text-align: right"><?php _eTag ('back', array ('url' => 'admin||')); ?></td>
			</tr>
		</table>
	<?php } else { ?>
		<a href="<?php echo _url ('admin|log|edit') ?>"
			><img src="<?php echo _resource ('img/tools/add.png') ?>" alt="<?php echo _i18n ('logs.alt.addProfile') ?>" title="<?php echo _i18n ('logs.alt.addProfile') ?>"
		/> <?php echo _i18n ('logs.action.addProfile') ?></a>
		<?php
	}

	_eTag ('endblock');
}
?>