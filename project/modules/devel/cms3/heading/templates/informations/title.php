<div class="togglerContainer" <?php echo $id ? "id='$id'" : ""; ?>>
	<a href="#" class="toggler"<?php if (strlen ($title) > 50) { echo ' title="'.$title.'"';} ?>>
		<img src="<?php echo $icon ?>" alt="" /> <?php echo (strlen ($title) > 50) ? substr ($title, 0, 50) . '...' : $title; ?>
	</a>
</div>