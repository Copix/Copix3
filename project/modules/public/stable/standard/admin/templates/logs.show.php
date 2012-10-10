<form action="<?php echo _url ('log|show'); ?>" method="POST">
<?php _eTag ('select', array ('values'=>$ppo->profils, 'name'=>'profile', 'selected'=>$ppo->profil)); ?>
&nbsp;
<br/>
<?php // Input du nombre d'élements à afficher 
echo _i18n ('logs.show.nblines');
echo '&nbsp;';
_eTag ('inputtext', array ( 'value'=>$ppo->nbitems, 'name'=>'nbitems', 'size'=>3 ));
?>
&nbsp;
<input type="submit" value="<?php echo _i18n ('copix:common.buttons.show'); ?>" />
</form>

<br />
<?php
	if(isset($ppo->profil)){
		echo CopixZone::process ('ShowLog', array ('profil'=>$ppo->profil, 'nbitems'=>$ppo->nbitems)); 
	}
?>

<br />
<a href="<?php echo _url ("admin||"); ?>"><input type="button" value="<?php echo _i18n ('copix:common.buttons.back'); ?>" /></a>