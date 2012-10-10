<?php _tag('mootools');
CopixHTMLHeader::addJSLink(_resource ('|js/showhide.js'));
CopixHTMLHeader::addCSSLink(_resource ('|styles/styles.css'));
?>
<table class="CopixTable">
 <thead>
  <tr>
   <th><?php _etag ('i18n', array('key' => 'test_soap.configure.functionName')) ?></th>
   <th><?php _etag ('i18n', array('key' => 'test_soap.configure.verifyFunction')) ?></th>
   <th><?php _etag('copixicon', array ('type'=>'select')) ?></th>
  </tr>
 </thead>
 <tbody>
 <?php foreach ($ppo->arData as $key => $value) { ?>
  <tr>
   <td class="function"> <?php echo $value ?>  </td>
			<td><a href="#" onclick="show('configure_<?php echo $key ?>');"> <?php _eTag('copixicon', array('type' => 'update')) ?> </a></td>
			<td>
			<?php
			if ($ppo->previousValues) {
				foreach ($ppo->previousValues as $key_2 => $value_2) {
					if ($value_2->name_function === $value) {
						_etag ('copixicon', array ('type'=>'valid'));
					}
				}
			}
			?>
			 </td>
		</tr>
	<?php } ?>
	</tbody>
</table>

<form action="<?php echo CopixUrl::get ('admin|Save') ?>" method="POST">
<?php foreach ($ppo->arData as $key => $value) { ?>
<div id="configure_<?php echo $key ?>" style="display:none">
<table class="CopixTable">
	<thead>
		<tr>
			<th ><div class="functionTitle"> <?php _etag ('i18n', array('key' => 'test_soap.configure.testConfiguration')) ?> <?php echo $value ?></div> </th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>
			<?php
				if ($ppo->previousValues) {
					foreach ($ppo->previousValues as $functionValue) { 
						if ($value === $functionValue->name_function) {
							$check = $functionValue->checktype;
							$var = 'checktype_'.$check;
							$$var = 'checked';
							break;
						}
						else {
							$checktype_notest = "checked";
							if (isset($var)) {
							$var = 'checktype_'.$check;
							$$var = '';
							}
						}
					}
				} else {
					$checktype_notest = "checked";
					if (isset($var)) {
						$var = 'checktype_'.$check;
						$$var = '';
					}
				}
			?>
				<?php _etag ('i18n', array('key' => 'test_soap.configure.checkType')) ?> <br />
				<input type="radio" name="checktype_<?php echo $key ?>" value="notest" <?php if(isset($checktype_notest)) {echo $checktype_notest;} ?>>
				<?php _etag ('i18n', array('key' => 'test_soap.configure.notest')) ?><br />		
				<input type="radio" name="checktype_<?php echo $key ?>" value="simple" <?php if(isset($checktype_simple)) {echo $checktype_simple;} ?>>
				<?php _etag ('i18n', array('key' => 'test_soap.configure.simple')) ?><br />
			</td>
		</tr>
		<tr>
			<th> <div align="right"> <a href="#" onclick="hide('configure_<?php echo $key ?>');"> <?php _etag ('i18n', array('key' => 'test_soap.configure.hide')) ?> </a> </div> </th>
		</tr>
	</tbody>
</table>
</div>
<?php } ?>
<div align="center">
	<input type="submit" name="send" value="<?php _etag ('i18n', array('key' => 'test_soap.configure.submit')) ?>" />
	<a href="<?php _resource('cancel') ?>">
     <input type="button" style="width:100px" value="<?php _etag ('i18n', array ('key' => 'test_soap.edit.cancel')) ?>" />
    </a>
</div>
</form>

<a href="javascript:history.back();"><input type="button" style="width:100px" value="<?php _etag('i18n', array ('key' => 'test_soap.historyback')) ?>">
</a>