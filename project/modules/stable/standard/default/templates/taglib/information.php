<?php _eTag ('beginblock', array ('title' => $title, 'isFirst' => true)) ?>
<?php echo implode ('<br />', $message) ?>
<br /><br />
<?php foreach ($links as $url => $caption) { ?>
	<a href="<?php echo $url ?>"><?php echo $caption ?></a><br />
<?php } ?>
<?php _eTag ('endblock') ?>