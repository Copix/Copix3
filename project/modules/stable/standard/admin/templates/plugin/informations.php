<h2 class="first"><?php echo _i18n ('plugin.title.plugin') ?></h2>
<table class="CopixVerticalTable">
	<tr class="alternate">
		<th style="width: 100px"><?php echo _i18n ('plugin.header.module') ?></th>
		<td><?php echo $ppo->plugin->getModule () ?></td>
	</tr>
	<tr>
		<th><?php echo _i18n ('plugin.header.path') ?></th>
		<td><?php echo $ppo->plugin->getPath () ?></td>
	</tr>
	<tr class="alternate">
		<th><?php echo _i18n ('plugin.header.configPath') ?></th>
		<td><?php echo $ppo->plugin->getConfigPath () ?></td>
	</tr>
	<tr>
		<th><?php echo _i18n ('plugin.header.caption') ?></th>
		<td><?php echo $ppo->plugin->getCaption () ?></td>
	</tr>
	<tr class="alternate">
		<th><?php echo _i18n ('plugin.header.description') ?></th>
		<td><?php echo $ppo->plugin->getDescription () ?></td>
	</tr>
</table>

<h2><?php echo _i18n ('plugin.title.configuration') ?></h2>
<?php
if (count ($ppo->config) == 0) {
	echo _i18n ('plugin.noConfig');
} else { ?>
	}
	<table class="CopixTable">
		<tr>
			<th style="width: 150px"><?php echo _i18n ('plugin.header.option') ?></th>
			<th><?php echo _i18n ('plugin.header.value') ?></th>
		</tr>
		<?php
		$alternate = null;
		foreach ($ppo->config as $name => $value) {
			$alternate = ($alternate == null) ? 'class="alternate"' : null;
			?>
			<tr <?php echo $alternate ?>>
				<td><?php echo $name ?></td>
				<td><?php echo CopixDebug::getDump ($value, true) ?></td>
			</tr>
		<?php } ?>
	</table>
<?php } ?>

<br />
<?php _eTag ('back', array ('url' => 'admin|plugin|')); ?>