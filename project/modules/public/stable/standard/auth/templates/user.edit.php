<?php if (count ($ppo->errors)) { ?>
<div class="errorMessage">
 <h1><?php _etag ('i18n', 'copix:common.messages.error');?> </h1>
 <?php _etag ('ulli', array ('values'=>$ppo->errors)); ?>
</div>
<?php } ?>

<form name="userEdit"
	action="<?php if($ppo->createInProcess){echo _url ("auth|usersregister|valid");} else { echo _url ("auth|users|valid");} ?>"
	method="post">

<table class="CopixVerticalTable">
	<?php if (isset ($ppo->user->id_dbuser)) { ?>
	<tr> 
	    <th><?php _etag ('i18n', 'auth.user.id'); ?></th>
	    <td><?php echo $ppo->user->id_dbuser;  ?></td>
	</tr>
	<?php } ?>
	
	<tr>
        <th><?php _etag ('i18n', 'auth.user.login'); ?></th>
	    <?php if (isset ($ppo->user->login_dbuser)) { ?>
	    <td><input type="text" name="login_dbuser" value="<?php _etag ('escape', $ppo->user->login_dbuser); ?>" /></td>
        <?php } else { ?>
        <td><input type="text" name="login_dbuser" value="" /></td>
        <?php }?>
	</tr>

	<tr>
		<th><?php _etag ('i18n', 'auth.user.password'); ?></th>
		<td><input type="password" name="password_dbuser" value="" /></td>
	</tr>

	<tr>
		<th><?php _etag ('i18n', 'auth.user.passwordConfirmation'); ?></th>
		<td><input type="password" name="password_confirmation_dbuser" value="" /></td>
	</tr>
	
	<tr>
		<th><?php _etag ('i18n', 'auth.user.email'); ?></th>
		<?php if (isset ($ppo->user->email_dbuser)) { ?>
		<td><input type="text" name="email_dbuser" value="<?php echo $ppo->user->email_dbuser; ?>" /></td>
        <?php } else { ?>
        <td><input type="text" name="email_dbuser" value="" /></td>
        <?php }?>
    </tr>
	
	
    <?php if ($ppo->createInProcess && CopixModule::isEnabled ('antispam')) { ?>
    <tr>
        <th>
            <?php echo _i18n('auth.confirmcode'); ?>
            <br />
            <?php echo _tag('imageprotect', array ('id'=>($id=uniqid()))); ?>
        </th>
        <td>
            <input type="text" name="confirmcode_dbuser" value="" />
            <input type="hidden" name="idcode_dbuser" value="<?php echo $id;?>" /></td>
        </td>
    </tr>
    <?php } ?>
	
	<?php if (!$ppo->createUser) { ?>
	<tr>
		<th><?php _etag ('i18n', 'auth.user.enabled'); ?></th>
		<td>
		<input type="radio" id="enabled_dbuser" name="enabled_dbuser" value=<?php echo '"1"'; if($ppo->user->enabled_dbuser == 1) { echo 'checked="checked"';} ?> />
		<?php _etag ('i18n', 'auth.user.enabledOk');?>
		<input type="radio" id="enabled_dbuser" name="enabled_dbuser" value=<?php echo '"0"'; if($ppo->user->enabled_dbuser == 0) { echo 'checked="checked"';} ?> />
		<?php _etag ('i18n', 'auth.user.enabledNok');?>
		</td>
	</tr>
	<?php } ?>
</table>

<p><input type="submit"
	value="<?php _etag ('i18n', "copix:common.buttons.valid"); ?>" />
<input type="button"
	value="<?php _etag ('i18n', "copix:common.buttons.cancel"); ?>"
	onclick="javascript:document.location.href='<?php if($ppo->createInProcess) {echo _url ('');} else {echo _url ('auth|users|');} ?>'" />
</p>

</form>