<br />
<table class="CopixVerticalTable">
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
        	{if $info->version != $version}
        		{i18n key='install.module.installVersion'} : {if $version}{$version}{else}{i18n key='install.module.noVersion'}{/if} / <span style="color:red;">{i18n key='install.module.updateVersion'} : {$info->version}</span>
        	{else}
        		{if $version}{$version}{else}{i18n key='install.module.noVersion'}{/if}
        	{/if}
        </td>
    </tr>
	
	<tr>
		<td>
			{i18n key='install.module.description'}
		</td>
		<td>
		    {if $info->longDescription}
				{$info->longDescription}
			{else}
				{$info->description}
			{/if}
		</td>
	</tr>
    <tr>
        <td>
            {i18n key='install.module.dependency'}
        </td>
        <td>
            {foreach from=$arModule item=module}
            	{if $module != $info->name}
                	{$module}<br />
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
   		<input type="button" value="{i18n key='install.module.deleteButton'}" onclick="javascript:document.location.href='{copixurl dest="admin|install|deleteModule" moduleName=$moduleName}'"/>
    	{if $info->version != $version}
   		<input type="button" value="{i18n key='install.module.updateButton'}" onclick="javascript:document.location.href='{copixurl dest="admin|install|updateModule" moduleName=$moduleName}'"/>
   		{/if}
   		<br /><br />
    	</td>
    </tr>
</table>
<br />