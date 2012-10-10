<?php
if ($ppo->isValid) {
?>
<h1><?php echo _i18n ('antispam.result.ok'); ?></h1>
<?php 
} else {
?>
<h1><?php echo _i18n ('antispam.result.nok'); ?></h1>
<form action="<?php echo _url ('antispam||valid')?>" method="POST">
<?php
	echo CopixZone::process ('antispam|antispam'); 
?>
<br/>
<input type="submit" value="<?php  echo _i18n ('antispam.valid'); ?>" />
</form>
<?php } ?>