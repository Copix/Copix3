<table>
	{foreach from=$arData key=label item=data}
		<tr class="{cycle values='alternate,'}">
			<th>{$label} : </th>
			<td>
{if is_array($data)}
				{$data|@implode:","}
{else}
				{$data}
{/if}
			</td>
		</tr>
	{/foreach}
<table>
