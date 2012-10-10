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
		<td style="width:20px">
			<?php
			$content = 'La base de données doit contenir les tables suivantes : <ul>';
			$content .= '<li>cms_actions</li>';
			$content .= '<li>cms_actions_extras</li>';
			$content .= '<li>cms_actions_profiles</li>';
			$content .= '<li>cms_actions_users</li>';
			$content .='</ul>';
			_eTag ('popupinformation', array (), $content);
			?>
		</td>
	</tr>
</table>