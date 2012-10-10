<?php _eTag ('error', array ('message' => $ppo->errors)) ?>

<?php if ($ppo->redirect != null) { ?>
	<center>
		<a href="<?php echo $ppo->redirect ?>">Naviguer sur le site malgré les avertissements</a>
	</center>
<?php } ?>