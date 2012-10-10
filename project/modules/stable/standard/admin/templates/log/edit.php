<?php _eTag ('error', array ('message' => $ppo->errors)) ?>

<?php _eTag ('beginblock', array ('title' => _i18n ('logs.title.globalInfos'), 'isFirst' => true)) ?>

<form action="<?php echo _url ('admin|log|doEdit') ?>" method="POST">
<input type="hidden" name="mode" value="<?php echo $ppo->mode ?>" />
<input type="hidden" name="exName" value="<?php echo $ppo->profile['name'] ?>" />

<table class="CopixVerticalTable">
	<tr>
		<th style="width: 20%"><?php echo _i18n ('logs.header.profileName') ?></th>
		<td>
			<?php if ($ppo->mode == 'add') { ?>
				<?php _eTag ('inputtext', array ('name' => 'name', 'value' => $ppo->profile['name'])) ?>
			<?php } else { ?>
				<?php echo $ppo->profile['name'] ?>
				<input type="hidden" name="name" value="<?php echo $ppo->profile['name'] ?>" />
			<?php } ?>
	</tr>
	<tr class="alternate">
		<th><?php echo _i18n ('logs.header.enabled') ?></th>
		<td>
			<label><input type="radio" name="enabled" value="1" <?php if ($ppo->profile['enabled']) echo 'checked="checked"' ?> /> <?php echo _eTag ('i18n', array ('key' => 'logs.enabled.yes')) ?></label>
			<label><input type="radio" name="enabled" value="0" <?php if (!$ppo->profile['enabled']) echo 'checked="checked"' ?> /> <?php echo _eTag ('i18n', array ('key' => 'logs.enabled.no')) ?></label>
		</td>
	</tr>
	<tr>
		<th><?php echo _i18n ('logs.header.strategy') ?></th>
		<td>
			<select name="strategy" id="strategy" onchange="onChangeStrategy ()">
				<?php foreach ($ppo->strategies as $strategy) { ?>
					<option value="<?php echo $strategy->getId () ?>" <?php if ($ppo->profile['strategy'] == $strategy->getId ()) echo 'selected="selected"' ?>><?php echo $strategy->getCaption () ?></option>
				<?php } ?>
			</select>
			<span id="strategyDescription"></span>
	</tr>
	<tr class="alternate">
		<th><?php echo _i18n ('logs.header.types') ?></th>
		<td>
			<?php echo _i18n ('logs.helpAll') ?><br />
			<input type="hidden" name="handle" id="handle" />
			<div id="divHandles"></div>
			<select name="handlesSelect" id="handlesSelect">
				<?php
				foreach ($ppo->types as $name => $types) {
					echo '<optgroup label="' . $name . '">';
					foreach ($types as $type) {
						?>
						<option value="<?php echo $type->getId () ?>">[<?php echo $type->getId () ?>] <?php echo $type->getCaption () ?></option>
						<?php
					}
				}
				?>
				</optgroup>
			</select>
			<input type="text" id="handleText" size="15" class="inputText" />
			<img src="<?php echo _resource ('img/tools/add.png') ?>" style="cursor: pointer" onclick="addHandle ()" />
		</td>
	</tr>
	<tr>
		<th class="last"><?php echo _i18n ('logs.header.level') ?></th>
		<td>
			<?php
			foreach ($ppo->levels as $level) {
				$checked = (in_array ($level->id, $ppo->profile['level'])) ? 'checked="checked"' : null;
				?>
				<label><input type="checkbox" name="level[]" value="<?php echo $level->id ?>" <?php echo $checked ?> /> <?php echo $level->caption ?></label>
			<?php } ?>
		</td>
	</tr>
</table>

<div id="configEditor">
<?php _eTag ('beginblock', array ('title' => _i18n ('logs.title.configEditor'))) ?>
<div id="configEditorContent"><?php echo $ppo->configEditor ?></div>
<?php _eTag ('endblock') ?>
</div>

<?php _eTag ('endblock') ?>

<br />
<center><?php _eTag ('button', array ('action' => 'save')) ?></center>
</form>

<script type="text/javascript">
var strategiesDescriptions = new Array ();
<?php
foreach ($ppo->strategies as $strategy) {
	echo 'strategiesDescriptions[\'' . $strategy->getId () . '\'] = \'' . str_replace ("'", "\'", $strategy->getDescription ()) . '\';' . "\n";
}
?>

var types = new Array ();
<?php
foreach ($ppo->types as $types) {
	foreach ($types as $type) {
		echo 'types[\'' . $type->getId () . '\'] = \'' . str_replace ("'", "\'", $type->getCaption ()) . '\';' . "\n";
	}
}
?>

function onChangeStrategy () {
	$ ('strategyDescription').innerHTML = strategiesDescriptions[$ ('strategy').value];
	new Request.HTML ({
		'url' : '<?php echo _url ('admin|log|getConfigEditor') ?>',
		'update' : 'configEditorContent',
		'evalScripts' : true,
		'onComplete' : function (pContent) {
			$ ('configEditor').style.display = ($ ('configEditorContent').innerHTML != '') ? '' : 'none';
		}
	}).post ({'strategy' : $ ('strategy').value});
}

function addHandle (pHandle) {
	if (pHandle == undefined) {
		var handle = ($ ('handleText').value == '') ? $ ('handlesSelect').value : $ ('handleText').value;
	} else {
		var handle = pHandle;
	}
	$ ('handleText').value = '';
	html = '<div id="divHandles_' + handle + '">';
	html += '<img style="cursor: pointer" src="<?php echo _resource ('img/tools/delete.png') ?>" onclick="deleteHandle (\'' + handle + '\')" alt="Supprimer" title="Supprimer" />';
	html += ' ' + handle + '</div>';
	$ ('divHandles').innerHTML += html;
	$ ('handle').value += '|' + handle;
}

function deleteHandle (pHandle) {
	var handles = $ ('handle').value.split ('|');
	var newHandles = new Array ();
	for (x = 0; x < handles.length; x++) {
		if (handles[x] != '' && handles[x] != pHandle) {
			newHandles[newHandles.length] = handles[x];
		}
	}
	handles = $ ('handle').value = newHandles.join ('|');
	$ ('divHandles_' + pHandle).dispose ();
}

$ ('strategyDescription').innerHTML = strategiesDescriptions[$ ('strategy').value];
$ ('configEditor').style.display = ($ ('configEditorContent').innerHTML != '') ? '' : 'none';
<?php foreach ($ppo->profile['handle'] as $handle) { ?>
	addHandle ('<?php echo $handle ?>');
<?php } ?>
</script>

<?php if ($ppo->isReadable) { ?>
	<table style="width: 100%">
		<tr>
			<td><a href="<?php echo _url ('admin|log|show', array ('profile' => $ppo->profile['name'])) ?>"><img src="<?php echo _resource ('img/tools/show.png') ?>" /> <?php echo _i18n ('logs.action.show') ?></a></td>
			<td style="text-align: right"><?php _eTag ('back', array ('url' => 'admin|log|')); ?></td>
		</tr>
	</table>
<?php } else { ?>
	<?php _eTag ('back', array ('url' => 'admin|log|')); ?>
<?php } ?>