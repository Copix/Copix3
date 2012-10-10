{if $toShow->title}
 <h2>{$toShow->title|escape:html}</h2>
{/if}

{if count ($toShow->links)}
<ul>
{foreach from=$toShow->links item=link}
 <li><a href="{$link->linkDestination}">{$link->linkName|escape:html}</a></li>
{/foreach}
</ul>
{/if}