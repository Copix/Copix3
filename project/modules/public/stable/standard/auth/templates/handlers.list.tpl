<div class="errorMessage">
<h1>{i18n key=copix:common.buttons.warning}</h1>
{assign var=key value=$ppo->type}
<ul>
 <li>{i18n key="auth|auth.handler.$key.adminalert"}</li>
 <li>{i18n key="auth|auth.handler.generalAlert"}</li>
 <li>{i18n key="auth.handler.saveConfiguration"}</li>
</ul>
</div>


<form method='POST' action='{copixurl dest='auth|admin|saveHandlers' type=$ppo->type}'>
<table class="CopixTable">
	<tr>
		<th>{i18n key="auth.handler"}</th>
		<th>{i18n key="copix:common.actions.title"}</th>
	</tr>
{foreach from=$ppo->handlers item=active key=handler} 
	<tr {cycle values='class="alternate",'}>
		<td><label for="{$handler}">{$handler}</label></td><td><input type="checkbox" id="{$handler}" name="handlers[]" value="{$handler}" {if $active}checked="checked"{/if} /></td>
	</tr>
{/foreach}
</table>
<input type="submit" value="{i18n key="copix:common.buttons.valid"}" />
<a href="{copixurl dest="admin||"}"> <input type="button" value="{i18n key="copix:common.buttons.back"}" /></a>
</form>