<?php if($ppo->success){?>
<div class="success">
	<?php echo _i18n('wsdl2php.generation.success');?> :
	<ul>
		<?php foreach (preg_split ('/,/', $ppo->success) as $file){ ?>
		<li><?php echo $file?></li>
		<?php }?>
	</ul>
</div>
<?php }?>
<?php if($ppo->missing){?>
	<div class="error">
		<?php echo _i18n('wsdl2php.incorrect.value')?> :
		<ul>
			<?php foreach (preg_split ('/,/', $ppo->missing) as $miss){ ?>
			<li><?php echo $miss?></li>
			<?php }?>
		</ul>
	</div>
<?php }?>

<?php if($ppo->error){?>
<div class="error">
	<?php echo $ppo->error;?>
</div>
<?php }?>

<form method="post" action="<?php echo _url('validate')?>">
	<p>
		<label for="wsdl"><?php echo _i18n('wsdl2php.wsdl.url')?> : <input type="text" name="wsdl" value="<?php echo $ppo->wsdl?>"/></label>
	</p>
	<p>	
		<label for="module"><?php echo _i18n('wsdl2php.destination.module')?> : <?php _eTag('select', array('name' => 'classmodule', 'values' => $ppo->modules, 'selected' => $ppo->module,'emptyShow' => false))?></label>
	</p>
	<input type="submit">
	
</form>