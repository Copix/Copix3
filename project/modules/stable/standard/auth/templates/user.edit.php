<?php if (count ($ppo->errors)) { ?>
<div class="errorMessage">
 <h1><?php _etag ('i18n', 'copix:common.messages.error');?> </h1>
 <?php _etag ('ulli', array ('values'=>$ppo->errors)); ?>
</div>
<?php } ?>

<form action="<?php if($ppo->createInProcess){echo _url ("auth|usersregister|valid");} else { echo _url ("auth|users|valid");} ?>" method="post" enctype="multipart/form-data" >

<table class="CopixVerticalTable">
	<?php if (isset ($ppo->user->id_dbuser)) { ?>
	<tr> 
		<th><?php _etag ('i18n', 'auth.user.id'); ?></th>
		<td><?php echo $ppo->user->id_dbuser;  ?></td>
	</tr>
	<?php } ?>
	
	<tr>
		<th><label for="login_dbuser" ><?php _etag ('i18n', 'auth.user.login'); ?><span class="required" > *</span></label></th>
		<td><input type="text" id="login_dbuser" name="login_dbuser" value="<?php (isset ($ppo->user->login_dbuser)) ? _etag ('escape', $ppo->user->login_dbuser) : ''; ?>" /></td>
	</tr>

	<tr>
		<th>
			<label for="password_dbuser" >
				<?php _etag ('i18n', 'auth.user.password'); ?>
				<?php if ($ppo->createUser) { ?><span class="required" > *</span><?php } ?>
			</label>
		</th>
		<td>
			<input type="password" id="password_dbuser" name="password_dbuser" value="" />
			<script type="text/javascript">
				//<!--
				$('password_dbuser').setProperty ('autocomplete','off');
				//-->
			</script>
		</td>
	</tr>

	<tr>
		<th>
			<label for="password_confirmation_dbuser" >
				<?php _etag ('i18n', 'auth.user.passwordConfirmation'); ?>
				<?php if ($ppo->createUser) { ?><span class="required" > *</span><?php } ?>
			</label>
		</th>
		<td>
			<input type="password" id="password_confirmation_dbuser" name="password_confirmation_dbuser" value=""/>
			<script type="text/javascript">
				//<!--
				// autocomplete="off" est en JS pour validation xHTML
				$('password_confirmation_dbuser').setProperty ('autocomplete','off');
				//-->
			</script>
		</td>
	</tr>
	
	<tr>
		<th><label for="email_dbuser" ><?php _etag ('i18n', 'auth.user.email'); ?><span class="required" > *</span></label></th>
		<td><input type="text" id="email_dbuser" name="email_dbuser" value="<?php echo (isset ($ppo->user->email_dbuser)) ? $ppo->user->email_dbuser : '';?>" /></td>
	</tr>
	
	<?php if ($ppo->createInProcess && CopixModule::isEnabled ('antispam')) { ?>
	<tr>
		<th>
			<?php echo _i18n('auth.confirmcode'); ?><span class="required" > *</span>
			<br />
			<?php _eTag('antispam|captcha_question'); ?>
		</th>
		<td>
			<?php _eTag('antispam|captcha_response'); ?>
		</td>
	</tr>
	<?php } ?>
	
	<?php if (!$ppo->createInProcess ) { ?>
	<tr>
		<th><label for="enabled_dbuser_0" ><?php _etag ('i18n', 'auth.user.enabled'); ?><span class="required" > *</span></label></th>
		<td>
		<input type="radio" id="enabled_dbuser_0" name="enabled_dbuser" value=<?php echo '"1"'; if($ppo->user->enabled_dbuser == 1) { echo ' checked="checked"';} ?> />
		<?php _etag ('i18n', 'auth.user.enabledOk');?>
		<input type="radio" id="enabled_dbuser_1" name="enabled_dbuser" value=<?php echo '"0"'; if($ppo->user->enabled_dbuser == 0) { echo ' checked="checked"';} ?> />
		<?php _etag ('i18n', 'auth.user.enabledNok');?>
		</td>
	</tr>
	<?php } ?>
	
	
	<?php if (CopixModule::isEnabled ('authextend')) { ?>
		<?php _eTag ('copixzone', array ('process'=>'authextend|admineditextendparam', 'idForm'=>$ppo->idForm, 'id_user'=>(($ppo->user)?$ppo->user->id_dbuser: NULL), 'id_handler'=>'auth|dbuserhandler' )); ?>
	<?php } ?>
</table>

<p class="required" >
* <?php echo _i18n ('auth.required'); ?>
</p>

<p>
	<input name="idForm" type="hidden" value="<?php echo $ppo->idForm ?>" />
	<input type="submit" value="<?php _etag ('i18n', "copix:common.buttons.valid"); ?>" />
	
</p>

</form>

<form action="<?php echo _url ('auth|users|'); ?>" method="get" style="margin-left:70px; margin-top:-35px;" >
	<p>
		<input type="submit" name="submit" value="<?php _etag ('i18n', "copix:common.buttons.cancel"); ?>" />
	</p>
</form>