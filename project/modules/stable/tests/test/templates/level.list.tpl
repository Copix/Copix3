{if count ($ppo->arErrors)}
 <div class="errorMessage">
  <h1>Erreurs</h1>
  {ulli values=$ppo->arErrors}
 </div>
{/if}

<table class="CopixTable">
	<thead>
		<tr>
			<th>{i18n key='test.level.identifier'}</th>
			<th>{i18n key='test.level.caption'}</th>
			<th>{i18n key='test.level.contact'}</th>
			<th>{i18n key='test.level.actions'}</th>
		</tr>
	</thead>
	<tbody>
		{foreach from=$ppo->arData item=element}
		<tr {cycle values=',class="alternate"'}>
			<td>{$element->id_level}</td>
			<td>{$element->caption_level|escape}</td>
			<td>{if ($element->email) == null} aucune {else} {$element->email|escape} {/if}</td>
			<td>
			 <a href="{copixurl dest="adminlevel|edit" id=$element->id_level}">{copixicon type="update"}</a>
			 <a href="{copixurl dest="adminlevel|delete" id=$element->id_level}">{copixicon type="delete"}</a>
			</td>
		</tr>
		{foreachelse}
		<tr>
		 <td colspan="4">{i18n key='test.level.novalue'}</td>
		</tr>
		{/foreach}
	</tbody>
</table>

<a href="{copixurl dest="adminlevel|create"}">{copixicon type="new"}{i18n key='test.level.new'} </a>
<input type="button" style="width:100px" onclick="location.href='{copixurl dest="admin|default|"}'" value="{i18n key='test.historyback'}">