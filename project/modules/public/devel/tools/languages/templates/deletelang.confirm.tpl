{literal}
<script type="text/javascript">
function confirmDeleteFiles () {
	if (confirm ('{/literal}{i18n key="synchronize.confirm.deleteFiles"}{literal}')) {		
		document.location = '{/literal}{copixurl dest="synchronize|delete"}{literal}?lang=' + lang;
	}
}
</script>
{/literal}
<br />

<form action="{copixurl dest="synchronize|delete"}" method="post">
<table class="CopixVerticalTable">
	<tr>
		<th>{i18n key="global.th.module"}</th>
		<th>{i18n key="global.th.file"}</th>
		<th>{i18n key="global.th.langue"}</th>
		<th align="center">{i18n key="global.th.delete"}</th>
	</tr>
	{assign var=alternate value='class="alternate"'} 
	{foreach from=$ppo->arFiles key=moduleName item=files}
	{foreach from=$files key=fileIndex item=fileInfos}
	<tr {$alternate}>
		<td>{$moduleName}</td>
		<td>{$fileInfos->baseName}</td>
		<td><img src="{$fileInfos->flag}" /> ({$fileInfos->langCountry})</td>
		<td align="center">
			{if $fileInfos->locked}
			{i18n key="synchronize.error.fileOpened"}
			{else}
			<input type="checkbox" checked="checked" name="deleteFile|{$moduleName}|{$fileInfos->baseName}" />
			{/if}
		</td>
	</tr>
	{if $alternate == ''}
		{assign var=alternate value='class="alternate"'}
  	{else}
		{assign var=alternate value=""}
  	{/if}
	{/foreach}
	{/foreach}
</table>

<br />
<center>
<input type="submit" value="{i18n key="synchronize.input.deleteFiles"}" />
</center>
<input type="hidden" name="confirm" value="1" />
<input type="hidden" name="lang" value="{$ppo->lang}" />
</form>

<br /><br />
<input type="button" value="{i18n key="synchronize.input.back"}" onclick="javascript: document.location='{copixurl dest="synchronize|"}'" />