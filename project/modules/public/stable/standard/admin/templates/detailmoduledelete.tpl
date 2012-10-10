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
			{i18n key='install.module.version'}
        </th>
        <td>
        	{if $info->version != $version}
        		{i18n key='install.module.installVersion'} : {if $version}{$version}{else}{i18n key='install.module.noVersion'}{/if} / <span style="color:red;">{i18n key='install.module.updateVersion'} : {$info->version}</span>
        	{else}
        		{if $version}{$version}{else}{i18n key='install.module.noVersion'}{/if}
        	{/if}
        </td>
    
        <th>
            {i18n key='install.module.dependencyDeleteModule'}
        </th>
        <td>
            {foreach from=$arModule item=module}
                {$module}<br />
            {/foreach}
        </td>
    </tr>  
    <tr>
    	<td colspan="2">
   		<input type="button" value="{i18n key='install.module.deleteButton'}" onclick="javascript:document.location.href='{copixurl dest="admin|install|deleteModule" moduleName=$moduleName}'"/>
    	</td>
    	<td colspan="2">
    	{if $info->version != $version}
   		<input type="button" value="{i18n key='install.module.updateButton'}" onclick="javascript:document.location.href='{copixurl dest="admin|install|updateModule" moduleName=$moduleName}'"/>
   		{/if}
    	</td>
    	
    </tr>
</table>
