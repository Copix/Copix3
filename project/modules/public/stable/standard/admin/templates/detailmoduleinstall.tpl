<br />
<table class="CopixVerticalTable" border="0">
	<tr class="alternate">
		<th colspan="2" align="center">{i18n key="install.module.infos"}</th>
	</tr>
	<tr>
		<td width="200px">
			{i18n key='install.module.name'}
		</td>
		<td>
			{$info->name}
		</td>
	</tr>
	
	<tr>
        <td>
			{i18n key='install.module.version'}
        </td>
        <td>
       		{if $info->version}
				{$info->version}
			{else}
				{i18n key='install.module.noVersion'}
        	{/if}
        </td>
    </tr>
	
	<tr>
		<td valign="top">
			{i18n key='install.module.description'}
		</td>
		<td valign="top">
		    {if $info->longDescription}
			{$info->longDescription}
			{else}
			{$info->description}
			{/if}
		</td>
	</tr>
	
    <tr>
        <td valign="top">
            {i18n key='install.module.dependency'}
        </td>
        <td>
            {foreach from=$arModule item=module}
            	{if $module->name != $info->name}
                	{if $module->exists && $module->isInstalled}
						<font color="green">{$module->name}</font><br />
					{elseif $module->exists && !$module->isInstalled}
						{$module->name}<br />
                	{else}
						<span style='color:red'>{$module->name}</span> ({i18n key="install.module.notfound"})<br />
					{/if}
                {/if}
            {/foreach}
        </td>
	</tr>
	<tr>
        <td valign="top">
            {i18n key='install.module.dependencyExtension'}
        </td>
        <td>
            {foreach from=$arExtension item=extension}
                {if $extension->exists}
                	{$extension->name}<br />
                {else}
                	<span style='color:red'>{$extension->name}</span><br />
                {/if}
            {/foreach}
        </td>
    </tr>
    <tr>
	 <td>{i18n key="install.module.path"}</td>
	 <td>{$path}</td>
	</tr>
      
    <tr>
    	<td colspan="2" align="center">
    	<br />
    	{if $install}
    		<input type="button" value="{i18n key='install.module.installButton'}" onclick="javascript:document.location.href='{copixurl dest="admin|install|installModule" moduleName=$moduleName}'"/>
    	{else}	
    		<input type="button" value="{i18n key='install.module.errorInstallButton'}" disabled=true />
    	{/if}
    	<br /><br />
    	</td>
    </tr>
</table>
<br />