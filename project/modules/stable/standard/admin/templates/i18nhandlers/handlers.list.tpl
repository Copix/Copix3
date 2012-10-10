<div class="errorMessage">
<h1>{i18n key=copix:common.buttons.warning}</h1>
<ul>
 <li>{i18n key="admin|i18n.handler.adminalert"}</li>
 <li>{i18n key="admin|i18n.handler.generalAlert"}</li>
 <li>{i18n key="admin|i18n.handler.saveConfiguration"}</li>
</ul>
</div>


<form method='post' action='{copixurl dest='admin|i18nHandlers|saveHandlers'}'>
<table class="CopixTable">
	<tr>
		<th>{i18n key="i18n.handler"}</th>
		<th>{i18n key="copix:common.actions.title"}</th>
	</tr>
{foreach from=$ppo->handlers item=handler} 
	<tr {cycle values='class="alternate",'}>
		<td><label for="{$handler.name}">{$handler.caption}</label></td><td><input type="checkbox" id="{$handler.name}" name="handlers[]" value="{$handler.name}" {if $handler.active}checked="checked"{/if} /></td>
	</tr>
{/foreach}
</table>
<p>
	<input type="submit" value="{i18n key="copix:common.buttons.valid"}" />
</p>
</form>
{back url="admin||"}