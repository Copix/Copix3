<?php
// pour afficher une fois les profils actifs, et une fois les inactifs, petite astuce
$boucle = array (true, false);
foreach ($boucle as $isEnabled) {
	if ($isEnabled) {
		$class = ' class="first"';
		$title = _i18n ('plugin.title.pluginsEnabled');
		$icon = 'enable.png';
		$iconTitle = _i18n ('plugin.alt.disablePlugin');
		$action = 'disable';
	} else {
		$class = null;
		$title = _i18n ('plugin.title.pluginsDisabled');
		$icon = 'disable.png';
		$iconTitle = _i18n ('plugin.alt.enablePlugin');
		$action = 'enable';
	}
	?>
	<h2<?php echo $class ?>><?php echo $title ?></h2>
	<table class="CopixTable">
		<tr>
			<th style="width: 18px"></th>
			<th style="width: 150px"><?php _eTag ('order', array ('captioni18n' => 'plugin.header.plugin', 'order' => 'id', 'value' => $ppo->order)) ?></th>
			<th style="width: 150px"><?php _eTag ('order', array ('captioni18n' => 'plugin.header.module', 'order' => 'module', 'value' => $ppo->order)) ?></th>
			<th><?php _eTag ('order', array ('captioni18n' => 'plugin.header.caption', 'order' => 'caption', 'value' => $ppo->order)) ?></th>
			<th colspan="3"></th>
		</tr>
		<?php
		foreach ($ppo->plugins as $plugin) {
			if ($plugin->isRegistered () == $isEnabled) {
				?>
				<tr <?php _eTag ('trclass', array ('id' => $isEnabled, 'highlight' => ($plugin->getName () == $ppo->highlight))) ?>>
					<td style="text-align: center">
						<?php
						$moduleInfos = CopixModule::getInformations ($plugin->getModule ());
						if ($moduleInfos->getIcon () != null) {
							?><img src="<?php echo $moduleInfos->getIcon () ?>" /><?php
						}
						?>
					</td>
					<td><a href="<?php echo _url ('admin|plugin|informations', array ('plugin' => $plugin->getName ())) ?>"><?php echo $plugin->getId () ?></a></td>
					<td><?php echo $plugin->getModule () ?></td>
					<td><?php echo $plugin->getCaption () ?></td>
					<td class="action">
						<a href="<?php echo _url ('admin|plugin|informations', array ('plugin' => $plugin->getName ())) ?>"
							><img src="<?php echo _resource ('img/tools/select.png') ?>" alt="<?php echo _i18n ('plugin.configure') ?>" title="<?php echo _i18n ('plugin.configure') ?>"
						/></a>
					</td>
					<td class="action">
						<a href="<?php echo _url ('admin|plugin|' . $action, array ('plugin' => $plugin->getName ())) ?>"
							><img src="<?php echo _resource ('img/tools/' . $icon) ?>" alt="<?php echo $iconTitle ?>" title="<?php echo $iconTitle ?>"
						/></a>
					</td>
					<td class="action">
						<?php
						$content = '<strong>' . _i18n ('plugin.name') . ' :</strong> ' . $plugin->getName ();
						if ($plugin->getDescription () != null) {
							$content .= '<br />';
							$content .= '<strong>' . _i18n ('plugin.description') . ' :</strong><br />' . $plugin->getDescription ();
						}
						_eTag ('popupinformation', array ('width' => '300px'), $content);
						?>
					</td>
				</tr>
				<?php
			}
		}
		?>
	</table>
	<?php
}

echo '<br />';
_eTag ('back', array ('url' => 'admin||'));
?>