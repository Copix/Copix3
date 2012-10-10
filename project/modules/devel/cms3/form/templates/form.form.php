<script language="javascript">
<!--
function addField (){
	$('all_field').clone().injectAfter('all_field');
}
-->
</script>
<form action="<?php echo _url ("form|admin|valid", array ('editId'=>$ppo->editId));?>" method="POST">

<table class="CopixVerticalTable">
 <tr>
	<th>Nom du formulaire</th>
	<td><input type="text" name="caption_hei" value="<?php echo $ppo->editedElement->caption_hei;?>" /></td>
 </tr>
 <tr>
	<th>Description</th>
	<td><textarea cols="40" rows="10" name="description_form"><?php echo $ppo->editedElement->description_form;?></textarea></td>
 </tr>
 <tr>
	<th>Champs</th>
	<td>
	<div id="libelle">Nom | Libellé | Type</div>
<?php 
if (isset ($ppo->editedElement->obj_form)) {
	echo CopixZone::process ('form|formaddfield', array ('formulaire'=>$ppo->editedElement->obj_form));
}
?>
	<div id="all_field" class="field">
<?php
	// Affichage d'un champ vide
	echo CopixZone::process ('form|formaddfield');
?>
</div>
<a href="javascript:return false;" onclick="javascript:addField ()"><img src="<?php echo _resource ('img/tools/add.png');?>" alt="<?php echo _i18n ('copix:common.buttons.add') ;?>" onclick="javascript:addField();"/> <?php echo _i18n ('copix:common.buttons.add');?></a>
	</td>
 </tr>
</table>

<input type="submit" name="valid" value="Sauvegarder" />
<input type="button" onclick="location.href='<?php echo _url ("admin|cancel", array ( 'editId'=>$ppo->editId));?>'" value="<?php echo _i18n ("copix:common.buttons.cancel");?>">
</form>
