<?php
_eTag ('error', array ('message'=>$ppo->errors));

$last = null;
echo '<h2>', _tag ('i18n', array ('key'=>'Tags list')), '</h2>';
echo '<table class="CopixTable">';
for ($i = 0; ($i < count ($ppo->arTags)); $i++) {
    if ($last !== strtoupper (substr ($ppo->arTags[$i], 0, 1))) {
		$last = utf8_encode(strtoupper (substr (utf8_decode ($ppo->arTags[$i]), 0, 1)));
		echo '<tr><th colspan="2">'.utf8_encode(htmlentities (utf8_decode ($last))).'</th></tr>';
    }
    echo '<tr><td>'. utf8_encode(htmlentities (utf8_decode ($ppo->arTags[$i]))).
         '</td><td><a href="'.
         utf8_encode(htmlentities (utf8_decode (_url ('admin|edit', array('name_tag'=> $ppo->arTags[$i], 'namespace' => $ppo->namespace))))).
         '" ><img src="'.
         _resource('img/tools/update.png').
         '" alt="'.
         _i18n ('copix:common.buttons.update').
         '" /></a>&nbsp;<a href="'.
         utf8_encode(htmlentities (utf8_decode (_url ('admin|delete', array('name_tag'=> $ppo->arTags[$i], 'namespace' => $ppo->namespace))))).
         '" ><img src="'.
         _resource('img/tools/delete.png') .
         '" alt="'.
         _i18n ('copix:common.buttons.cross').
         '" /></a></td></tr>';
}
echo "</table>";
?>

<br />
<h2><?php _eTag ('i18n', array ('key'=>'Create a new Tag')); ?></h2>
<form action="<?php echo _url ('admin|add', array('namespace' => $ppo->namespace)); ?>" method="post" > 
	<input type="text" id="name_tag" name="name_tag" maxlength="<?php echo $ppo->maxlength; ?>" value="<?php echo $ppo->tagWrite; ?>" />
	<input src="<?php echo _resource('img/tools/add.png'); ?>" alt="<?php echo _i18n ('copix:common.buttons.add'); ?>" type="image" value="<?php echo _i18n ('copix:common.buttons.add');?>" />
</form>
<br />

<form action="<?php echo _url ('admin|default|default'); ?>" method="get" >
    <input type="submit" value="<?php echo _i18n ('copix:common.buttons.back');?>" /> 
</form>
<?php 
_eTag ('formfocus', array ('id'=>'name_tag'));
?>