<div id="wiki_arianwire">
    {foreach from=$ppo->arian item=link key=url} 
&gt; <a href="{copixurl dest="wiki||show" title=$link->title heading=$link->heading}">{$link->title}</a> 
	{/foreach}
</div>
{$ppo->translations}
<div id="wiki_content" class="wiki_content">
{$ppo->page->content_wiki}
</div>

<div id="wiki_nav_bar" class="wiki_nav_bar">
<h2>Page Informations</h2>
{if $ppo->page->author_wiki}
	<div class="wiki_author">
	{i18n key="wiki.page.last_modification" lastmodifier=$ppo->last_modifier date=$ppo->page->modificationdate_wiki|date_format:"%d/%m/%Y %H:%M:%S" }
	<br />
		{i18n key="wiki.page.author"}: <strong>{$ppo->original_author}</strong><br />
		{if count($ppo->contributors)}
			{i18n key="wiki.page.contribs"}:
			<ul>
			{foreach from=$ppo->contributors item=contributor}
				<li>{$contributor}</li>
			{/foreach}
			</ul>
		{/if}
	</div>
{/if}

{if $ppo->canedit}
	<a href="{copixurl dest="wiki|admin|edit" title=$ppo->page->title_wiki heading=$ppo->page->heading_wiki lang=$ppo->page->lang_wiki}" >{i18n key="wiki|wiki.edit.page"}</a>
	<p>
	<form method="POST" action="{copixurl dest="wiki|admin|edit"}">
	<h3>{i18n key="wiki.translation"}</h3>
	<input type="hidden" name="pagesource" value="{if $ppo->page->translatefrom_wiki}{$ppo->page->translatefrom_wiki}{else}{$ppo->page->title_wiki}{/if}" />
	<input type="hidden" name="fromlang" value="{if $ppo->page->translatefrom_wiki}{$ppo->page->fromlang_wiki}{else}{$ppo->page->lang_wiki}{/if}" />
	<input type="hidden" name="heading" value="{$ppo->page->heading_wiki}" />
	{i18n key="wiki.translate.title"}: <input type="text" name="title" value="{$ppo->page->title_wiki}"/><br />
	{i18n key="wiki.choose.language"}: 
	<select name="lang">
	{foreach from=$ppo->langs item=lang}
		<option value="{$lang}">{$lang}</option>
	{/foreach}
	</select>
	<input type="submit" value="OK" />
	</form> 
	</p>
{/if}
</div>

<div id="wiki_comments" class="wiki_comments">
{copixzone process="comments|comment" id="module;group;action;title" required=false}
</div>