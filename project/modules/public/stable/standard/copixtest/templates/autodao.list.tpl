<table>
<tr>
 <th>Titre</th>
 <th>Description</th>
</tr>
{foreach from=$ppo->arData item=data}
<tr>
 <td>{$data->titre_test}</td>
 <td>{$data->description_test}</td>
 <td><a href="{copixurl dest="autodao|getEdit" id_test=$data->id_test}">Modifier</a></td>
</tr>
{/foreach}
</table>