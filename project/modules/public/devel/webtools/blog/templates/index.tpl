{if $ppo->canwrite}
<a href="{copixurl dest="blog|admin|newticket"}"><img src="{copixresource path="img/tools/new.png"}" alt="{i18n key="copix:common.buttons.new"}"/>{i18n key="blog.new.ticket"}</a>
{/if}


{foreach from=$ppo->tickets item=ticket}
<div class="blog_ticket">
<p class="blog_date">{$ticket->human_date}<br /><a href="{copixurl dest="blog||" heading=$ticket->heading_blog}" title="{$ticket->heading_blog}">{$ticket->heading_blog}</a></p>
<h2>{$ticket->title_blog}{if $ppo->canwrite} - <a href="{copixurl dest="blog|admin|editticket" id=$ticket->id_blog}"><img src="{copixresource path="img/tools/update.png"}" /></a>{/if}</h2>
<p class="blog_index_content">
{$ticket->content_blog|html_substr:550:"..."}
</p>
<p>
{copixurl dest="blog||showticket" year=$ticket->year month=$ticket->month day=$ticket->day title=$ticket->title_blog assign=detailUrl}
<img src="{copixresource path="img/tools/open.png"}" /><a href="{$detailUrl}">lire la suite</a>
 {assign var=year value=$ticket->year}
 {assign var=month value=$ticket->month}
 {assign var=day value=$ticket->day}
 {assign var=title value=$ticket->title_blog} 
 {copixzone process="comments|comment" id="module;group;action=showticket;year=$year;month=$month;day=$day;title=$title" required=false moreUrl=$detailUrl}
</p>
</div>
{/foreach}

{if $ppo->canwrite}
<a href="{copixurl dest="blog|admin|newticket"}"><img src="{copixresource path="img/tools/new.png"}" alt="{i18n key="copix:common.buttons.new"}"/>{i18n key="blog.new.ticket"}</a>
{/if}

<div class="blog_pager">
{if $ppo->haveprev}
	<a href="{copixurl dest="blog||" heading=$ppo->heading tag=$ppo->tag page=$ppo->pagenum-1}">showprev</a>
{/if}
	{$ppo->pagenum}/{$ppo->count}
{if $ppo->havenext}
	<a href="{copixurl dest="blog||" heading=$ppo->heading tag=$ppo->tag page=$ppo->pagenum+1}">shownext</a>
{/if}
</div>

