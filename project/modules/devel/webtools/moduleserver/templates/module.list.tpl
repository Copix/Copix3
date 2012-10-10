
<table class="CopixTable">
<tr>
	<th>Module</th>
	<th>Action</th>
</tr>
{foreach from=$ppo->arModuleList item=module}
<tr>
	<td><a href="{copixurl dest='moduleserver|admin|exportConfirm' moduleName=$module}">{$module}</a></td>
	<td><a href="{copixurl dest='moduleserver|admin|exportConfirm' moduleName=$module}"><img src="{copixresource path='img/tools/add.png'}" /></a></td>
</tr>
{/foreach}