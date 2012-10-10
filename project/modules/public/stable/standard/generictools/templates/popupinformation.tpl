<div class="{$params.divclass}" id="{$params.id}" style="{if $params.width}width: {$params.width}px; {/if}display:none;" rel="{$params.namespace}">
{if $params.popuptitle !== null}
	<div class="{$params.divclass}Title">{$params.popuptitle}</div>
{/if}
{$MAIN}
</div>