<?php
function _write_theme ($pTheme, $pTrClassId, $pPublicPages = true, $pAdminPages = true) {
	?>
	<tr <?php _eTag ('trclass', array ('id' => $pTrClassId)) ?>>
		<td>
			<?php if ($pTheme->image != null) { ?>
				<a href="<?php echo _url ('admin|themes|doSelectTheme', array ('theme' => $pTheme->id)) ?>"
					><img src="<?php echo _resource ($pTheme->image, $pTheme->id) ?>" border="0"
				/></a>
			<?php } ?>
		</td>
		<td width="100%">
			<center>
				<strong><?php echo $pTheme->name ?></strong>
			</center>

			<br />
			<table style="margin-left: auto; margin-right: auto">
				<?php if ($pTheme->author != null) { ?>
					<tr>
						<th><?php echo _i18n ('themes.theme.author') ?></th>
						<td><?php echo $pTheme->author ?></td>
					</tr>
				<?php } ?>
					<?php if ($pTheme->website != null) { ?>
					<tr>
						<th><?php echo _i18n ('themes.theme.website') ?></th>
						<td><a href="<?php echo $pTheme->website ?>"><?php echo $pTheme->website ?></a></td>
					</tr>
				<?php } ?>
					<?php if ($pTheme->description != null) { ?>
					<tr>
						<th><?php echo _i18n ('themes.theme.description') ?></th>
						<td><?php echo $pTheme->description ?></td>
					</tr>
				<?php } ?>
				<tr>
					<th><?php echo _i18n ('themes.theme.uses') ?></th>
					<td>
						<?php
						if ($pPublicPages && $pAdminPages) {
							_eTag ('button', array ('captioni18n' => 'themes.theme.defineAll', 'url' => _url ('admin|themes|doSelectTheme', array ('theme' => $pTheme->id))));
						}
						?>
						<?php
						if ($pPublicPages) {
							_eTag ('button', array ('captioni18n' => 'themes.theme.definePublic', 'url' => _url ('admin|themes|doSelectTheme', array ('theme' => $pTheme->id, 'type' => 'public'))));
						}
						?>
						<?php
						if ($pAdminPages) {
							_eTag ('button', array ('captioni18n' => 'themes.theme.defineAdmin', 'url' => _url ('admin|themes|doSelectTheme', array ('theme' => $pTheme->id, 'type' => 'admin'))));
						}
						?>
					</td>
				</tr>
				<tr>
					<th><?php echo _i18n ('themes.theme.others') ?></th>
					<td>
						<?php _eTag ('button', array ('captioni18n' => 'themes.theme.preview', 'url' => _url ('admin|themes|preview', array ('theme' => $pTheme->id)))) ?>
						<?php _eTag ('button', array ('captioni18n' => 'themes.theme.optimize', 'url' => _url ('admin|themes|optimize', array ('theme' => $pTheme->id)))) ?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<?php
}
?>

<?php _eTag ('beginblock', array ('isFirst' => true, 'titlei18n' => 'themes.title.publicTheme')) ?>
<table class="CopixTable">
	<?php _write_theme ($ppo->publicTheme, 'public', false, true) ?>
</table>
<?php _eTag ('endblock') ?>

<?php _eTag ('beginblock', array ('titlei18n' => 'themes.title.adminTheme')) ?>
<table class="CopixTable">
	<?php _write_theme ($ppo->adminTheme, 'admin', true, false) ?>
</table>
<?php _eTag ('endblock') ?>

<?php if (count ($ppo->arThemes) > 0) { ?>
	<?php _eTag ('beginblock', array ('titlei18n' => 'themes.title.otherThemes')) ?>
	<table class="CopixTable">
		<tr>
			<th><?php echo _i18n ('themes.theme.photo') ?></th>
			<th><?php echo _i18n ('themes.theme.infos') ?></th>
		</tr>

		<?php foreach ($ppo->arThemes as $theme) { ?>
			<?php _write_theme ($theme, 'availables') ?>
		<?php } ?>
	</table>
	<?php _eTag ('endblock') ?>
<?php } ?>

<?php _eTag ('back', array ('url' => 'admin||')) ?>