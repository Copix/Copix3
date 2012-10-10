{if count ($ppo->proxysEnabled) > 0}
	<h2 class="first">{i18n key="proxy.list.title.proxysEnabled"}</h2>
	<table class="CopixTable">
		<tr>
			<th width="200px">{i18n key="proxy.list.id"}</th>
			<th>{i18n key="proxy.list.host"}</th>
			<th width="60px">{i18n key="proxy.list.port"}</th>
			<th width="60px">{i18n key="proxy.list.actions"}</th>
		</tr>
		{foreach from=$ppo->proxysEnabled item=proxyInfos key=proxyID}
			<tr {cycle values=',class="alternate"' name="alternate"}>
				<td>{$proxyID}</td>
				<td>{$proxyInfos.host}</td>
				<td>{$proxyInfos.port}</td>
				<td align="right">
					<a href="{copixurl dest="admin|proxy|edit" proxy="$proxyID"}"
					><img src="{copixresource path="img/tools/select.png"}" alt="{i18n key="proxy.list.edit"}" title="{i18n key="proxy.list.edit"}"
					/></a>
					<a href="{copixurl dest="admin|proxy|disable" proxy="$proxyID"}"
					><img src="{copixresource path="img/tools/enable.png"}" alt="{i18n key="proxy.list.disable"}" title="{i18n key="proxy.list.disable"}"
					/></a>
					<a href="{copixurl dest="admin|proxy|delete" proxy="$proxyID"}"
					><img src="{copixresource path="img/tools/delete.png"}" alt="{i18n key="proxy.list.edit"}" title="{i18n key="proxy.list.delete"}"
					/></a>
				</td>
			</tr>
		{/foreach}
	</table>
{/if}

{if count ($ppo->proxysDisabled) > 0}
	<h2{if count ($ppo->proxysEnabled) == 0} class="first"{/if}>{i18n key="proxy.list.title.proxysDisabled"}</h2>
	<table class="CopixTable">
		<tr>
			<th width="200px">{i18n key="proxy.list.id"}</th>
			<th>{i18n key="proxy.list.host"}</th>
			<th width="60px">{i18n key="proxy.list.port"}</th>
			<th width="60px">{i18n key="proxy.list.actions"}</th>
		</tr>
		{foreach from=$ppo->proxysDisabled item=proxyInfos key=proxyID}
			<tr {cycle values=',class="alternate"' name="alternate"}>
				<td>{$proxyID}</td>
				<td>{$proxyInfos.host}</td>
				<td>{$proxyInfos.port}</td>
				<td align="right">
					<a href="{copixurl dest="admin|proxy|edit" proxy="$proxyID"}"
					><img src="{copixresource path="img/tools/select.png"}" alt="{i18n key="proxy.list.edit"}" title="{i18n key="proxy.list.edit"}"
					/></a>
					<a href="{copixurl dest="admin|proxy|enable" proxy="$proxyID"}"
					><img src="{copixresource path="img/tools/disable.png"}" alt="{i18n key="proxy.list.enable"}" title="{i18n key="proxy.list.enable"}"
					/></a>
					<a href="{copixurl dest="admin|proxy|delete" proxy="$proxyID"}"
					><img src="{copixresource path="img/tools/delete.png"}" alt="{i18n key="proxy.list.edit"}" title="{i18n key="proxy.list.delete"}"
					/></a>
				</td>
			</tr>
		{/foreach}
	</table>
{/if}

{if count ($ppo->proxysEnabled) == 0 && count ($ppo->proxysDisabled) == 0}
	{i18n key="proxy.list.noProxy"}
	<br />
{/if}

<br />
<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr>
		<td><a href="{copixurl dest="admin|proxy|edit"}"><img src="{copixresource path="img/tools/add.png"}" alt="{i18n key="proxy.list.add"}" /> {i18n key="proxy.list.add"}</a></td>
	</tr>
</table>
{back url="admin||"}