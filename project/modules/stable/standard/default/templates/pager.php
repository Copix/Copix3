<?php if ($showPrevious) { ?>
	<a href="<?php echo $previous ?>"><img src="<?php echo _resource ('img/tools/previous.png') ?>" /></a>
<?php } ?>

<?php
foreach ($pages as $index => $page) {
	if ($currentPage == ($index + 1)) {
		echo '<b>' . ($index + 1) . '</b> ';
	} else {
		echo '<a href="' . $page . '">' . ($index + 1) . '</a> ';
	}
}
?>

<?php if ($showNext) { ?>
	<a href="<?php echo $next ?>"><img src="<?php echo _resource ('img/tools/next.png') ?>" /></a>
<?php } ?>