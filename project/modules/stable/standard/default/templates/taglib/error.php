<div class="<?php echo $class; ?>">
<h1><?php echo $title ?></h1>
<?php if (count ($errors) > 1) { ?>
	<ul>
		<?php foreach ($errors as $error) { ?>
			<li><?php echo $error ?></li>
		<?php } ?>
	</ul>
<?php } else { ?>
	<?php echo $error ?>
<?php } ?>
</div>