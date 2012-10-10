{if count ($ppo->arErrors)}
  {if count ($ppo->arErrors) == 1}
    {assign var=title_key value='global.title.error'}
  {else}
    {assign var=title_key value='global.title.errors'}
  {/if}
  <div class="errorMessage">
  <h1>{i18n key="$title_key"}</h1>
  {ulli values=$ppo->arErrors}
  </div>
{/if}

<br />
{i18n key="backup.fileToRestore"}
<select id="moduleFile">
  <option value="">----</option>
  {foreach from=$ppo->files key=module item=moduleInfos}
  <optgroup label="{$module}">
  {foreach from=$moduleInfos key=fileIndex item=file}
  <option value="{$module}|{$file}" {if ($ppo->backupModule eq $module && $ppo->backupFile eq $file)}selected="selected"{/if} >{$file}</option>
  {/foreach}
  </optgroup>
  {/foreach}
</select>
<input type="button" value="{i18n key="global.other.showBackups"}" onclick="document.location='{copixurl dest="languages|backups|"}?moduleFile=' + document.getElementById('moduleFile').value" />
<br /><br />

{if (count ($ppo->savedFiles) > 0)}
<table class="CopixVerticalTable">
  <tr>
    <th>{i18n key="global.th.file"}</th>
    <th>{i18n key="global.th.saveDate"}</th>
    <th width="1px"></th>
  </tr>
  {foreach from=$ppo->savedFiles key=fileIndex item=fileInfos}
  <tr>
    <td><img src="{$fileInfos->flag}" alt="{$fileInfos->langCountry}" title="{$fileInfos->langCountry}" /> {$fileInfos->name}</td>
    <td>{$fileInfos->saveDate}</td>
    <td align="right">
      {if ($fileInfos->isWritable)}
      <a href="{copixurl dest="backups|restore" moduleName=$fileInfos->module file=$fileInfos->name saveDateTimestamp=$fileInfos->saveDateTimestamp}"
        ><img src="{copixresource path="img/tools/restore.png"}" alt="{i18n key="global.other.restore"}" title="{i18n key="global.other.restore"}" border="0"
      /></a>
      {else}
      <img src="{copixresource path="img/tools/locked.png"}" alt="{i18n key="global.error.baseFileLocked"}" title="{i18n key="global.error.baseFileLocked"}" border="0" />
      {/if}
    </td>
  </tr>
  {/foreach}
</table>
<br />
{/if}

<input type="button" value="{i18n key="global.other.back"}" onclick="javascript:document.location='{copixurl dest="admin||"}'" />