<script type="text/javascript">
//<![CDATA[
function doUrl (pUrl) {
   var myForm = document.groupEdit;
   myForm.action = pUrl;
   myForm.submit ();
   return false;
}
//]]>
</script>

<form name="groupEdit"
	action="<?php echo _url ('auth|groups|valid'); ?>" method="post">
<h2><?php _etag ('i18n', 'auth.groupInformation'); ?></h2>
<fieldset>
<table class="CopixVerticalTable">
	<tr>
		<th><label for="caption_dbgroup"><?php _etag ('i18n', 'auth.group.caption'); ?></label></th>
		<td><input type="text" id="caption_dbgroup" name="caption_dbgroup"
			value="<?php _etag ('escape', $ppo->group->caption_dbgroup); ?>"
			size="48" /></td>
	</tr>
	<tr>
		<th><label for="description_dbgroup"><?php _etag ('i18n', 'auth.group.description');?></label></th>
		<td><textarea name="description_dbgroup" cols="40" rows="5"><?php _etag ('escape', $ppo->group->description_dbgroup); ?></textarea></td>
	</tr>
	<tr>
		<th><label for="public_dbgroup"><?php _etag ('i18n', 'auth.group.public'); ?></label></th>
		<td><input type="checkbox" class="checkbox" id="public_dbgroup"
			name="public_dbgroup"
			<?php if ($ppo->group->public_dbgroup) { echo 'checked="checked"';} ?> /></td>
	</tr>
	<tr>
		<th><label for="registered_dbgroup"><?php _etag ('i18n', 'auth.group.registered'); ?></label></th>
		<td><input type="checkbox" class="checkbox" id="registered_dbgroup"
			name="registered_dbgroup"
			<?php if ($ppo->group->registered_dbgroup) { echo 'checked="checked"';} ?> /></td>
	</tr>
	<tr>
		<th><label for="superadmin_dbgroup"><?php _etag ('i18n', 'auth.group.superadmin'); ?></label></th>
		<td><input type="checkbox" class="checkbox" id="superadmin_dbgroup"
			name="superadmin_dbgroup"
			<?php if ($ppo->group->superadmin_dbgroup) { echo 'checked="checked"';} ?> /></td>
	</tr>
</table>
</fieldset>

<h2><?php _etag ('i18n', 'auth.usersInGroup'); ?></h2>
<?php if (count ($ppo->arUsers)){ ?>
<table class="CopixTable">
	<thead>
		<tr>
			<th><?php _etag ('i18n', 'auth.user.login'); ?></th>
			<th><?php _etag ('i18n', 'copix:common.actions.title'); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php $modulo = 0; foreach ($ppo->arUsers as $handler=>$usersInHandler){ 
						foreach ($usersInHandler as $id=>$caption){
		?>
		<tr <?php echo  (++$modulo % 2 == 0) ? 'class="alternate"' : ' '; ?>>
		 <td><?php _etag ('escape', $caption); ?></td>
		 <td><a
				href="<?php echo _url ('auth|groups|removeUser', array ('handlerUser'=>$handler, 'idUser'=>$id)); ?>"
				onclick="return doUrl ('<?php echo _url ('auth|groups|removeUser', array ('handlerUser'=>$handler, 'idUser'=>$id)); ?>')"><img
				src="<?php echo _resource ("img/tools/delete.png"); ?>"
				alt="<?php _etag ('i18n', 'copix:common.buttons.delete');?>"
				title="<?php _etag ('i18n', 'copix:common.buttons.delete'); ?>" /></a></td>
		</tr>
	<?php } } ?>
	</tbody>
</table>
<?php }else{ 
   echo '<p>'._i18n ("auth.group.noUser").'</p>';
} ?>
<p><a href="<?php echo _url ('auth|groups|selectUsers');?>" onclick="return doUrl ('<?php echo _url ('auth|groups|selectUsers');?>');"><img
	src="<?php echo _resource ('img/tools/add.png'); ?>"
	alt="<?php echo _etag ('i18n', "copix:common.buttons.new"); ?>" /><?php _etag ('i18n', 'auth.group.addUser'); ?></a></p> 
	<?php if (CopixConfig::instance()->copixauth_isRegisteredCredentialHandler ('auth|dbmodulecredentialhandler')) { ?>
<p><a href="<?php echo _url('auth|module|list',array('id_group'=>$ppo->group->id_dbgroup, 'handler_group'=>'auth|dbgrouphandler', 'url_return'=>_url('#'))); ?>">
   <img
	src="<?php echo _resource ('img/tools/update.png'); ?>"
	alt="<?php echo _etag ('i18n', "copix:common.buttons.new"); ?>" /><?php _etag ('i18n', 'auth.group.editModuleCredential'); ?></a>
   </p>
	<?php }
	if (CopixConfig::instance()->copixauth_isRegisteredCredentialHandler ('auth|dbdynamiccredentialhandler')) {
	    ?>
<p><a href="<?php echo _url('auth|dynamic|list',array('id_group'=>$ppo->group->id_dbgroup, 'handler_group'=>'auth|dbgrouphandler', 'url_return'=>_url('#'))); ?>" >
   <img
	src="<?php echo _resource ('img/tools/update.png'); ?>"
	alt="<?php echo _etag ('i18n', "copix:common.buttons.new"); ?>" /><?php _etag ('i18n', 'auth.group.editDynamicCredential'); ?></a>
   </p>
   <?php } ?>
   
		<input type="submit"
			value="<?php _etag ('i18n', "copix:common.buttons.save"); ?>" />
		<input type="button"
			value="<?php _etag ('i18n', 'copix:common.buttons.cancel'); ?>"
			onclick="javascript:document.location.href='<?php echo _url("auth|groups|");?>'" />

		<?php _etag ('formfocus', array ('id'=>'caption_dbgroup')); ?>
</form>