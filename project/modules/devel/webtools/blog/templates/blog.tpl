{** Main Template for blog **}

{** Here the blog content, ticket or list **}
<div id="blog_mainview">
{$ppo->main_content}
</div>
{** now the panel **}
<div id="blog_panel">
	{$ppo->panel->calendar}
	<div id="blog_tags">
	<h3>{i18n key="blog.ticket.tags"}</h3>
	<p>	
	{$ppo->panel->tags}
	</p>
	</div>
	<div id="blog_headings">
	<h3>{i18n key="blog.headings"}</h3>
	<ul>
		<li><a href="{copixurl dest="blog||"}" title="{i18n key="blog.all.headings"}">{i18n key="blog.all.headings"}</a></li>
		{foreach from=$ppo->panel->headings item=heading}	
		<li><a href="{copixurl dest="blog||" heading=$heading->heading_blog}" title="{$heading->heading_blog}">{$heading->heading_blog}</a></li>
	{/foreach}
	</ul>
	</div>
</div>
