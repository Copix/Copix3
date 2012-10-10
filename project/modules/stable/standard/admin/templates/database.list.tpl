<h2 class="first">{i18n key="database2.defaultProfile"}</h2>
{if !is_null ($ppo->defaultProfile)}
	<table class="CopixTable">
		<tr>
			<th>{i18n key="database2.profil"}</th>
			<th>{i18n key="database2.database"}</th>
			<th>{i18n key="database2.host"}</th>		
			<th>{i18n key="database2.user"}</th>
			<th>{i18n key="database2.driver"}</th>
			<th width="80">{i18n key="database2.status"}</th>
		</tr>
		
		<tr>
			<td>{$ppo->defaultProfileName}</td>
			<td>{$ppo->defaultProfile.dbname}</td>
			<td>{$ppo->defaultProfile.host}</td>
			<td>{$ppo->defaultProfile.user}</td>
			<td>{$ppo->defaultProfile.driver}</td>
			<td>
				{if $ppo->defaultProfile.available === true}
					<img src="{copixresource path="img/tools/online.png"}" alt="online" />
				{else}
					<img src="{copixresource path="img/tools/offline.png"}" alt="offline" title="{$ppo->defaultProfile.available}" />
				{/if}
				{if $ppo->defaultProfile.copixIsInstalled}
					<img src="{copixresource path="img/copix.ico"}" alt="{i18n key="database2.img.copixIsInstalled"}" title="{i18n key="database2.img.copixIsInstalled"}" />
				{/if}
				{if $ppo->defaultProfile.canEdit}
					<a href="{copixurl dest="database2|edit" profile=$ppo->defaultProfileName}"><img src="{copixresource path="img/tools/select.png"}" alt="select" /></a>
					<a href="{copixurl dest="database2|delete" profile=$ppo->defaultProfileName}"><img src="{copixresource path="img/tools/delete.png"}" alt="delete" /></a>
				{/if}
			</td>
		</tr>
	</table>
{else}
	{i18n key="database2.noDefaultProfile"}
{/if}

{if count ($ppo->profiles) > 0}
	<h2>{i18n key="database2.othersProfiles"}</h2>
	<table class="CopixTable">
		<tr>
			<th>{i18n key="database2.profil"}</th>
			<th>{i18n key="database2.database"}</th>
			<th>{i18n key="database2.host"}</th>		
			<th>{i18n key="database2.user"}</th>
			<th>{i18n key="database2.driver"}</th>
			<th width="80">{i18n key="database2.status"}</th>
		</tr>
		{foreach from=$ppo->profiles item=item key=key}
			<tr>
				<td>{$key}</td>
				<td>{$item.dbname}</td>
				<td>{$item.host}</td>
				<td>{$item.user}</td>
				<td>{$item.driver}</td>
				<td>
					{if $item.available === true}
						<img src="{copixresource path="img/tools/online.png"}" alt="online" />
					{else}
						<img src="{copixresource path="img/tools/offline.png"}" alt="offline" title="{$item.available}" />
					{/if}
					{if $item.copixIsInstalled}
						<<img src="{copixresource path="img/copix.ico"}" alt="{i18n key="database2.img.copixIsInstalled"}" title="{i18n key="database2.img.copixIsInstalled"}" />
					{/if}
					{if $item.canEdit}
						<a href="{copixurl dest="database2|edit" profile=$key}"><img src="{copixresource path="img/tools/select.png"}" alt="select" /></a>
						<a href="{copixurl dest="database2|delete" profile=$key}"><img src="{copixresource path="img/tools/delete.png"}" alt="delete" /></a>
					{/if}
				</td>
			</tr>
		{/foreach}
	</table>
{/if}

<br />
<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr>
		<td><a href="{copixurl dest="database2|add"}"><img src="{copixresource path="img/tools/add.png"}" alt="add" /> {i18n key="database2.addProfile"}</a></td>
		<td align="right"><a href="{copixurl dest="admin||"}"><img src="{copixresource path="img/tools/up.png"}" alt="back" /> {i18n key="copix:common.buttons.back"}</a></td>
	</tr>
</table>
<br />
{back url="admin||"}