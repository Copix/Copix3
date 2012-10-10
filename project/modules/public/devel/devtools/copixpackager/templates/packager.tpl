<h2>Modules à packager</h2>

{foreach from=$ppo->modules key=path item=modulesInfos}
<h3>{showdiv id="div_$path" show="false"} <input type="checkbox" id="checkPath_{$path}" /><label for="checkPath_{$path}"> {$path}</label></h3>
<div id="div_{$path}" style="display:none">
<table>
	{foreach from=$modulesInfos key=moduleName item=moduleInfos}
	<tr>
		<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td width="100%">
			<input type="checkbox" id="checkModule_{$moduleName}" /><label for="checkModule_{$moduleName}"> <b>[{$moduleName}]</b> {$moduleInfos.description}</label>
		</td>
		<td nowrap="nowrap" align="right">{$moduleInfos.files}</td>
		<td nowrap="nowrap" align="right">&nbsp;&nbsp;{$moduleInfos.size}</td>
	</tr>
	{/foreach}
</table>
</div>
{/foreach}

<h2>Thèmes à packager</h2>

{foreach from=$ppo->themes item=tplInfos}
{assign var=tplId value=$tplInfos->id}
<h3>{showdiv id="divtpl_$tplId" show="false"} <input type="checkbox" id="checktpl_{$tplInfos->id}" /><label for="checktpl_{$tplInfos->id}"> {$tplInfos->name}</label></h3>
<div id="divtpl_{$tplInfos->id}" style="display:none">
<table>
	<tr>
		<td width="50px"></td>
		<td>
			<img src="{copixurl dest=admin|themes|getImage id=$tplInfos->id name=$tplInfos->image}" />
		</td>
		<td>
			{i18n key="global.theme.author"} : {$tplInfos->author}
			<br />{i18n key="global.theme.website"} : <a href="{$tplInfos->website}">{$tplInfos->website}</a>
			<br />{i18n key="global.theme.description"} : {$tplInfos->description}
		</td>
	</tr>
</table>
</div>
{/foreach}

<input type="button" value="{i18n key="copix:copix.back"}" onclick="javascript:document.location='{copixurl dest="admin||"}'" />