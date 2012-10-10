<center>
<a href="{copixurl dest="copixtools|session|" showNamespace=""}"
><img src="{copixresource path="img/tools/show.png"}" /> {i18n key="session.showAllNamespaces"}</a>
&nbsp;&nbsp;&nbsp;<a href="{copixurl dest="copixtools|session|DeleteSession"}"
><img src="{copixresource path="img/tools/delete.png"}" /> {i18n key="session.deleteSession"}</a>
</center>

{foreach from=$ppo->arSessionCopix key=nameSpace item=session}
	<h2>{i18n key=session.namespace 0=$nameSpace}</h2>
	<center>
	<a href="{copixurl dest="copixtools|session|" showNamespace=$nameSpace}"
	><img src="{copixresource path="img/tools/show.png"}" /> {i18n key="session.showNamespace"}</a>
	&nbsp;&nbsp;&nbsp;<a href="{copixurl dest="copixtools|session|delete" namespace=$nameSpace}"
	><img src="{copixresource path="img/tools/delete.png"}" /> {i18n key="session.deleteNamespace"}</a>
	</center>
	<br />
	<table class="CopixTable">
		<tr>
			<th>{i18n key="session.varName"}</th>
			<th>{i18n key="session.varContent"}</th>
			<th width="20px"></th>
		</tr>
		{foreach from=$session key=sessionVarName item=sessionVarContent}
			<tr {cycle values=',class="alternate"' name="alternate"}>
				<td width="150">{$sessionVarName}</td>
				<td>
					<div id="value_{$nameSpace}_{$sessionVarName}" style="white-space: pre">{$sessionVarContent|@var_dump}</div>
					<div id="update_{$nameSpace}_{$sessionVarName}"></div>
				</td>
				<td style="text-align: center">
					<a href="{copixurl dest="session|delete" var=$sessionVarName namespace=$nameSpace}"
						><img src="{copixresource path=img/tools/delete.png}" alt="{i18n key="session.deleteVar"}" title="{i18n key="session.deleteVar"}"
					/></a>
					</td>
			</tr>
		{/foreach}
	</table>
{/foreach}

<br />
{back dest="admin||" tagalign="right"}