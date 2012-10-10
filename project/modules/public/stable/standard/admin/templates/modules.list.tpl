<h2>{i18n key="install.title.installedModules"}</h2>
<table class="CopixTable">
<tr>
 <th colspan="3">{i18n key=install.titleTab.name}</th>
</tr>
  {foreach from=$arModules item=module}
     {if $module->isInstalled}
   <tr  class="detailmodule {cycle values=",alternate"}" rel="{$module->name}">
   <td width="20px">
   		{if ($module->icon)}
   		<img src="{$module->icon}" alt="{$module->name}" title="{$module->name}" />
   		{/if}
   </td>
   <td>{$module->description|default:$module->name|escape}
       {ajax_divzone id=$module->name zone='admin|detailmodule' moduleName=$module->name}
   </td>
   <td width="20px"><img src="{copixresource path="img/tools/add.png"}" /></td>
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
   <tr  class="detailmodule {cycle values=",alternate"}" rel="{$module->name}">
   <td width="20px">
   		{if ($module->icon)}
   		<img src="{$module->icon}" alt="{$module->name}" title="{$module->name}" />
   		{/if}
   </td>
   <td>{$module->description|default:$module->name|escape}
       {ajax_divzone id=$module->name zone='admin|detailmodule' moduleName=$module->name}
       <!-- <a title="{i18n key="copix:common.buttons.add"}"  href="{copixurl dest="admin|install|installModule" moduleName=$module->name todo="add"}"> -->
   </td>
   <td width="20px"><img src="{copixresource path="img/tools/add.png"}" /></td>
   </tr>
  {/if}
 {/foreach}
</table>
<br />
   
<h2>{i18n key="install.title.modulesPath"}</h2>
<ul>
 {ulli values=$arModulesPath}
</ul>


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
