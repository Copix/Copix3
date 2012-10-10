<?php _eTag ('error', array ('message' => $ppo->errors)) ?>

<form action="<?php echo _url ('admin|parameters|save') ?>" method="POST">
<input type="hidden" name="choiceModule" value="<?php echo $ppo->choiceModule ?>" />
<input type="hidden" name="errorsId" value="<?php echo $ppo->errorsId ?>" />
<?php
$isFirst = true;
$exGroup = null;
foreach ($ppo->params as $name => $param) {
	if ($param['Group'] != $exGroup) {
		$class = ' class="first"';
		if (!$isFirst) {
			echo '</table>';
			$class = null;
		}
		?>
		<h2<?php echo $class ?>><?php echo $param['Group'] ?></h2>
		<table class="CopixTable">
			<tr>
				<th><?php echo _i18n ('params.paramsName') ?></th>
				<th style="width: 35%"><?php echo _i18n ('params.paramsDefault') ?></th>
				<th style="width: 35%"><?php echo _i18n ('params.paramsCurrentValue') ?></th>
				<th></th>
			</tr>
		<?php
		$exGroup = $param['Group'];
	}
	?>
	<tr <?php _eTag ('trclass', array ('id' => $exGroup)) ?>>
		<td><?php echo $param['Caption'] ?></td>
		<td><?php echo $param['DefaultStr'] ?></td>
		<td>
			<?php
			$inputName = 'param_' . $name;
			if ($param['Type'] == 'bool') {
				_eTag ('radiobutton', array ('name' => $inputName, 'values' => array (1 => 'Oui', 0 => 'Non'), 'selected' => $param['Value']));
			} else if ($param['Type'] == 'select' || $param['Type'] == 'multiSelect') {
				$listValues = explode (';', $param['ListValues']);
				$values = array ();
				foreach ($listValues as $listValue) {
					list ($key, $value) = explode ('=>', $listValue);
					$values[$key] = $value;
				}
				$extra = ($param['Type'] == 'multiSelect') ? 'size="3"' : null;
				_eTag ('select', array ('name' => $inputName, 'emptyShow' => false, 'values' => $values, 'selected' => $param['Value'], 'extra' => $extra));
			} else { ?>
				<input type="text" name="<?php echo $inputName ?>" value="<?php echo $param['Value'] ?>" style="width: 98%" class="inputText" />
			<?php } ?>
		</td>
		<td class="action">
			<?php
			$content = '<strong>' . _i18n ('params.name') . ' :</strong> ' .$name . '<br />';
			$content .= '<strong>' . _i18n ('params.type') . ' :</strong> ' . $param['Type'];
			if ($param['Description'] != null) {
				$content .= '<br /><strong>' . _i18n ('params.description') . ' :</strong><br />' . $param['Description'];
			}
			_eTag ('popupinformation', array ('max-width' => '600'), $content);
			?>
		</td>
	</tr>
	<?php
	$isFirst = false;
}
?>
</table>

<br />
<center><input type="submit" class="inputSubmit" value="<?php echo _i18n ('params.valid') ?>" /></center>
</form>

<br />
<?php _eTag ('back', array ('url' => 'admin|parameters|')) ?>