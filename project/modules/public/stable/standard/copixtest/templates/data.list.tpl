<table>
<tr>
 <th>Type</th>
 <th>Caption</th>
</tr>
{foreach from=$ppo->arData item=data}
<tr>
 <td>{$data->type_test}</td>
 <td>{$data->caption_typetest}</td>
</tr>
{/foreach}
</table>