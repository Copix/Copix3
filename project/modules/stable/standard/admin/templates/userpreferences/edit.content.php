<form action="<?php echo _url ('admin|userpreferences|save') ?>" method="POST" id="<?php echo $ppo->uniqId ?>_form" <?php if ($ppo->ajaxSave) {?> onsubmit="return <?php echo $ppo->uniqId ?>_submit ();" <?php }?>>
<?php if (!$ppo->ajaxSave) { ?>
	<input type="hidden" name="user" value="<?php echo $ppo->user ?>" />
	<input type="hidden" name="userhandler" value="<?php echo $ppo->userhandler ?>" />
	<input type="hidden" name="modulePref" value="<?php echo $ppo->modulePref ?>" />
	<input type="hidden" name="confirmMessage" value="<?php echo $ppo->confirmMessage ?>" />
	<input type="hidden" name="redirect" value="<?php echo $ppo->redirect ?>" />
	<?php
}

if ($ppo->tabs) {
	$tabs = array ();
	foreach ($ppo->preferences as $group) {
		$tabs[$group->getId ()] = $group->getCaption ();
	}
	_eTag ('tabgroup', array ('tabs' => $tabs, 'default' => $ppo->defaultTab));
	echo '<br />';
}

$isFirst = true;
$inputs = array ();
foreach ($ppo->preferences as $group) {
	if (!$ppo->tabs && $ppo->showGroups) {
		?>
		<h2<?php if ($isFirst) { echo ' class="first"'; } ?>><?php echo $group->getCaption () ?></h2>
		<?php
	}
	?>
	<div id="<?php echo $group->getId () ?>">
	<table class="CopixTable" <?php if ($ppo->width != null) { echo 'style="width: ' . $ppo->width . 'px"'; } ?>>
		<tr>
			<th><?php echo _i18n ('userpreferences.header.name') ?></th>
			<th style="width: 35%"><?php echo _i18n ('userpreferences.header.defaultValue') ?></th>
			<th style="width: 35%"><?php echo _i18n ('userpreferences.header.value') ?></th>
			<th></th>
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
					} else { ?>
						<input type="text" name="<?php echo $inputName ?>" value="<?php echo (isset ($preference->value)) ? $preference->value : null ?>" style="width: 98%" class="inputText" id="<?php echo $inputName ?>" />
					<?php } ?>
				</td>
				<td class="action">
					<?php
					$content = '<strong>' . _i18n ('userpreferences.name') . ' :</strong> ' .$preference->getName () . '<br />';
					$content .= '<strong>' . _i18n ('userpreferences.type') . ' :</strong> ' . $type;
					if ($preference->getDescription () != null) {
						$content .= '<br /><strong>' . _i18n ('userpreferences.description') . ' :</strong><br />' . $preference->getDescription ();
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
	</div>
<?php } ?>

<br />
<center><input type="submit" class="inputSubmit" value="<?php echo _i18n ('userpreferences.valid') ?>" /></center>
</form>

<script type="text/javascript">
function <?php echo $ppo->uniqId ?>_submit () {
	<?php
	if ($ppo->ajaxSave) {
		?>
		var post = new Hash({
			'user' : '<?php echo $ppo->user ?>',
			'userhandler' : '<?php echo $ppo->userhandler ?>',
			'modulePref' : '<?php echo $ppo->modulePref ?>'
		});
		var hashes = $('<?php echo $ppo->uniqId ?>_form').set('send').toQueryString().split('&');
		var data = new Hash();
		for(var i = 0; i < hashes.length; i++)
	    {
	        var hash = hashes[i].split('=');
	        data.set(hash[0], hash[1]);
	    }
		post.combine(data);
		new Request.HTML ({
			url: '<?php echo _url ('admin|userpreferences|save') ?>'
		}).post (post);
		Copix.get_copixwindow ('<?php echo $ppo->uniqId ?>_window').close ();
		return false;
	<?php } else { ?>
		return true;
	<?php } ?>
}
</script>