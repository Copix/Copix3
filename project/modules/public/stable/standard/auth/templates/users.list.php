<form action="<?php echo _url ('auth|users|'); ?>" method="POST">
 <input type="text" name="filter" id="filter" value="<?php _etag ('escape', $ppo->filter); ?>" />
 <input type="submit" value="<?php _etag ('i18n', 'copix:common.buttons.search'); ?>" />
</form>
<br />
<table class="CopixTable"> 
<thead>
 <tr>
  <th><?php _etag ('i18n', 'auth.userLogin'); ?></th>
  <th><?php _etag ('i18n', 'copix:common.actions.title'); ?></th>
 </tr>
</thead>
	<tbody>
		<?php
		$modulo = 0; 
		foreach ($ppo->arUsers as $handler=>$users) {
			foreach ($users as $user){ 
			?>
		<tr <?php echo  (++$modulo % 2 == 0) ? 'class="alternate"' : ' '; ?>>
			<td><?php echo $user->login; ?></td>
			<td><a
				href="<?php echo _url ("auth|users|edit", array ("id"=>$user->id)); ?>"><img
				src="<?php echo _resource ("img/tools/update.png"); ?>"
				alt="<?php _etag ('i18n', 'copix:common.buttons.update'); ?>" /></a>
			<a
				href="<?php echo _url ("auth|users|delete", array ("id"=>$user->id)); ?>"><img
				src="<?php echo _resource ('img/tools/delete.png'); ?>"
				alt="<?php _etag ('i18n', "copix:common.buttons.delete"); ?>" /></a></td>
		</tr>
		<?php }
		}?>
	</tbody>
</table>

<p>
 <a href="<?php echo _url ('auth|users|create'); ?>"><img src="<?php echo _resource('img/tools/new.png'); ?>" alt="<?php _etag ('i18n', 'copix:common.buttons.new');?>" />
 <?php _etag ('i18n', 'copix:common.buttons.new');?></a>
</p>
<input type="submit" value="<?php _etag('i18n',"copix:common.buttons.back"); ?> " onclick="javascript:window.location='<?php echo _url("admin||"); ?>'">