<table class="CopixVerticalTable">
	<tr>
		<th style="width: 20%"><?php echo _i18n ('copix:log.CopixLogDBStrategy.header.profile') ?></th>
		<td>
			<select name="config_profile">
				<?php foreach ($profiles as $name) { ?>
					<option <?php if ($profile == $name) { echo 'selected="selected"'; } ?>><?php echo $name ?></option>
				<?php } ?>
			</select>
		</td>
		<td style="width:20px"><?php _eTag ('popupinformation', array (), _i18n ('copix:log.CopixLogDBStrategy.help.profile')) ?></td>
	</tr>
</table>