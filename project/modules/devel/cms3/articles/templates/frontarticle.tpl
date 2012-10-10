<div>
	{if $ppo->article->summary_article}
		<h2>{$ppo->article->summary_article}</h2>
		<br />
	{/if}
	{if $ppo->article->content_article}
		{$ppo->article->content_article}
		<br />
	{/if}
	{if $ppo->article->description_hei}
		{$ppo->article->description_hei}
	{/if}
</div>
