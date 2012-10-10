<h2>{$ppo->nbResult} {i18n key="index_search.result"}</h2>
{foreach from=$ppo->listResults item=result}
<div class="searchResult">
	<h3><a href="{$result->url}">{$result->caption|escape}</a></h3>
	<p class="detail">{$result->detail}</p>
	<p class="cached">
	    {if $ppo->showCachedAndText}
			<a href="{copixurl dest='index_search||getFileCache' id=$result->idobject}">{i18n key='index_search.filecache'}</a>
			{if $result->type ne 'txt' && $result->type ne 'txtbrute'}
				- <a href="{copixurl dest='index_search||getFileText' id=$result->idobject}">{i18n key='index_search.filetxt'}</a>
			{/if}
		{/if}
	</p>
</div>
{/foreach}

{if $ppo->similarWord && $ppo->nbResult < 5}
<h2>Mots similaires</h2>
	{foreach item=word from=$ppo->similarWord name=mots}
	<a href="{copixurl dest=$ppo->url criteria=$word theme=$ppo->theme}" title="Chercher {$word} sur le site">{$word}</a>{if not $smarty.foreach.mots.last}, {/if}
	{/foreach}
{/if}

{if $ppo->maxPage != 1}
	<div class="pagination">
	{if $ppo->loopStart > 1}
	    <a href="{copixurl dest=$ppo->url criteria=$ppo->criteria page=1 theme=$ppo->theme}">1</a>
		{if $ppo->loopStart > 2}
			<span class="ellipse">...</span>
		{/if}
	{/if}

	{section name=i start=$ppo->loopStart loop=$ppo->loopEnd+1}
		{if $smarty.section.i.index != $ppo->currentPage}
			<a href="{copixurl dest=$ppo->url criteria=$ppo->criteria page=$smarty.section.i.index theme=$ppo->theme}">{$smarty.section.i.index}</a>
		{else}
			<span class="current">{$ppo->currentPage}</span>
		{/if}
	{/section}

	{if $ppo->loopEnd != $ppo->maxPage}
		{if $ppo->loopEnd + 1 != $ppo->maxPage}
			<span class="ellipse">...</span>
		{/if}
	    <a href="{copixurl dest=$ppo->url criteria=$ppo->criteria page=$ppo->maxPage theme=$ppo->theme}">{$ppo->maxPage}</a>
	{/if}
	</div>
{/if}