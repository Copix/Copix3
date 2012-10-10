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

<h1 class="main">{i18n key="global.groupe.installedModules"}</h1>

{foreach from=$ppo->installedModules key=module_name item=module_infos}
<h2>
{if ($module_infos.icon)}
<img src="{$module_infos.icon}" />
{/if}
{$module_infos.title}
</h2>

<table class="CopixVerticalTable">
  {assign var=alternate value=""}  
  {foreach from=$module_infos key=file_index item=file_baseName}
  {if $file_index neq 'title' && $file_index neq 'icon'}
  <tr>
    <td {$alternate}>{$file_index|substr:5}</td>
    <td {$alternate} align="right">
      {foreach from=$file_baseName item=file_lang}
      {if $file_lang.isWritable}
      <a href="{copixurl dest="keys|" moduleName=$module_name file=$file_lang.fileName}"
        ><img src="{$file_lang.icon}" alt="{i18n key="global.other.edit"}" title="{i18n key="global.other.edit"}"
      /></a>
      {else}
      <a href="{copixurl dest='keys|' moduleName=$module_name file=$file_lang.fileName}" >
        <img src="{copixresource path='img/flags/locked.png'}" alt="{i18n key='global.error.fileLocked'}" title="{i18n key='global.error.fileLocked'}" /></a>
      {/if}
      {/foreach}      
    </td>
  </tr>
  {if $alternate == ''}
    {assign var=alternate value='class="alternate"'}
  {else}
    {assign var=alternate value=""}
  {/if}
  {/if}
  {/foreach}  
</table>
<br />
{/foreach}

<br />
<h1 class="main">{i18n key="global.groupe.uninstalledModules"}</h1>

{foreach from=$ppo->uninstalledModules key=module_name item=module_infos}
<h2>
{if ($module_infos.icon)}
<img src="{$module_infos.icon}" />
{/if}
{$module_infos.title}
</h2>

<table class="CopixVerticalTable">
  {assign var=alternate value=""}  
  {foreach from=$module_infos key=file_index item=file_baseName}
  {if $file_index neq 'title' && $file_index neq 'icon'}
  <tr>
    <td {$alternate}>{$file_index|substr:5}</td>
    <td {$alternate} align="right">
      {foreach from=$file_baseName item=file_lang}
      {if $file_lang.isWritable}
      <a href="{copixurl dest="keys|" moduleName=$module_name file=$file_lang.fileName}"
        ><img src="{$file_lang.icon}" alt="{i18n key="global.other.edit"}" title="{i18n key="global.other.edit"}"
      /></a>
      {else}
      <font color="red">{i18n key="global.error.fileWrite"}</font>
      {/if}
      {/foreach}      
    </td>
  </tr>
  {if $alternate == ''}
    {assign var=alternate value='class="alternate"'}
  {else}
    {assign var=alternate value=""}
  {/if}
  {/if}
  {/foreach}  
</table>
<br />
{/foreach}

<input type="button" value="{i18n key="global.other.back"}" onclick="javascript:document.location='{copixurl dest="admin||"}'" />