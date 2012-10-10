<?php if ($align == 'right') { ?>
	<div style="text-align: right; width: 100%">
<?php } ?>
<a href="<?php echo $url ?>">
	<img src="<?php echo _resource ('img/tools/back.png') ?>" alt="<?php echo _i18n ('copix:common.buttons.back') ?>" />
	<?php echo _i18n ('copix:common.buttons.back') ?>
</a>
<?php if ($align == 'right') { ?>
	</div>
<?php } ?>