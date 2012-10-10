{mootools}
<h2 class="first">{i18n key="install.title.installedModules"}</h2>
<div style="text-align: center">
	{assign var=countGroups value=$groupsInstalled|@count}
	{assign var=index value=0}
	{foreach from=$groupsInstalled key=groupId item=group}
		{assign var=index value=$index+1}
		<a href="{copixurl dest="admin|install|manageModules" group=$groupId}" onclick="showModuleGroupInstalled ('installedModules{$groupId}'); return false;">{$group.caption} ({$group.count})</a>
		{if $index < $countGroups} - {/if}
	{/foreach}
</div>
<br />
{assign var=isFirst value=true}
{foreach from=$arModules.installed key=groupId item=modules}
	{if $isFirst}{assign var=exModuleGroupInstalled value=$groupId}{/if}
	<div id="installedModules{$groupId}" {if !$isFirst}style="display: none"{/if}>
		<table class="CopixTable">
			<tr>
				<th colspan="2">{i18n key=install.titleTab.module}</th>
				<th>{i18n key=install.titleTab.name}</th>
				<th>{i18n key=install.titleTab.version}</th>
				<th colspan="3"></th>
			</tr>
			{foreach from=$modules item=module}
				<tr class="{cycle values=",alternate"}" title="module{$module->name}">
					<td class="detailmodule" title="module{$module->name}" style="width:20px;" valign="top" align="center">
						{if ($module->icon)}
							<img src="{$module->icon}" alt="{$module->name}" title="{$module->name}" style="margin-top: 3px" />
						{/if}
					</td>
					<td class="detailmodule" title="module{$module->name}" style="width:110px;" valign="top"><div style="margin-top: 3px">{$module->name}</div></td>
					<td class="detailmodule" title="module{$module->name}" valign="top">
						<div style="margin-top: 3px">{$module->description|default:$module->name|escape}</div>
						{assign var=idSufix value=$module->name}
						{copixzone id="module$idSufix" process='admin|detailmodule' moduleName=$module->name ajax="true"}
					</td>
					<td align="right" valign="top"><div style="width:55px; margin-top: 3px">{$module->installedVersion}</div></td>
					<td class="action" valign="top">
						{if $module->haveConfig}
							<a href="{copixurl dest='admin|parameters|edit' choiceModule=$module->name}" title="{i18n key='install.alt.config'}">
								<img src="{copixresource path='img/tools/config.png'}" alt="{i18n key='install.alt.config'}" />
							</a>
						{/if}
					</td>
					<td class="action" valign="top">
						{if $module->version neq $module->installedVersion}
							<a href="{copixurl dest='admin|install|updateModule' moduleName=$module->name}" title="{i18n key='install.alt.update'}" >
								<img src="{copixresource path='admin|img/icon/updateModule.png'}" alt="{i18n key='install.alt.update'}" />
							</a>
						{/if}
					</td>
					<td class="action" valign="top">
						<a href="{copixurl dest='admin|install|deleteModule' moduleName=$module->name}" title="{i18n key='install.alt.delete'}" >
							<img src="{copixresource path='img/tools/delete.png'}" alt='{i18n key='install.alt.delete' }' />
						</a>
					</td>
				</tr>
			{/foreach}
		</table>
	</div>
	{assign var=isFirst value=false}
{/foreach}
   
<h2>{i18n key="install.title.InstallableModules"}</h2>
<div style="text-align: center">
	{assign var=countGroups value=$groupsAvailables|@count}
	{assign var=index value=0}
	{foreach from=$groupsAvailables key=groupId item=group}
		{assign var=index value=$index+1}
		<a href="{copixurl dest="admin|install|manageModules" group=$groupId}" onclick="showModuleGroupAvailables ('availablesModules{$groupId}'); return false;">{$group.caption} ({$group.count})</a>
		{if $index < $countGroups} - {/if}
	{/foreach}
</div>
<br />
{assign var=isFirst value=true}
{foreach from=$arModules.availables key=groupId item=modules}
	{if $isFirst}{assign var=exModuleGroupAvailables value=$groupId}{/if}
	<div id="availablesModules{$groupId}" {if !$isFirst}style="display: none"{/if}>
		<table class="CopixTable">
			<tr>
				<th colspan="2">{i18n key=install.titleTab.module}</th>
				<th>{i18n key=install.titleTab.name}</th>
				<th>{i18n key=install.titleTab.version}</th>
				<th></th>
			</tr>
			{foreach from=$modules item=module}
				<tr class="detailmodule {cycle values=",alternate"}" title="module{$module->name}">
					<td style="width:20px;" valign="top" align="center">
						{if ($module->icon)}
							<img src="{$module->icon}" alt="{$module->name}" title="{$module->name}" style="margin-top: 3px" />
						{/if}
					</td>
					<td style="width:110px;" valign="top"><div style="margin-top: 3px">{$module->name}</div></td>
					<td valign="top">
						<div style="margin-top: 3px;">{$module->description|default:$module->name|escape}</div>
						{assign var=idSufix value=$module->name}
						{copixzone id="module$idSufix" process='admin|detailmodule' moduleName=$module->name ajax="true"}
					</td>
					<td style="width:55px;" align="right" valign="top"><div style="margin-top: 3px">{$module->version}</div></td>
					<td style="width:20px;" valign="top"><img src="{copixresource path="img/tools/add.png"}" alt="{i18n key='install.module.installButton'}" style="margin-top: 3px" /></td>
				</tr>
			{/foreach}
		</table>
	</div>
	{assign var=isFirst value=false}
{/foreach}
   
<h2>{i18n key="install.title.modulesPath"}</h2>
{ulli values=$arModulesPath}

{back url="admin||"}

{copixhtmlheader kind="jsCode"}
{literal}
window.addEvent('domready',function () {
	$$('.detailmodule').each (function (el) {
		el.setStyle('cursor','pointer');
    	el.addEvent('click',function () {
        	var div = $(el.getProperty('title'));
        	if (div.getStyle('display') != 'none') {
            	div.setStyle('display','none');
        	} else {
            	div.fireEvent('display');
            	div.setStyle('display','');
        	}
    	});
    });
});

var exModuleGroupInstalled = 'installedModules{/literal}{$exModuleGroupInstalled}{literal}';
function showModuleGroupInstalled (pGroup) {
	if (pGroup == exModuleGroupInstalled) {
		return null;
	}
	var elExModuleGroup = $ (exModuleGroupInstalled);
	var elModuleGroup = $ (pGroup);
	if (elExModuleGroup != undefined) {
		elExModuleGroup.style.display = 'none';
	}
	if (elModuleGroup != undefined) {
		elModuleGroup.style.display = '';
	}
	exModuleGroupInstalled = pGroup;
}

var exModuleGroupAvailables = 'availablesModules{/literal}{$exModuleGroupAvailables}{literal}';
function showModuleGroupAvailables (pGroup) {
	if (pGroup == exModuleGroupAvailables) {
		return null;
	}
	var elExModuleGroup = $ (exModuleGroupAvailables);
	var elModuleGroup = $ (pGroup);
	if (elExModuleGroup != undefined) {
		elExModuleGroup.style.display = 'none';
	}
	if (elModuleGroup != undefined) {
		elModuleGroup.style.display = '';
	}
	exModuleGroupAvailables = pGroup;
}
{/literal}
{/copixhtmlheader}