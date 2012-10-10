<h2 class="first">{i18n key="classpath.result.title.modifications"}</h2>
{if count ($ppo->added) == 0 && count ($ppo->edited) == 0 && count ($ppo->deleted) == 0}
	{i18n key="classpath.result.noModification"}
{else}
	<table class="CopixTable">
		<tr>
			<th>{i18n key="classpath.result.th.action"}</th>
			<th>{i18n key="classpath.result.th.class"}</th>
			<th>{i18n key="classpath.result.th.file"}</th>
		</tr>
		{foreach from=$ppo->added key=name item=path}
			<tr {cycle values=',class="alternate"' name="alternate"}>
				<td><font color="green">{i18n key="classpath.result.added"}</font></td>
				<td>{$name}</td>
				<td>{$path}</td>
			</tr>
		{/foreach}
		{foreach from=$ppo->edited key=name item=path}
			<tr {cycle values=',class="alternate"' name="alternate"}>
				<td><font color="green">{i18n key="classpath.result.edited"}</font></td>
				<td>{$name}</td>
				<td>{$path}</td>
			</tr>
		{/foreach}
		{foreach from=$ppo->deleted key=name item=path}
			<tr {cycle values=',class="alternate"' name="alternate"}>
				<td><font color="red">{i18n key="classpath.result.deleted"}<font></td>
				<td>{$name}</td>
				<td>{$path}</td>
			</tr>
		{/foreach}
	</table>
{/if}

<h2>{i18n key="classpath.result.title.dirs"}</h2>
<table class="CopixTable">
	<tr>
		<th>{i18n key="classpath.result.th.dir"}</th>
		<th width="50px">{i18n key="classpath.result.th.classes"}&nbsp;</th>
	</tr>
	{foreach from=$ppo->dirs key=dir item=classes}
		<tr {cycle values=',class="alternate"' name="alternate"}>
			<td>{$dir}</td>
			<td style="text-align: right">{$classes}</td>
		</tr>
	{/foreach}
</table>

<br />
{back url="admin||"}