<h2 class="first"><?php echo _i18n ('dbserver.section.chooseConnection') ?></h2>
<form action="<?php echo _url ('admin|parameters|dbserver') ?>" method="GET">
<?php echo _i18n ('dbserver.connectionProfile') ?>&nbsp;
<select name="profile" onchange="this.form.submit ()">
	<?php foreach ($ppo->profiles as $profile) { ?>
		<option value="<?php echo $profile ?>"<?php if ($ppo->selectedProfile == $profile) echo ' selected="selected"' ?>><?php echo $profile ?></option>
	<?php } ?>
</select>
</form>

<?php foreach ($ppo->sections as $sectionName => $infos) { ?>
	<h2><?php echo $sectionName ?></h2>
	<table class="CopixVerticalTable">
		<?php foreach ($infos as $caption => $value) { ?>
			<tr <?php _eTag ('trclass') ?>>
				<td width="270px"><?php echo $caption ?></td>
				<td>
					<?php if (is_array ($value)) { ?>
						<ul>
							<?php foreach ($value as $key => $item) { ?>
								<li><?php if (!is_int ($key)) { echo $key; } else { echo $item; } ?></li>
								<?php if (is_array ($item)) { ?>
									<ul>
										<?php foreach ($item as $key2 => $item2) { ?>
											<?php if (is_array ($item2)) { ?>
												<li><?php echo $key2 ?></li>
												<ul>
													<?php foreach ($item2 as $key3 => $item3) { ?>
														<li><?php if (!is_int ($key3)) echo $key3 . ' : '?><?php echo $item3 ?></li>
													<?php } ?>
												</ul>
											<?php } else { ?>
												<li><?php if (!is_int ($key2)) echo $key2 . ' : '; ?><?php echo $item2 ?></li>
											<?php } ?>
										<?php } ?>
									</ul>
								<?php } ?>
							<?php } ?>
						</ul>
					<?php } else { ?>
						<?php echo $value ?>
					<?php } ?>
				</td>
			</tr>
		<?php } ?>
	</table>
<?php } ?>

<br />
<?php _eTag ('back', array ('url' => 'admin|parameters|')) ?>