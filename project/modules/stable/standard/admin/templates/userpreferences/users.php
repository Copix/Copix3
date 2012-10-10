<h2 class="first"><?php echo _i18n ('userpreferences.title.users') ?></h2>
<table class="CopixTable">
	<tr>
		<th style="width: 200px"><?php echo _i18n ('userpreferences.header.login') ?></th>
		<th><?php echo _i18n ('userpreferences.header.userhandler') ?></th>
		<th></th>
	</tr>
	<?php foreach ($ppo->users as $user) { ?>
		<tr <?php _eTag ('trclass') ?>>
			<td><a href="<?php echo _url ('admin|userpreferences|modules', array ('user' => $user['user'], 'userhandler' => $user['userhandler'])) ?>"><?php echo $user['login'] ?></a></td>
			<td><?php echo $user['userhandler'] ?></td>
			<td class="action">
				<a href="<?php echo _url ('admin|userpreferences|deleteAll', array ('user' => $user['user'], 'userhandler' => $user['userhandler'])) ?>"
					><img src="<?php echo _resource ('img/tools/delete.png') ?>" alt="<?php echo _i18n ('copix:common.buttons.delete') ?>" title="<?php echo _i18n ('copix:common.buttons.delete') ?>"
				/></a>
			</td>
		</tr>
	<?php } ?>
</table>

<br />
<?php _eTag ('back', array ('url' => _url ('admin||'))) ?>