<textarea id="{$ppo->name}" name="{$ppo->name}" cols="{$ppo->cols}" rows="{$ppo->rows}">
{$content}
</textarea>
{if $ppo->script}
<script type="text/javascript">
{$ppo->script}
</script>
{/if}