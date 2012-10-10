<div id="form_field_line_{$ppo->cfe_id}" style="float:left;margin:0px 5px;">
	<span style="float:left;padding:0px 10px;">
		<input id="cb_form_field_{$ppo->cfe_id}" name="form_field[]" type='checkbox' 
			   value="{$ppo->cfe_id}" onclick="addRemoveField({$ppo->cfe_id})" />
	</span>
	<div id="cfe_label_{$ppo->cfe_id}" style="float:left;cursor:pointer;width:100px;" onclick="updateElement({$ppo->cfe_id})">
		{$ppo->cfe_label}
	</div>
</div>

<script>
	myNewElementSlide.slideOut();
</script>