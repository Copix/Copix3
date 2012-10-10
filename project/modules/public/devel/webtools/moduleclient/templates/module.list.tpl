<form action="{copixurl dest='moduleclient|default|install'}" method=POST>
<table class="CopixTable">
<tr>
	<th></th>
	<th>Nom</th>
	<th>Version</th>
	<th>Description</th>
	<th></th>
</tr>
{foreach from=$ppo->distantModule item=module}
<tr>
	<td><input type="checkbox" name="id[]" value="{$module->id_export}|{$module->module_name}" {if $module->installed}checked="checked" disabled="disabled"{/if} /></td>
	<td>{$module->module_name}</td>
	<td>{$module->module_version}</td>
	<td>{$module->module_description}</td>
	<td></td>
</tr>
{/foreach}
</table>
<input type="submit" />
</form>