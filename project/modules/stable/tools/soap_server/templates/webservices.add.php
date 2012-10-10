<?php
if (count ($ppo->arErrors) > 0) {
  $title = (count ($ppo->arErrors) == 1) ? _i18n ('soap_server.error') : _i18n ('soap_server.errors');

  echo '<div class="errorMessage">';
  echo '<h1>' . $title . '</h1>';
  _etag ('ulli', array ('values' => $ppo->arErrors));
  echo '</div>';
}
?>

<form name="wsserviceEdit"
	action="<?php echo _url ("soap_server|admin|doExport") ?>"
	method="post">

<table class="CopixVerticalTable">
<tr>
	<th><?php _etag ('i18n', 'soap_server.edit.module'); ?></th>
	<td><?php echo $ppo->module; ?>|<?php echo $ppo->class; ?></td>
</tr>
<tr>
	<th><?php _etag ('i18n', 'soap_server.edit.class'); ?></th>
	<td><?php 
	if (count ($ppo->arClass) > 1) {
		echo '<select name="class">';
		foreach ($ppo->arClass as $className) {
			echo '<option value="'.$ppo->module.'|'.$ppo->class.'|'.$className.'">'.$className.'</option>';
		}
		echo '</select>';
	} else {
		echo $ppo->arClass[0];
		echo '<input type="hidden" name="class" value="'.$ppo->module.'|'.$ppo->class.'|'.$ppo->arClass[0].'">'; 
	} 
	?></td>
</tr>
<tr>
	<th><?php _etag ('i18n', 'soap_server.edit.name'); ?></th>
	<td><input type="text" name="name"></td>
</tr>
</table>
<p><input type="submit"
	value="<?php _etag ('i18n', "copix:common.buttons.valid"); ?>" /></p>
</form>