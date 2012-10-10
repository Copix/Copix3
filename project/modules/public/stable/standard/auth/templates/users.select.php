<form action="<?php echo _url ('auth|groups|addUsers'); ?>" method="POST" > 
<table class="CopixTable"> 
<thead>
 <tr>
  <th><?php _etag ('i18n', 'auth.userLogin'); ?></th>
 </tr>
</thead>
	<tbody>
		<?php
		$modulo = 0; 
		foreach ($ppo->arUsers as $handler=>$users) { 
		   foreach ($users as $user){
			?>
		<tr <?php echo  (++$modulo % 2 == 0) ? 'class="alternate"' : ' '; ?>>
			<td><input type="checkbox"
				value="<?php _etag ('escape', $user->login); ?>"
				name="users[<?php echo $handler; ?>][<?php _etag ('escape', $user->id); ?>]"/><?php echo $user->login; ?></td>
		</tr>
		<?php }
 		} ?>
	</tbody>
</table>
<input type="submit" value="<?php _etag('i18n',"copix:common.buttons.ok"); ?> " />
</form>