{capture name=editKeys}{i18n key="synchronize.action.editKeys"}{/capture}
<br />
<form action="{$ppo->urlSync}" method="post">
<table class="CopixVerticalTable">	
	<tr>
		<th>{i18n key="global.th.module"}</th>
		<th>{i18n key="global.th.file"}</th>
		<th>{i18n key="global.th.langue"}</th>
		<th>{i18n key="global.th.actions"}</th>
		<th align="center">{i18n key="global.th.edit"}</th>
	</tr>
	{assign var=alternate value=''}
	{foreach from=$ppo->arDifferences key=moduleName item=moduleFiles}
	{foreach from=$moduleFiles key=fileBaseName item=fileLangs}
	{foreach from=$fileLangs key=langName item=langInfos}
	<tr {$alternate}>
		<td>{$moduleName}</td>
		<td>{$fileBaseName}</td>
		<td><img src="{$langInfos.fileInfos->flag}" alt="{$langInfos.fileInfos->langName}" title="{$langInfos.fileInfos->langName}" /> ({$langName})</td>
		<td>
			{if $langInfos.actions[0] == 'deleteFile'}
			<font color="red">{i18n key="synchronize.action.deleteFile"}</font>
			
			{elseif $langInfos.actions[0] == 'createFile'}
			<font color="green">{i18n key="synchronize.action.createFile"}</font>
			
			{else}
			<font color="green">{popupinformation displayimg=false text=$smarty.capture.editKeys}</font>
			{foreach from=$langInfos.actions|toarray item=actionInfos key=actionIndex}
			
			{if $actionInfos.addKey != ''}
			<b><font color="green">{i18n key="synchronize.action.addKey"}</font></b> {$actionInfos.addKey}
			{/if}
			
			{if $actionInfos.deleteKey != ''}
			<b><font color="red">{i18n key="synchronize.action.deleteKey"}</font></b> {$actionInfos.deleteKey}
			{/if}
			
			<br />
			{/foreach}
			{/popupinformation}
			{/if}
		</td>
		<td align="center">
			<input type="checkbox" name="{$moduleName}|{$fileBaseName}|{$langName}" checked="checked" />
		</td>
	</tr>
	{if $alternate == ''}
      {assign var=alternate value='class="alternate"'}
    {else}
      {assign var=alternate value=""}
    {/if}
    {/foreach}
    {/foreach}
	{/foreach}
</table>

<br />
<center>
	<input type="submit" value="{i18n key="synchronize.input.synchronizeLanguages"}" />
</center>
</form>
<input type="button" value="{i18n key="global.other.back"}" onclick="document.location='{copixurl dest="synchronize|"}'" />