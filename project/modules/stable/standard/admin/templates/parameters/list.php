<h2 class="first">Copix</h2>
<table class="CopixTable">
	<tr>
		<th style="width: 18px"></th>
		<th style="width: 130px"><?php echo _i18n ('params.header.config') ?></th>
		<th><?php echo _i18n ('params.header.description') ?></th>
		<th></th>
	</tr>
	<tr <?php _eTag ('trclass') ?>>
		<td style="text-align: center"><img src="<?php echo _resource ('img/copix.ico') ?>" /></td>
		<td><a href="<?php echo _url ('admin|parameters|copix') ?>"><?php echo _i18n ('params.frameworkConfig') ?></a></td>
		<td><?php echo _i18n ('params.frameworkConfigDescription') ?></td>
		<td class="action">
			<a href="<?php echo _url ('admin|parameters|copix') ?>"
				><img src="<?php echo _resource ('img/tools/config.png') ?>" alt="<?php echo _i18n ('params.alt.config') ?>" title="<?php echo _i18n ('params.alt.config') ?>"
			/></a>
		</td>
	</tr>
	<tr <?php _eTag ('trclass') ?>>
		<td style="text-align: center"><img src="<?php echo _resource ('admin|img/icon/webserver.png') ?>" /></td>
		<td><a href="<?php echo _url ('admin|parameters|webserver') ?>"><?php echo _i18n ('params.webserver') ?></a></td>
		<td><?php echo _i18n ('params.webserverDescription') ?></td>
		<td class="action">
			<a href="<?php echo _url ('admin|parameters|webserver') ?>"
				><img src="<?php echo _resource ('img/tools/config.png') ?>" alt="<?php echo _i18n ('params.alt.config') ?>" title="<?php echo _i18n ('params.alt.config') ?>"
			/></a>
		</td>
	</tr>
	<tr <?php _eTag ('trclass') ?>>
		<td style="text-align: center"><img src="<?php echo _resource ('admin|img/icon/dbserver.png') ?>" /></td>
		<td><a href="<?php echo _url ('admin|parameters|dbserver') ?>"><?php echo _i18n ('params.dbserver') ?></a></td>
		<td><?php echo _i18n ('params.dbserverDescription') ?></td>
		<td class="action">
			<a href="<?php echo _url ('admin|parameters|dbserver') ?>"
				><img src="<?php echo _resource ('img/tools/config.png') ?>" alt="<?php echo _i18n ('params.alt.config') ?>" title="<?php echo _i18n ('params.alt.config') ?>"
			/></a>
		</td>
	</tr>
</table>

<?php foreach ($ppo->modules as $groupId => $infos) { ?>
	<h2><?php echo $infos['caption'] ?></h2>
	<table class="CopixTable">
		<tr>
			<th style="width: 18px"></th>
			<th style="width: 130px"><?php echo _i18n ('params.header.module') ?></th>
			<th><?php echo _i18n ('params.header.description') ?></th>
			<th></th>
		</tr>
		<?php foreach ($infos['modules'] as $module) { ?>
			<tr <?php _eTag ('trclass', array ('id' => 'modules', 'highlight' => ($module->getName () == $ppo->highlight))) ?>>
				<td style="text-align: center">
					<?php if ($module->getIcon () != null) { ?>
						<img src="<?php echo $module->getIcon () ?>" />
					<?php } ?>
				</td>
				<td><a href="<?php echo _url ('admin|parameters|edit', array ('choiceModule' => $module->getName ())) ?>"><?php echo $module->getName () ?></a></td>
				<td><?php echo $module->getDescription () ?></td>
				<td class="action">
					<a href="<?php echo _url ('admin|parameters|edit', array ('choiceModule' => $module->getName ())) ?>"
						><img src="<?php echo _resource ('img/tools/config.png') ?>" alt="<?php echo _i18n ('params.alt.config') ?>" title="<?php echo _i18n ('params.alt.config') ?>"
					/></a>
				</td>
			</tr>
		<?php } ?>
	</table>
<?php } ?>
<br />

<?php _eTag ('back', array ('url' => 'admin||')) ?>