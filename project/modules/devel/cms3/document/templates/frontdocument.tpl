<table>
	<tr>
	{if $ppo->document->description_hei}
		<td>
			{$ppo->document->description_hei}
		</td>
	{/if}
	{if $ppo->document->file_document}
		<td>
			<a href="{copixurl dest='document|documentfront|ShowDocumentFile' public_id_hei=$ppo->document->public_id_hei}" >voir le fichier</a>
		</td>
	{/if}
	</tr>
</table>
