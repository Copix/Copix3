{* On affiche les Sessions Copix *}
{if count ($ppo->arSessionCopix)}
<h2>{i18n key=session.copix}</h2>
<table class="CopixTable">
<thead>
 <tr>
  <th>{i18n key=session.varName}</th>
  <th>{i18n key=session.varContent}</th>
  <th>{i18n key=copix:common.title.actions}</th>
 </tr>
</thead>
<tbody>
{foreach from=$ppo->arSessionCopix key=nameSpace item=session}
  <tr>
   <th colspan = "2">{i18n key=session.namespace} {$nameSpace}</th>
   <th><a href="{copixurl dest="session|remove" for_namespace=$nameSpace}"><img src="{copixresource path=img/tools/delete.png}" ?></a></td>
  </tr>
	{foreach from=$session key=sessionVarName item=sessionVarContent}
	<tr {cycle values=',class="alternate"' name="module"}>
	 <td>{$sessionVarName}</td>
	 <td>{$sessionVarContent|@var_dump}</td>
	 <td><a href="{copixurl dest="session|remove" key=$sessionVarName for_namespace=$nameSpace}"><img
				src="{copixresource path=img/tools/delete.png}"?></a></td>
	</tr>
	{/foreach}
{/foreach}
</tbody>
</table>
{/if}

{if count ($ppo->arSession)}
<h2>{i18n key=session.others}</h2>
<table class="CopixTable">
<thead>
 <tr>
  <th>{i18n key=session.varName}</th>
  <th>{i18n key=session.varContent}</th>
  <th>{i18n key=copix:common.title.actions}</th>
 </tr>
</thead>
<tbody>
	{foreach from=$ppo->arSession key=sessionVarName item=sessionVarContent}
	<tr {cycle values=',class="alternate"'}>
	 <td>{$sessionVarName}</td>
	 <td>{$sessionVarContent|@var_dump}</td>
	 <td><a href="{copixurl dest="session|remove" key=$sessionVarName}"><img src="{copixresource path=img/tools/delete.png}" ?></a></td>
	</tr>
	{/foreach}
</tbody>
</table>
{/if}

{if $ppo->otherOperations}
<h2>{i18n key=session.otherOperations}</h2>
<table class="CopixVerticalTable">
 <tr>
  <td>{i18n key='session.destroy'}</td>
  <td><a href="{copixurl dest="session|destroy"}"><img src="{copixresource path="img/tools/select.png"}" alt="{i18n key="copix:common.buttons.select"}" /></a></td>
 </tr>
 <tr>
  <td>{i18n key='session.popup'}</td>
  <td><a href="{copixurl dest="session|popup"}" target="_blank" ><img src="{copixresource path="img/tools/select.png"}" alt="{i18n key="copix:common.buttons.select"}" /></a></td>
 </tr> 
</table>
{/if}
<a href="{copixurl dest="admin||"}"><input type="button" value="{i18n key='copix:common.buttons.back'}" /></a>