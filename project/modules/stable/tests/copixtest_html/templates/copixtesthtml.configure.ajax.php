<div id="ConfigureTag_<?php echo $ppo->arData->id ?>">
<table class="CopixTable">
	<tr>
		<th class="key"> <?php _etag ('i18n', array ('key'=>'copixtest_html.configure.body')); ?>(<?php echo $ppo->arData->id ?>)</th>
	</tr>
	<tr>
		<td>
			<b><?php _etag ('i18n', array ('key'=>'copixtest_html.configure.path')); ?> </b><?php echo $ppo->arData->path ?><br />
			<b><?php _etag ('i18n', array ('key'=>'copixtest_html.configure.name_tag')); ?></b><?php echo $ppo->arData->name ?><br />
			<?php 
			if (is_string($ppo->arData->attributes)) { ?>
			<b><?php _etag ('i18n', array ('key'=>'copixtest_html.configure.attributes')); ?> </b><?php echo $ppo->arData->attributes ?><br />
			<?php } ?>
			<?php  if (isset($ppo->arData->contains)) { ?>
			<b> <?php _etag ('i18n', array ('key'=>'copixtest_html.configure.contains')); ?> </b>
			<?php echo $ppo->arData->contains ?>
			<?php } else { ?>
			<b> <?php _etag ('i18n', array ('key'=>'copixtest_html.configure.noattributes')); ?> </b>
			<?php } ?>
			<br /><br />
			<b><?php _etag ('i18n', array ('key'=>'copixtest_html.configure.checktype')); ?></b> 
			<br />
			<?php
				if ($ppo->arData->type !== null) {
					$var = 'checktype_'.$ppo->arData->type;
					$$var = 'checked';
					$modify = 'modify_'.$ppo->arData->id;
					$$modify = 'true';
					echo $checktype_notest;
					} else {
					$checktype_notest = "checked";
					if(isset($$modify)) {$$modify = 'false';}
				}
			?>
			<input type="hidden" name="body[]" value="<?php if($ppo->arData->id) {echo $ppo->arData->id;} ?>|<?php if(isset($ppo->arData->path)) {echo $ppo->arData->path ;} ?>|<?php if(isset($ppo->arData->name)) {echo $ppo->arData->name;} ?>|<?php if(isset($ppo->arData->attributes)) {echo $ppo->arData->attributes;} ?>|<?php if(isset($ppo->arData->contains)) {echo $ppo->arData->contains;} ?>">		
			<input type="radio" name="checktype_<?php echo $ppo->arData->id ?>" value="notest" <?php if(isset($checktype_notest)) { echo $checktype_notest; } ?>>
			<?php _etag ('i18n', array ('key'=>'copixtest_html.configure.notest')); ?><br />
			<input type="radio" name="checktype_<?php echo $ppo->arData->id ?>" value="simple" <?php if(isset($checktype_simple)) {echo $checktype_simple;} ?>>
			<?php _etag ('i18n', array ('key'=>'copixtest_html.configure.simple')); ?><br />
			<input type="radio" name="checktype_<?php echo $ppo->arData->id ?>" value="moderate" <?php if(isset($checktype_moderate)) {echo $checktype_moderate;} ?>>
			<?php _etag ('i18n', array ('key'=>'copixtest_html.configure.moderate')); ?><br />
			<input type="radio" name="checktype_<?php echo $ppo->arData->id ?>" value="absolute" <?php if(isset($checktype_absolute)) {echo $checktype_absolute;} ?>>
			<?php _etag ('i18n', array ('key'=>'copixtest_html.configure.absolute')); ?><br />
		</td>
	</tr>
	
	<tr>
		<th><div align="right"> <a href="javascript:hide('ConfigureTag_<?php echo $ppo->arData->id ?>');"> 
		<?php _etag ('i18n', array('key'=>'copixtest_html.configure.hide')) ?> </a> </div></th>
	</tr>
</table>
</div>
<br /> <br />
<?php if(isset($$var)) {
 $$var = '';
} ?>
