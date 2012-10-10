<table>
	<tr>
	{if $isDescription}
		<td>
			{$document->description_hei}
		</td>
	{/if}
	{if $isContenu}
		<td>
			<a href="{copixurl dest='heading||' public_id=$document->public_id_hei}" >voir le fichier</a>
		</td>
	{/if}
	</tr>
</table>