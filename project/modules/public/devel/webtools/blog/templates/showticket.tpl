<div class="blog_fullview">
{if $ppo->canwrite}<p>
<a href="{copixurl dest="blog|admin|editticket" id=$ppo->ticket->id_blog}"><img src="{copixresource path="img/tools/update.png"}" /></a>
 - <a href="{copixurl 
	dest="trackback||sendForm" 
	title=$ppo->ticket->title_blog 
	excerpt=$ppo->ticket->content_blog
	url=$ppo->url
	}">{i18n key="trackback|trackback.add"}</a>
</p>
{/if}
<h2>{$ppo->ticket->title_blog}</h2>
<div>
Tags:: 
{assign var=flag value=false}
{foreach from=$ppo->ticket->_arTags item=tag}
	{if $flag} :: {/if}
	<a href="{copixurl dest="blog|default|default" tag=$tag}" title="Tag::{$tag}">{$tag}</a>
	{assign var=flag value=true}
{/foreach}
</div>
<div>
{$ppo->ticket->content_blog}
</div>
<div id="blog_footpane">
	<div>
	{assign var=year value=$ppo->year}
	{assign var=month value=$ppo->month}
	{assign var=day value=$ppo->day}
	{assign var=title value=$ppo->ticket->title_blog} 
	{copixzone process="comments|comment" zoneParams_id="module=blog;group=;action=showticket;year=$year;month=$month;day=$day;title=$title" title=$title mode=list required=false}
	</div>
	<div>
	{assign var=id value=$ppo->ticket->id_blog}
	{copixzone process="trackback|trackback" zoneParams_id="blog/$id"}
	</div>
</div>
</div>
