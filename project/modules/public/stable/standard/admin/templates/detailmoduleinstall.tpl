<table class="CopixVerticalTable">
	<tr>
		<th>
			{i18n key='install.module.name'}
		</th>
		<td>
			{$info->name}
		</td>
		<th>
			{i18n key='install.module.description'}
		</th>
		<td>
		    {if $info->longdescription}
			{$info->longdescription}
			{else}
			{$info->description}
			{/if}
		</td>
	</tr>
	
    <tr>
        <th>
            {i18n key='install.module.dependencyInstallModule'}
        </th>
        <td>
            {foreach from=$arModule item=module}
            	{if $module->name != $info->name}
                {if $module->ok}{$module->name}<br />
                {else}<span style='color:red'>{$module->name}</span>
                {/if}
                {/if}
            {/foreach}
        </td>
        <th>
            {i18n key='install.module.dependencyExtension'}
        </th>
        <td>
            {foreach from=$arExtension item=extension}
                {if $extension->ok}{$extension->name}<br />
                {else}<span style='color:red'>{$extension->name}</span>
                {/if}
            {/foreach}
        </td>
    </tr>  
    <tr>
    	<td colspan="4">
    	{if $install}
    		<input type="button" value="{i18n key='install.module.installButton'}" onclick="javascript:document.location.href='{copixurl dest="admin|install|installModule" moduleName=$moduleName}'"/>
    	{else}	
    		<input type="button" value="{i18n key='install.module.errorInstallButton'}" disabled=true />
    	{/if}
    	</td>
    </tr>
</table>
