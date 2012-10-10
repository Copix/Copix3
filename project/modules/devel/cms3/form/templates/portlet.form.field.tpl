<script>
$('{$target_id}').innerHTML = '{html_checkboxes name="$name" options=$fields separator="<br />"}';
$$('#option_div_{$identifiantFormulaire} input').each (function (el){ldelim}
	el.addEvent('click', 
	function (){ldelim}
		updateFormulaire('{$identifiantFormulaire}', '{$identifiantFormulaire}', '{"editId"|request}');
	{rdelim}
	)
{rdelim});
</script>