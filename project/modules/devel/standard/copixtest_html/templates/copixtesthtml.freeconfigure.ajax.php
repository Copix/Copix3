<?php
/* Gestion des radios */

 if ($ppo->arData->validType == 'exist') {
	$exist = 'checked';
	$notexist = '';
} else {
	$exist = '';
	$notexist = 'checked';
}
if ($ppo->arData->checkType !== null) {
					$var = 'checktype_'.$ppo->arData->checkType;
					$$var = 'checked';
} else {
	$checktype_notest = 'checked';
}

if ($ppo->arData) {
	$activate = 'checked';
} else {
	$activate = 'checked';
}
?>

<div id="ConfigureTag_<?php echo $ppo->arData->id_tag ?>">
<input type="hidden" name="freetest[]" value="<?php echo $ppo->arData->id_tag ?>" />
	<table class="CopixVerticalTable">
	<tr>
	<th> <?php _etag('i18n', array('key' => 'copixtest_html.configure.freetest')); ?> </th>
	</tr>
	<tr>
		<th>
			<?php _etag('i18n', array('key' => 'copixtest_html.configure.activationtitle')); ?>
			<input type="radio" name="activation_<?php echo $ppo->arData->id_tag ?>" value="yes" <?php echo $activate ?> />
			<?php _etag('i18n', array('key' => 'copixtest_html.configure.active')); ?>
			<input type="radio" name="activation_<?php echo $ppo->arData->id_tag ?>" value="no" /> 
			<?php _etag('i18n', array('key' => 'copixtest_html.configure.desactive')); ?>
		</th>
	</tr>
	</table>  
	<table class="CopixVerticalTable">
		<tr>
			<th><?php _etag('i18n', array('key' => 'copixtest_html.freeConfigure.id')) ?> </th>
			<td> <b><?php if (isset($ppo->arData->id_tag)) {echo $ppo->arData->id_tag;} ?></b>  </td>
		</tr>
	
		<tr>
			<th> <?php _etag('i18n', array('key' => 'copixtest_html.freeConfigure.path')) ?> </th>
			<td> <input type="text" name="path_<?php echo $ppo->arData->id_tag ?>" value="<?php if(isset($ppo->arData->path_tag)) {echo $ppo->arData->path_tag;} ?>" /> </td>
		</tr>
		
		<tr>
			<th> <?php _etag('i18n', array('key' => 'copixtest_html.freeConfigure.name')) ?> </th>
			<td> <input type="text" name="name_<?php echo $ppo->arData->id_tag ?>" value="<?php if(isset($ppo->arData->name_tag)) {echo $ppo->arData->name_tag;} ?>" /> </td>
		</tr>
		
		<tr>
			<th> <?php _etag('i18n', array('key' => 'copixtest_html.freeConfigure.attributes')) ?> </th>
			<td> <input type="text" name="attributes_<?php echo $ppo->arData->id_tag ?>" value="<?php if(isset($ppo->arData->attributes_tag)) {echo $ppo->arData->attributes_tag;} ?>" /> </td>
		</tr>
		
		<tr>
			<th> <?php _etag('i18n', array('key' => 'copixtest_html.freeConfigure.contains')) ?> </th>
			<td> <input type="text" name="contains_<?php echo $ppo->arData->id_tag ?>" value="<?php if (isset($ppo->arData->contains)) {echo $ppo->arData->contains;} ?>" /> </td>
		</tr>
		
		<tr>
			<th> <?php _etag('i18n', array('i18n', 'key' => 'copixtest_html.configure.util')); ?> </th>
			<td>
				<input name="validType_<?php echo $ppo->arData->id_tag ?>" type="radio" value="exist" <?php echo $exist ?>/> <?php _etag('i18n', array('key' => 'copixtest_html.configure.exist')); ?>
				<input name="validType_<?php echo $ppo->arData->id_tag ?>" type="radio" value="exclude" <?php echo $notexist ?>/>	<?php _etag('i18n', array('key' => 'copixtest_html.configure.notexist')); ?>
			 </td>
		</tr>
		
		<tr>
			<th> <?php _etag('i18n', array('key' => 'copixtest_html.freeConfigure.type')) ?> </th>
			<td> 
			<input type="radio" name="checktype_<?php echo $ppo->arData->id_tag ?>" value="notest" <?php if(isset($checktype_notest)) { echo $checktype_notest; } ?>>
			<?php _etag ('i18n', array ('key'=>'copixtest_html.configure.notest')); ?><br />
			<input type="radio" name="checktype_<?php echo $ppo->arData->id_tag ?>" value="simple" <?php if(isset($checktype_simple)) {echo $checktype_simple;} ?>>
			<?php _etag ('i18n', array ('key'=>'copixtest_html.configure.simple')); ?><br />
			<input type="radio" name="checktype_<?php echo $ppo->arData->id_tag ?>" value="moderate" <?php if(isset($checktype_moderate)) {echo $checktype_moderate;} ?>>
			<?php _etag ('i18n', array ('key'=>'copixtest_html.configure.moderate')); ?><br />
			<input type="radio" name="checktype_<?php echo $ppo->arData->id_tag ?>" value="absolute" <?php if(isset($checktype_absolute)) {echo $checktype_absolute;} ?>>
			<?php _etag ('i18n', array ('key'=>'copixtest_html.configure.absolute')); ?><br />
			 </td>
		</tr>
	</table>
<br /> <br />
</div>
<?php if(isset($$var)) {
 $$var = '';
} ?>
