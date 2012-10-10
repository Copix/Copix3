<h2>{i18n key="wiki.page.not.found"}</h2>
<p>
{i18n key="wiki.page.maybe.in.list"}
</p>
{if $ppo->canwrite}
<p>
{i18n key="wiki.create.anyway"}
<a href="{copixurl dest="wiki|admin|edit" title=$ppo->title heading=$ppo->heading lang=$ppo->lang}">{i18n key="wiki.create.page"}</a>
</p>
{/if}
<ul>
{foreach from=$ppo->pages item=page}
<li><a href="{copixurl dest="wiki||show" title=$page->title_wiki heading=$page->heading_wiki lang=$page->lang_wiki}">{if $page->displayedtitle_wiki}{$page->displayedtitle_wiki}{else}{$page->title_wiki}{/if}</a> - 
{if $page->heading_wiki}
	{i18n key="wiki.heading"} {$page->heading_wiki}
{else}
	{i18n key="wiki.no.heading"} 
{/if}
 - {i18n key="wiki.languages"} {$page->lang_wiki}</li>
{/foreach}
</ul>

