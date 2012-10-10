<table class="CopixTable">
	<thead>
		<tr>
			<th>{i18n key='test.launchdetails.step'}</th>
			<th>{i18n key='test.launchdetails.result'}</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		{foreach from=$ppo->results item=element key=id}
		<tr>
			<td>{$id}</td>
			<td>{if ($element[1] == true) } <font color="green"><b>{i18n key='test.launchdetails.success'}</b></font> {else} <font color="red"><b>{$element[0]}<b></font>
			{/if}</td>
			<td>
			<div align="right">{if ($element[1] == true) } {copixicon type="ok"}
			{else} {copixicon type="delete"} {/if}</div>
			</td>
		</tr>
		{/foreach}
	</tbody>
</table>
<br />
<input type="button" onclick=location.href='{copixurl dest="admin|default"}' style="width:100px" value="{i18n key='test.historyback'}">