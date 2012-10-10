{foreach from=$toShow->links item=link}
<p>
<iframe width="100%" height="800" src="{$link->linkDestination}" style="border: 0px none;">
Votre navigateur ne gère pas les iframe
</iframe>
</p>
<p>
</p>
{/foreach}
