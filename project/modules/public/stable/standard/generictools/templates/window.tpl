<div class="copixwindow" id="{$ppo.id}" style="{if $ppo.height}height: {$ppo.height}px; {/if}{if $ppo.width}width: {$ppo.width}px; {/if}display:none;">
	<div class="copixwindow_title">
		<div style="float:left;">{$ppo.title}</div>
		<div style="text-align:right;padding-right:2px;">
	    	<img onclick="javascript:$('{$ppo.id}').fireEvent('close');" style="cursor:pointer" src="{copixresource path="img/tools/close.jpg"}" alt="fermer" />
	    </div>
	</div>
	<div class="copixwindow_content">
	{$MAIN}
	</div>
</div>