<div id="trackbacks">
<h2>{i18n key="trackback|trackback.list.title"}</h2>
<p class="trackback">
{i18n key="trackback|trackback.link"} {copixurl dest="trackback||tb" id=$id}
</p>
{foreach from=$trackbacks item=tb}
	<h3>{$tb->title_tb}</h3>
	<p><a href="{$tb->url_tb}">{$tb->blogname_tb}</a></p>
	<p class="trackback_content">
	{$tb->excerpt_tb}
	</p>
{/foreach}
</div>