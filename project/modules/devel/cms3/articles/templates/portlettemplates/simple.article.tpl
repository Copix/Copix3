<div>
	{if $isSummary}
		<h2>{$article->summary_article}</h2>
		<br />
	{/if}
	{if $isContent}
		{$article->content_article}
		<br />
	{/if}
	{if $isDescription}
		{$article->description_hei}
	{/if}
</div>
