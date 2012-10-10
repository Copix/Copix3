
<table class="CopixVerticalTable">
{foreach from=$fields item=field name=fields}
{if $field->getLabel()}
<tr>
	<th>{$field->getLabel()}</th>
	<td>{$field->getHTML()} {$field->getErrors()}</td>
</tr>
{else}
{capture name="nolabelfields"}{$smarty.capture.nolabelfields}{$field->getHTML()}{$field->getErrors()}{/capture}
{/if}
{/foreach}
</table>
{$smarty.capture.nolabelfields}