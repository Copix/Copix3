<form action="{copixurl dest="packager|make"}" method="post">
<h2>Modules à packager</h2>

{foreach from=$ppo->modules key=path item=modulesInfos}
{showdiv id="div_$path" show="false"} <input type="checkbox" id="checkPath_{$path}" /><label for="checkPath_{$path}"> {$path}</label><br />
<div id="div_{$path}" style="display:none">
<table class="CopixVerticalTable">
	{assign var=alternate value=""}
	{foreach from=$modulesInfos key=moduleName item=moduleInfos}
	<tr>
		<td {$alternate} width="100%">
			<input type="checkbox" id="checkModule_{$moduleName}" name="checkModule_{$moduleName}" /><label for="checkModule_{$moduleName}"> <b>[{$moduleName}]</b> {$moduleInfos.description}</label>
		</td>
		<td {$alternate} nowrap="nowrap" align="right">{$moduleInfos.files}</td>
		<td {$alternate} nowrap="nowrap" align="right">&nbsp;&nbsp;{$moduleInfos.size}</td>
	</tr>
	{if $alternate == ''}
		{assign var=alternate value='class="alternate"'}
	{else}
		{assign var=alternate value=""}
	{/if}
	{/foreach}
</table>
<br />
</div>
{/foreach}

<h2>Thèmes à packager</h2>

{foreach from=$ppo->themes item=tplInfos}
{assign var=tplId value=$tplInfos->id}
{showdiv id="divtpl_$tplId" show="false"} <input type="checkbox" id="checktpl_{$tplInfos->id}" name="checktpl_{$tplInfos->id}" /><label for="checktpl_{$tplInfos->id}"> {$tplInfos->name}</label><br />
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

<h2>Configuration du package</h2>
Nom : <input type="text" name="packageName" value="{$ppo->packageName}" size="35" /><br />
Formats :<br />
&nbsp;&nbsp;<input type="checkbox" name="compressZip" id="compressZip" checked="checked" /><label for="compressZip"> .zip</label><br />
&nbsp;&nbsp;<input type="checkbox" name="compressTarGz" id="compressTarGz" checked="checked" /><label for="compressTarGz"> .tar.gz</label>
<br /><br />
<center>
<input type="submit" value="Créer le package" />
</center>
</form>

<br />
<input type="button" value="{i18n key="copix:copix.back"}" onclick="javascript:document.location='{copixurl dest="admin||"}'" />