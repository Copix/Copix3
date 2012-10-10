{foreach from=$ppo->tags item=tag}
	<a href="{copixurl dest="blog||" tag=$tag->tagname|trim}" title="{$tag->rank}" style="font-size: {$tag->size}pt;">{$tag->tagname|trim}</a>
{/foreach}
	