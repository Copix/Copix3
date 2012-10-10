<?php
if (count ($ppo->arErrors) > 0) {
  $title = (count ($ppo->arErrors) == 1) ? _i18n ('wsserver.error') : _i18n ('wsserver.errors');

  echo '<div class="errorMessage">';
  echo '<h1>' . $title . '</h1>';
  _etag ('ulli', array ('values' => $ppo->arErrors));
  echo '</div>';
}
?>

<form name="wsserviceEdit"
	action="<?php echo _url ("wsserver|admin|exportClass") ?>"
	method="post">

<input type="hidden" name="confirm" value="1">
<input type="hidden" name="classFileName" value="<?php _etag ('escape', $ppo->classFileName); ?>">
<input type="hidden" name="moduleName" value="<?php _etag ('escape', $ppo->ModuleName);?>">

<table class="CopixVerticalTable">
<tr>
	<th><?php _etag ('i18n', 'wsserver.edit.module'); ?></th>
	<td><?php echo $ppo->ModuleName; ?></td>
</tr>
<tr>
	<th><?php _etag ('i18n', 'wsserver.edit.fileclass'); ?></th>
	<td><?php echo $ppo->classFileName; ?></td>
</tr>
<tr>
	<th><?php _etag ('i18n', 'wsserver.edit.class'); ?></th>
	<td><?php 
	if (count ($ppo->arClass) > 1) {
		echo '<select name="className">';
		foreach ($ppo->arClass as $className) {
			echo '<option value="'.$className.'">'.$className.'</option>';
		}
		echo '</select>';
	} else {
		echo $ppo->arClass[0];
		echo '<input type="hidden" name="className" value="'.$ppo->arClass[0].'">'; 
	} 
	?></td>
</tr>
<tr>
	<th><?php _etag ('i18n', 'wsserver.edit.name'); ?></th>
	<td><input type="text" name="serviceName"></td>
</tr>
</table>
<p><input type="submit"
	value="<?php _etag ('i18n', "copix:common.buttons.valid"); ?>" /></p>
</form>