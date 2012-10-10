<div id="wiki_arianwire">
    {foreach from=$ppo->arian item=link key=url} 
&gt; <a href="{copixurl dest="wiki||show" title=$link->title heading=$link->heading}">{$link->title}</a> 
	{/foreach}
</div>
{$ppo->translations}
<div id="wiki_content" class="wiki_content">
{$ppo->page->content_wiki}
</div>
{if $ppo->canedit}
<div id="wiki_nav_bar" class="wiki_nav_bar">
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
</div>
{/if}