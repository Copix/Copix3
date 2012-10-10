{if $toShow->title}
<h2>{$toShow->title|escape:html}</h2>
{/if}
<ul>

{foreach from=$toShow->links item=link}
 <li><a href="#" onclick="window.open('{$link->linkDestination}')">{$link->linkName|escape:html}</a></li>
{/foreach}
</ul>