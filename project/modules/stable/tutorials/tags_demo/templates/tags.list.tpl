<ul>
{foreach from=$ppo->arTags item=tag}
 <li><a href="{copixurl dest=$tag}">{$tag}</a></li>
{/foreach}
</ul>