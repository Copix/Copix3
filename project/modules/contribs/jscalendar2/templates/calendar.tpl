<input type="text" id="{$ppo->id}" name="{$ppo->name}" value="{$ppo->value}" 
       size="{$ppo->size}" maxlength="{$ppo->maxlength}" style="{$ppo->style}" 
       tabindex="{$ppo->tabindex}"  class="{$ppo->class}"
       {$ppo->extra} />
{if !$ppo->interne}
	<img id="{$ppo->trigger->id}" class="{$ppo->trigger->class}" style="{$ppo->trigger->style}"
	     src="{copixresource path='jscalendar2|img/icon/jscalendar2.png'}" 
	     alt="{i18n key='jscalendar2|jscalendar2.selectedDate'}" 
	     {$ppo->trigger->extra}/>
{/if}

{if $ppo->lock}
<script type="text/javascript">
	//<!--
		$('{$ppo->id}').readOnly = true;
	//-->
</script>
{/if}