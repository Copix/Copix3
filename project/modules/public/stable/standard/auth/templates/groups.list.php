<table class="CopixTable"> 
<thead>
 <tr>
  <th><?php _etag ('i18n', 'auth.groupCaption'); ?></th>
  <th><?php _etag ('i18n', 'copix:common.actions.title'); ?></th>
 </tr>
</thead>
	<tbody>
		<?php
		$modulo = 0; 
		foreach ($ppo->arGroups as $group) { ?>
		<tr <?php echo  (++$modulo % 2 == 0) ? 'class="alternate"' : ' '; ?>>
			<td><?php echo $group->caption_dbgroup; ?></td>
			<td><a
				href="<?php echo _url ("auth|groups|edit", array ("id"=>$group->id_dbgroup)); ?>"><img
				src="<?php echo _resource ("img/tools/update.png"); ?>"
				alt="<?php _etag ('i18n', 'copix:common.buttons.update'); ?>" /></a>
			<a
				href="<?php echo _url ("auth|groups|delete", array ("id"=>$group->id_dbgroup)); ?>"><img
				src="<?php echo _resource ('img/tools/delete.png'); ?>"
				alt="<?php _etag ('i18n', "copix:common.buttons.delete"); ?>" /></a></td>
		</tr>
		<?php } ?>
	</tbody>
</table>

<p>
 <a href="<?php echo _url ('auth|groups|create'); ?>"><img src="<?php echo _resource('img/tools/new.png'); ?>" alt="<?php _etag ('i18n', 'copix:common.buttons.new');?>" />
 <?php _etag ('i18n', 'copix:common.buttons.new');?></a>
</p>
<input type="submit" value="<?php _etag('i18n',"copix:common.buttons.back"); ?> " onclick="javascript:window.location='<?php echo _url("admin||"); ?>'">
