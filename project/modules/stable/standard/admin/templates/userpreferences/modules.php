<?php
if (count ($ppo->modules) == 0) {
	echo _i18n ('userpreferences.noModule');
} else {
	$isFirst = true;
	foreach ($ppo->modules as $groupId => $infos) {
		?>
		<h2<?php if ($isFirst) { echo ' class="first"'; } ?>><?php echo $infos['caption'] ?></h2>
		<table class="CopixTable">
			<tr>
				<th style="width: 18px"></th>
				<th style="width: 130px"><?php echo _i18n ('userpreferences.header.module') ?></th>
				<th><?php echo _i18n ('userpreferences.header.description') ?></th>
				<th></th>
			</tr>
			<?php foreach ($infos['modules'] as $module) { ?>
				<tr <?php _eTag ('trclass', array ('id' => $infos['caption'], 'highlight' => ($module->getName () == $ppo->highlight))) ?>>
					<td style="text-align: center">
						<?php if ($module->getIcon () != null) { ?>
							<img src="<?php echo $module->getIcon () ?>" alt="<?php echo _i18n ('params.alt.config') ?>" />
						<?php } ?>
					</td>
					<td><a href="<?php echo _url ('admin|userpreferences|edit', array ('user' => $ppo->user, 'userhandler' => $ppo->userhandler, 'modulePref' => $module->getName ()), true) ?>"><?php echo $module->getName () ?></a></td>
					<td><?php echo $module->getDescription () ?></td>
					<td class="action">
						<a href="<?php echo _url ('admin|userpreferences|edit', array ('user' => $ppo->user, 'userhandler' => $ppo->userhandler, 'modulePref' => $module->getName ()), true) ?>"
							><img src="<?php echo _resource ('img/tools/config.png') ?>" alt="<?php echo _i18n ('params.alt.config') ?>" title="<?php echo _i18n ('params.alt.config') ?>"
						/></a>
					</td>
				</tr>
			<?php } ?>
		</table>
		<?php
		$isFirst = false;
	}
}
?>

<br />
<?php _eTag ('back', array ('url' => _url ('admin|userpreferences|'))) ?>