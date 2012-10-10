<?php if ($ppo->clicker == null) { ?>
	<?php $ppo->clicker = uniqid ('preferences_clicker_') ?>
	<a href="javascript: void(0);" id="<?php echo $ppo->clicker ?>">
		<img src="<?php echo $ppo->img ?>" alt="<?php echo _i18n ('admin|userpreferences.alt.config') ?>" title="<?php echo _i18n ('admin|userpreferences.alt.config') ?>" />
		<?php echo $ppo->caption ?>
	</a>
<?php } ?>

<?php _eTag ('copixwindow', array ('id' => $ppo->uniqId . '_window', 'clicker' => $ppo->clicker, 'title' => $ppo->title, 'fixed' => $ppo->fixed), $ppo->content) ?>