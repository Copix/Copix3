<?php
if (count ($ppo->modules) == 0) {
	echo _i18n ('grouppreferences.noModule');
} else {
	$isFirst = true;
	foreach ($ppo->modules as $groupId => $infos) {
		_eTag ('beginblock', array ('title' => $infos['caption'], 'isFirst' => $isFirst));
		?>
		<table class="CopixTable">
			<tr>
				<th style="width: 18px"></th>
				<th style="width: 130px"><?php echo _i18n ('grouppreferences.header.module') ?></th>
				<th><?php echo _i18n ('grouppreferences.header.description') ?></th>
				<th class="last"></th>
			</tr>
			<?php foreach ($infos['modules'] as $module) { ?>
				<tr <?php _eTag ('trclass', array ('id' => $infos['caption'], 'highlight' => $module->getName ())) ?>>
					<td style="text-align: center">
						<?php if ($module->getIcon () != null) { ?>
							<img src="<?php echo $module->getIcon () ?>" alt="<?php echo _i18n ('params.alt.config') ?>" />
						<?php } ?>
					</td>
					<td><a href="<?php echo _url ('admin|grouppreferences|edit', array ('groupName' => $ppo->groupName, 'grouphandler' => $ppo->grouphandler, 'modulePref' => $module->getName ()), true) ?>"><?php echo $module->getName () ?></a></td>
					<td><?php echo $module->getDescription () ?></td>
					<td class="action">
						<a href="<?php echo _url ('admin|grouppreferences|edit', array ('groupName' => $ppo->groupName, 'grouphandler' => $ppo->grouphandler, 'modulePref' => $module->getName ()), true) ?>"
							><img src="<?php echo _resource ('img/tools/config.png') ?>" alt="<?php echo _i18n ('params.alt.config') ?>" title="<?php echo _i18n ('params.alt.config') ?>"
						/></a>
					</td>
				</tr>
			<?php } ?>
		</table>
		<?php
		_eTag ('endblock');
		$isFirst = false;
	}
}
?>

<br />
<?php _eTag ('back', array ('url' => _url ('admin|grouppreferences|'))) ?>