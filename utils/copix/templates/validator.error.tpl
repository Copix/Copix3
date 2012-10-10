{foreach from=$errors item=error name=errors}
{$error}{if !$smarty.foreach.errors.last}<br />{/if}
{/foreach}