<?php
$isFirst = true;
foreach ($ppo->groups as $handler => $groups) {
	_eTag ('beginblock', array ('title' => $handler, 'isFirst' => $isFirst));
	$isFirst = false;
	?>
	<table class="CopixTable">
		<tr>
			<th><?php echo _i18n ('grouppreferences.header.groupName') ?></th>
			<th class="last"></th>
		</tr>
		<?php foreach ($groups as $id => $name) { ?>
			<tr <?php _eTag ('trclass') ?>>
				<td><a href="<?php echo _url ('admin|grouppreferences|modules', array ('groupName' => $id, 'grouphandler' => $handler)) ?>"><?php echo $name ?></a></td>
				<td class="action">
					<a href="<?php echo _url ('admin|grouppreferences|deleteAll', array ('groupName' => $id, 'grouphandler' => $handler)) ?>"
						><img src="<?php echo _resource ('img/tools/delete.png') ?>" alt="<?php echo _i18n ('copix:common.buttons.delete') ?>" title="<?php echo _i18n ('copix:common.buttons.delete') ?>"
					/></a>
				</td>
			</tr>
		<?php } ?>
	</table>
	<?php
	_eTag ('endblock');
}

echo '<br />';
_eTag ('back', array ('url' => _url ('admin||')));
?>