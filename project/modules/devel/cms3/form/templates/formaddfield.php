<?php 

if (isset ($arField)) :
	foreach ($arField as $field):
?>
<div>
	<input type="text" name="fieldname[]" value="<?php echo $field->idElement;?>"> 
	<input type="text" name="libelle[]" value="<?php echo $field->fieldName;?>"> 
	<?php _etag ('select', array ('name'=>'type[]', 'id'=>'type', 'values'=>$arKind, 'selected'=>$field->kind));?>
</div>
<?php
	endforeach;
else :
?>
<input type="text" name="fieldname[]"> 
<input type="text" name="libelle[]"> 
<?php 
	_etag ('select', array ('name'=>'type[]', 'id'=>'type', 'values'=>$arKind));
?>
<?php
endif;
?>