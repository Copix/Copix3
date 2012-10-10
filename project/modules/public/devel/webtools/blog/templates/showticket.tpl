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
{$ppo->ticket->content_blog}
<div id="blog_footpane">
	<div>
	{copixzone process="comments|comment" id="module;group;action;year;month;day;title" mode=list required=false}
	</div>
	<div>
	{assign var=id value=$ppo->ticket->id_blog}
	{copixzone process="trackback|trackback" id="blog/$id"}
	</div>
</div>