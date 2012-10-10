<table class="CopixTable">
<!-- Affichage de l'en-tête -->
{foreach from=$results item=result}
		<tr {cycle values=",class='alternate'"}>
			{foreach from=$result item=field key=key}
				<th>{$key}&nbsp;</th>
			{/foreach}
			{php}break;{/php}
		</tr>
{/foreach}

<!-- Affichage du contenu -->
{foreach from=$results item=result}
		<tr {cycle values=",class='alternate'"}>
			{foreach from=$result item=field key=key}
				<td width="400">{$field}</td>
			{/foreach}
		</tr>
{/foreach}
</table>