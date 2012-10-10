<table class="CopixVerticalTable" style="margin-top: 5px">
	{if $info->version != $version}
		<tr>
			<th>{i18n key='install.module.updateVersion'}</th>
			<td>
				{if $version}
					{$version}
				{else}
					{i18n key='install.module.noVersion'}
				{/if}
				-> <span style="color:red;">{$info->version}</span>
			</td>
		</tr>
	{/if}

	{if $info->longDescription}
		<tr>
			<th>{i18n key='install.module.description'}</th>
			<td>{$info->longDescription}</td>
		</tr>
	{/if}

	{assign var=haveDependecies value="false"}
	{foreach from=$arModule item=module}
		{if $module != $info->name}
			{assign var=haveDependecies value="true"}
		{/if}
	{/foreach}
	{if $haveDependecies eq "true"}
		<tr>
			<th>{i18n key='install.module.dependency'}</th>
			<td>
				{foreach from=$arModule item=module}
					{if $module != $info->name}
						{$module}<br />
					{/if}
				{/foreach}
			</td>
		</tr>
	{/if}
	<tr>
		<th width="100px">{i18n key="install.module.path"}</th>
		<td>{$path}</td>
	</tr>
</table>