<h2 class="first"><?php echo _i18n ('copix.dbProfile.database') ?></h2>
<?php if (! $ppo->configurationFileIsWritable){ ?>
}
<div class="errorMessage">
<h1><?php _etag ('i18n', 'copix:common.messages.error');?></h1>
<ul>
 <li><?php _etag ('i18n', array ('key'=>'install.databaseFileNotWritable', 'configurationFilePath'=>$ppo->configurationFilePath)); ?></li>
</ul>
</div>
<?php } ?>

<form action="<?php echo _url ('admin|database|validForm'); ?>" method="POST" class="bdd">
<table class="CopixTable">
<thead>
<tr>
<th></th>
<th><?php _etag ('i18n', 'install.database.connectionName'); ?></th>
<th><?php _etag ('i18n', 'install.database.driver'); ?></th>
<th><?php _etag ('i18n', 'install.database.connectionString'); ?></th>
<th><?php _etag ('i18n', 'install.database.user'); ?></th>
<th><?php _etag ('i18n', 'install.database.password'); ?></th>
<th></th>
</tr>
</thead>
	<tbody>
		<tr>
			<td><input type="radio" name="defaultRadio" value="nodefault"></td>
			<td colspan="6"><?php _etag ('i18N','install.database.nodefault'); ?></td>
		</tr>
		<?php
		    foreach ($ppo->connections as $postFix=>$configuredConnection){ 
		?>
		<tr <?php _eTag ('cycle', array ('values'=>'class="alternate",')); ?>>
		 <td><input type="radio" name="defaultRadio" <?php echo 'value="default'.$postFix.'"'; 
		 
		 if ($configuredConnection['default'] === true) {
		     echo " checked";
		 }
		 
		 ?>></td>
		 <td><input size="10" type="text" name="connectionName<?php echo $postFix; ?>" value="<?php echo $postFix; ?>" /></td>
		 <td><?php _etag ('select', array ('name'=>'driver'.$postFix, 'values'=>$ppo->drivers, 'selected'=>isset ($configuredConnection['driver']) ? $configuredConnection['driver'] : null)); ?></td>
		 <td><input type="text" value="<?php _etag ('escape', array ('value'=>$configuredConnection['connectionString'])); ?>"
				name="connectionString<?php echo $postFix; ?>" /></td>
		 <td><input type="text"
				value="<?php _etag ('escape', array ('value'=>$configuredConnection['user'])); ?>"
				name="user<?php echo $postFix; ?>" /></td>
		 <td><input type="password"
				value="<?php _etag ('escape', array ('value'=>$configuredConnection['password'])); ?>"
				name="password<?php echo $postFix; ?>" /></td>
		 <td><?php if ($configuredConnection['available']){$imgSrc = 'img/tools/tick.png'; $alt=_i18n ('copix:common.status.valid');}else{$imgSrc = 'img/tools/cross.png';$alt=_i18n ('copix:common.status.notValid');} ?>
		 	<img src="<?php echo _resource ($imgSrc); ?>"
				title="<?php echo $configuredConnection['errorNotAvailable']; ?>"
				alt="<?php echo $alt; ?>" />
			<?php if (!$configuredConnection['available']) {_etag ('popupinformation', array(), $configuredConnection['errorNotAvailable']);}?>
		 	</td>
		</tr>
		<?php 
        }
        ?>
		<tr <?php _eTag ('cycle', array ('values'=>'class="alternate",')); ?>>
			<td></td>
			<td><input size="10" type="text" name="connectionName" /></td>
			<td><?php _etag ('select', array ('name'=>'driver', 'values'=>$ppo->drivers)); ?></td>
			<td><input type="text" value="" name="connectionString" /></td>
			<td><input type="text" value="" name="user" /></td>
			<td><input type="password" value="" name="password" /></td>
			<td></td>
		</tr>
	</tbody>
</table>

<input type="submit" name="btn" value="<?php _etag ('i18n', 'install.database.test'); ?>" />
<?php if ($ppo->valid) { ?>
<span id="record">
<input type="submit" name="btn"	value="<?php _etag ('i18n', 'install.database.save'); ?>" />
</span>
<?php } ?>

</form>
<?php _eTag ('back', array ('url' => 'admin||')); ?>

<?php _etag('copixtips',array('warning'=>$ppo->importantTips,'tips'=>$ppo->tips,'titlei18n'=>'install.tips.title')); ?>

<?php 
_eTag('mootools');  
$jsCode = "

window.addEvent('domready', function () {
	$$('.bdd input,.bdd select').each(function (el) {
		el.addEvent('change',function () {
			$('record').setStyle('display','none');
		});
	});
});

";
CopixHTMLHeader::addJSCode($jsCode); 
?>