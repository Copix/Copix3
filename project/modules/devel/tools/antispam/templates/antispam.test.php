<form action="<?php echo _url ('antispam||valid')?>" method="POST">
<?php
echo $ppo->antispam;
?>
<br/>
<input type="submit" value="<?php  echo _i18n ('antispam.valid'); ?>" />
</form>