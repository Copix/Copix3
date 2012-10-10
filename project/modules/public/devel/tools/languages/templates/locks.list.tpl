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

<table class="CopixVerticalTable">
  <tr>
    <th>{i18n key="global.th.module"}</th>
    <th>{i18n key="global.th.file"}</th>
    <th>{i18n key="global.th.user"}</th>
    <th>{i18n key="global.th.timeLeft"}</th>    
  </tr> 
  {foreach from=$ppo->lockedFiles item=fileInfos}
  <tr>
    <td>{$fileInfos.module}</td>
    <td><img src="{$fileInfos.icon}" /> {$fileInfos.file}</td>
    <td>{$fileInfos.user}</td>
    <td>{$fileInfos.timeLeft} {i18n key="global.other.min"}</td>
    <td align="right">
    	<a href="{copixurl dest="locks|unlock" moduleName=$fileInfos.module file=$fileInfos.file}"
    		><img src="{copixresource path="img/tools/restore.png"}" alt="{i18n key="global.other.unlock"}" title="{i18n key="global.other.unlock"}" border="0"
    	/></a>
    </td>
  </tr>
  {/foreach} 
</table>

<br />
<input type="button" value="{i18n key="global.other.back"}" onclick="javascript:document.location='{copixurl dest="admin||"}'" />