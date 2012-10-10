<h2>{i18n key="install.title.installedModules"}</h2>
<table class="CopixTable">
<tr>
 <th colspan="3">{i18n key=install.titleTab.name}</th>
</tr>
  {foreach from=$arModules item=module}
     {if $module->isInstalled}
   <tr  class="detailmodule {cycle values=",alternate"}" rel="module{$module->name}">
   <td width="20px" valign="top" align="center">
   		{if ($module->icon)}
   		<img src="{$module->icon}" alt="{$module->name}" title="{$module->name}" style="margin-top: 3px" />
   		{/if}
   </td>
   <td valign="top">
		<div style="margin-top: 3px">{$module->description|default:$module->name|escape}</div>
		{assign var=idSufix value=$module->name}
        {copixzone id="module$idSufix" process='admin|detailmodule' moduleName=$module->name ajax=true}
   </td>
   <td width="20px" valign="top"><img src="{copixresource path="img/tools/delete.png"}" style="margin-top: 3px" /></td>
   </tr>
     {/if}
  {/foreach}
</table>
<br />
   
<h2>{i18n key="install.title.InstallableModules"}</h2>

<table class="CopixTable">
	<tr>
		<th colspan="3">{i18n key=install.titleTab.name}</th>
	</tr>

	{foreach from=$arModules item=module}
		{if ! $module->isInstalled}
			<tr class="detailmodule {cycle values=",alternate"}" rel="module{$module->name}">
				<td width="20px" valign="top" align="center">
   					{if ($module->icon)}
   						<img src="{$module->icon}" alt="{$module->name}" title="{$module->name}" style="margin-top: 3px" />
   					{/if}
				</td>
				<td valign="top">
					<div style="margin-top: 3px">{$module->description|default:$module->name|escape}</div>
					{assign var=idSufix value=$module->name}
					{copixzone id="module$idSufix" process='admin|detailmodule' moduleName=$module->name ajax=true}
					<!-- <a title="{i18n key="copix:common.buttons.add"}"  href="{copixurl dest="admin|install|installModule" moduleName=$module->name todo="add"}"> -->
				</td>
				<td width="20px" valign="top">
					<img src="{copixresource path="img/tools/add.png"}" style="margin-top: 3px" />
				</td>
			</tr>
		{/if}
	{/foreach}
</table>
<br />
   
<h2>{i18n key="install.title.modulesPath"}</h2>
{ulli values=$arModulesPath}

<a href="{copixurl dest="admin||"}"> <input type="button" value="{i18n key="copix:common.buttons.back"}" /></a>

{copixhtmlheader kind="jsCode"}
{literal}
window.addEvent('domready',function () {
	$$('.detailmodule').each (function (el) {
		el.setStyle('cursor','pointer');
    	el.addEvent('click',function () {
        	var div = $(el.getProperty('rel'));
        	if (div.getStyle('display') != 'none') {
            	div.setStyle('display','none');
        	} else {
            	div.fireEvent('display');
            	div.setStyle('display','');
        	}
    	});
    });
});
{/literal}
{/copixhtmlheader}