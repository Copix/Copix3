<table class="CopixTable">
<!-- Affichage des en-têtes -->
<tr>
{foreach from=$results item=result}
	{foreach from=$result item=field key=key}
		<th><input type="checkbox" name="fields[]" value="{$key}"/></th><th>{$key}</th>
	{/foreach}
	{php}break;{/php}
{/foreach}
</tr>

<!-- Affichage des valeurs -->
{foreach from=$results item=result}
	<tr {cycle values=",class='alternate'"}>
		{foreach from=$result item=field key=key}
			<td colspan="2" width="400">{$field}</td>
		{/foreach}
	</tr>
{/foreach}
</table>
<br />
