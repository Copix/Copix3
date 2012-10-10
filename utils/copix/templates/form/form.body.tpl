
{if $title}
	<h1>{$title}</h1>
{/if}
{foreach from=$hiddenFields item=hiddenField name=hiddenFields}
	{$hiddenField->getRow()}
{/foreach}

<table class="CopixVerticalTable">
	{foreach from=$fields item=field name=fields}
		{if $field->getLabel()}
			{$field->getRow()}
		{else}
			{capture name="nolabelfields"}{$smarty.capture.nolabelfields}{$field->getRow()}{/capture}
		{/if}
	{/foreach}
</table>
<br/>
{$smarty.capture.nolabelfields}
<br/>
{if $legend}
	<p>{$legend}</p>
{/if}

{if $jsCode}
	<script type="text/javascript">
		{$jsCode}
	</script>
{/if}