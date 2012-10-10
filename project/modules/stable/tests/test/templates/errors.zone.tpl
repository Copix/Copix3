{$HTML_HEAD}
{if ($data->result == false)}
<table class="CopixTable" id="error_{$tpl->arData->id_test}">
<thead>
	<tr>
	<th>{copixicon type="cancel"} <font color="#ff0000">{i18n key="test.launch.titleError"} {$data->caption_test} 	</font></th>
	</tr>
</thead>
<tbody>
{foreach from=$data->errors item=error}
<tr>
<td> {$error} </td>
</tr>
{/foreach}
</tbody>
</table>
{/if}
