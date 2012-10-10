<?php 
echo '<form action="'._url ('database|repair').'" method="post">';

foreach ($ppo->tables as $profile=>$tables){
?>
<h2><?php _eTag ('i18n', array ('key'=>'database profile [%s]', 'profile'=>$profile)); ?></h2>

<?php 

if (count ($tables['InnoDB']) > 0){
	$innoDB = false;
	echo '<h3>'._tag ('i18n', array ('key'=>'To be converted into InnoDB')).'</h3>';
	echo '<ul>';
	foreach ($tables['InnoDB'] as $table){
		echo '<li><input type="checkbox" name="tables['.$profile.'][InnoDB][]" value="'.$table.'" />'.$table.'</li>';
	}
	echo '</ul>';
}else{
	$innoDB = true;
}
?>

<?php 
if (count ($tables['UTF8']) > 0){
	$UTF8 = false;
	echo '<h3>'._tag ('i18n', array ('key'=>'To be converted into UTF8')).'</h3>';
	echo '<ul>';
	foreach ($tables['UTF8'] as $table){
		echo '<li><input type="checkbox" name="tables['.$profile.'][UTF8][]" value="'.$table.'" />'.$table.'</li>';
	}
	echo '</ul>';
}else{
	$UTF8 = true;
}
?>

<?php 
if ($UTF8 && $innoDB){
	_eTag ('i18n', array ('key'=>'Tables are OK in this profile'));
}else{
	echo '<input type="submit" value="'._tag ('i18n', array ('key'=>'Repair selected tables')).'" />';
}
?>
<?php
}
echo '</form>';
?>