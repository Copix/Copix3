<form action="<?php _url('repository|default|Add');?>" method="POST" enctype="multipart/form-data">
<input type="hidden" name="confirm" value="1">
<fieldset>
<table class="CopixVerticalTable">
	<tr>
		<th><?php _etag ('i18n', 'repository.form.fileupload');?></th>
		<td><input type="file" name="uploadfile"></td>
	</tr>
	<tr class="alternate">
		<th><?php _etag ('i18n', 'repository.form.uploader');?></th>
		<td><?php echo $ppo->uploader ;?></td>
	</tr>
	<tr>
		<th><?php _etag ('i18n', 'repository.form.comment');?></th>
		<td><textarea cols="50" rows="5" name="comment"></textarea></td>
	</tr>
	<tr>
		<td colspan="2"><input type="image" src="<?php CopixUrl::getResource ('img/tools/add.png') ;?>" alt="<?php _etag ('i18n', 'repository.form.save');?>"></td>
</table>
</form>
