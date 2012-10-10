<form action="<?php echo _url ('log|show'); ?>" method="POST">
<?php _eTag ('select', array ('values'=>$ppo->profils, 'name'=>'profile', 'selected'=>$ppo->profil)); ?>
<input type="submit" value="<?php echo _i18n ('copix:common.buttons.show'); ?>" />
</form>
<?php
	if(isset($ppo->profil)){
		echo CopixZone::process ('ShowLog', array ('profil'=>$ppo->profil)); 
	}
?>
<a href="<?php echo _url ("admin||"); ?>"><input type="button" value="<?php echo _i18n ('copix:common.buttons.back'); ?>" /></a>