{if count ($ppo->arErrors)}
<div class="errorMessage">
<h1>Erreurs</h1>
{ulli values=$ppo->arErrors}
</div>
{/if}

<form name="form" action="{copixurl dest="adminCategory|Save"}" >
<table class="CopixTable">
	<thead>
		<tr>
			<th>{i18n key='test.categories.caption'}</th>
			<th>{i18n key='test.categories.actions'}</th>
		</tr>
	</thead>
	<tbody>
		{foreach from=$ppo->categories item=element}
		<tr {cycle values=',class="alternate"'}>
			<td>
			{if (isset($element->caption_ctest) && $element->id_ctest !== $ppo->edit->id_ctest)}
			{$element->caption_ctest}
			{else}
			<input type="hidden" name="idc" value="{$element->id_ctest}" />
			<input type="text" name="caption" value="{$element->caption_ctest}" />
			{/if}
			</td>
			{if (isset($element->caption_ctest) && $element->id_ctest !== $ppo->edit->id_ctest)}
			<td><a href="{copixurl 
			dest="adminCategory|edit" idc=$element->id_ctest}">
		 	{copixicon type="update"} </a><a href="{copixurl dest="adminCategory|delete" idc=$element->id_ctest}">
			{copixicon type="delete"} </a> </td>
		 	{else}
		 	<td>
		 	<a href="javascript:document.form.submit();" name="envoyer">
		 		{copixicon type="valid"}
		 	 </a>
		 	 <a href="{copixurl dest="adminCategory|delete" idc=$element->id_ctest}">
			{copixicon type="delete"} </a>
		 	</td>
		 	{/if}
		</tr>
		{/foreach}
	    <tr>
	     <td><input type="text" name="caption_ctest" value="{$ppo->caption_ctest}"></td>
		 <td><input type="image" name="envoyer" value="{i18n key=copix:common.buttons.save}" src="{copixresource path='img/tools/save.png'}" ></td>
	    </tr>
	</tbody>
</table>
</form>

<input type="button" onclick=location.href='{copixurl dest="admin|default|"}' style="width:100px" value="{i18n key='test.historyback'}">