<form action="<?php echo _url ('admin|grouppreferences|save') ?>" method="POST">
<input type="hidden" name="groupName" value="<?php echo $ppo->groupName ?>" />
<input type="hidden" name="grouphandler" value="<?php echo $ppo->grouphandler ?>" />
<input type="hidden" name="modulePref" value="<?php echo $ppo->modulePref ?>" />
<?php
$isFirst = true;
$inputs = array ();
foreach ($ppo->preferences as $group) {
	_eTag ('beginblock', array ('title' => $group->getCaption (), 'isFirst' => $isFirst));
	?>
	<table class="CopixTable">
		<tr>
			<th><?php echo _i18n ('grouppreferences.header.name') ?></th>
			<th style="width: 35%"><?php echo _i18n ('grouppreferences.header.defaultValue') ?></th>
			<th style="width: 35%"><?php echo _i18n ('grouppreferences.header.value') ?></th>
			<th class="last"></th>
		</tr>
		<?php foreach ($group->getList () as $preference) { ?>
			<tr <?php _eTag ('trclass', array ('id' => $group->getId ())) ?>>
				<td><?php echo $preference->getCaption () ?></td>
				<td><?php echo $preference->getDefaultValue (true) ?></td>
				<td>
					<?php
					$inputName = 'pref_' . $preference->getName ();
					$inputs[] = $inputName;
					$type = $preference->getType ();
					if ($type == 'bool') {
						_eTag ('radiobutton', array ('name' => $inputName, 'values' => array (1 => 'Oui', 0 => 'Non'), 'selected' => $preference->value));
					} else if ($type == 'select') {
						_eTag ('select', array ('id' => $inputName, 'name' => $inputName, 'emptyShow' => false, 'values' => $preference->getListValues (), 'selected' => $preference->value));
					} else {
						_eTag ('inputtext', array ('name' => $inputName, 'value' => (isset ($preference->value) ? $preference->value : null)));
					}
					?>
				</td>
				<td class="action">
					<?php
					$content = '<strong>' . _i18n ('grouppreferences.name') . ' :</strong> ' .$preference->getName () . '<br />';
					$content .= '<strong>' . _i18n ('grouppreferences.type') . ' :</strong> ' . $type;
					if ($preference->getDescription () != null) {
						$content .= '<br /><strong>' . _i18n ('grouppreferences.description') . ' :</strong><br />' . $preference->getDescription ();
					}
					_eTag ('popupinformation', array ('max-width' => '600'), $content);
					?>
				</td>
			</tr>
			<?php
		}
		$isFirst = false;
	?>
	</table>
	<?php
	_eTag ('endblock');
}
?>

<br />
<table style="width: 100%">
	<tr>
		<td style="width: 33%"></td>
		<td style="width: 33%; text-align: center"><?php _eTag ('button', array ('action' => 'valid', 'value' => _i18n ('groupreferences.valid'))) ?></td>
		<td style="text-align: right"><?php _eTag ('back', array ('url' => _url ('admin|grouppreferences|modules', array ('groupName' => $ppo->groupName, 'grouphandler' => $ppo->grouphandler)))) ?></td>
	</tr>
</table>
</form>