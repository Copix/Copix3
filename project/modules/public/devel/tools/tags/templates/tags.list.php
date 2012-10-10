<?php
$last = null;

$pos = 0;
$letterTitle = false;
for ($i = 0; ($i < count ($ppo->arTags)); $i++){
	if ($last !== substr (utf8_decode ($ppo->arTags[$i]->name_tag), 0, 1)){
		if ($last !== null){
 			echo '</ul>';			
		}
		$last = utf8_encode (substr (utf8_decode ($ppo->arTags[$i]->name_tag), 0, 1));
 	 	echo '<h2>'.$last.'</h2><ul>';
	}
	echo '<li>', $ppo->arTags[$i]->name_tag, '</li>';
}
?>

<form action="<?php echo _url ('admin|add'); ?>" method="POST"> 
<input type="text" name="name_tag" />
<input type="submit" value="Ajouter" /> 
</form>