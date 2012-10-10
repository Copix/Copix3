{if $isTitre}
	{assign var=link_text value=$document->caption_hei}
{else}
	{if $isDescription && $document->description_hei}
		{assign var=link_text value=$document->description_hei}
	{else}
		{assign var=link_text value="Voir le fichier `$type`"}
	{/if}
{/if}
<div class="document {$type}">
	{if $isContenu}
	<a href="{copixurl dest='heading||' public_id=$document->public_id_hei content_disposition=$content_disposition}?{$document->date_update_hei}"
		target="_blank"
		{if $content_disposition eq 'inline'}		
		title="{$link_text} {$type}, {$filesize} (nouvelle fenêtre)"
		{else}
		title="{$link_text} {$type}, {$filesize}"
		{/if}
	>
	{/if}
		{$link_text}
		{if $isTitre && $isDescription && $document->description_hei}
			<br />
			{$document->description_hei}
		{/if}
	{if $isContenu}
	</a>
	{/if}
</div>