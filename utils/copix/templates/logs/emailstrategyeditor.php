<table class="CopixVerticalTable">
	<tr>
		<th style="width: 20%"><?php echo _i18n ('copix:log.CopixLogEmailStrategy.header.to') ?></th>
		<td><input size="60" type="text" name="config_to" class="inputText" value="<?php echo $to ?>" /></td>
		<td style="width:20px"><?php _eTag ('popupinformation', array (), _i18n ('copix:log.CopixLogEmailStrategy.help.to')) ?></td>
	</tr>
	<tr class="alternate">
		<th><?php echo _i18n ('copix:log.CopixLogEmailStrategy.header.mailSubject') ?></th>
		<td><input size="60" type="text" name="config_mailSubject" class="inputText" value="<?php echo $mailSubject ?>" /></td>
		<td><?php _eTag ('popupinformation', array (), _i18n ('copix:log.CopixLogEmailStrategy.help.mailSubject')) ?></td>
	</tr>
	<tr>
		<th><?php echo _i18n ('copix:log.CopixLogEmailStrategy.header.from') ?></th>
		<td colspan="2"><input size="25" type="text" name="config_from" class="inputText" value="<?php echo $from ?>" /></td>
	</tr>
	<tr class="alternate">
		<th><?php echo _i18n ('copix:log.CopixLogEmailStrategy.header.fromname') ?></th>
		<td colspan="2"><input size="25" type="text" name="config_fromname" class="inputText" value="<?php echo $fromname ?>" /></td>
	</tr>
</table>