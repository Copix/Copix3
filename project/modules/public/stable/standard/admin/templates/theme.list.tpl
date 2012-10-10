<br />
{if count($ppo->arThemes)}
 <table class="CopixTable">
 <thead>
  <tr>
   <th>{i18n key="themes.theme.photo"}</th>
   <th width="10px"></th>
   <th>{i18n key="themes.theme.infos"}</th>
  </tr>
 </thead>
 <tbody>
  <tr {cycle values=',class="alternate"'}>
   <td>
   	 <br />
     <a href="{copixurl dest=admin|themes|doSelectTheme id_ctpt=$theme->id}">
       <img src="{copixurl dest=admin|themes|getImage id=default name=default.png}" border="0"
     /></a>
     
     <br /><br />
   </td>
   <td></td>
   <td>
     <br />
     <center>
     <b>Copix 3</b>{if $ppo->selectedTheme == $theme->id_ctpt} ({i18n key="themes.theme.actual"}){/if}     
     </center>
     <br />
     {i18n key=themes.theme.author} : {i18n key="themes.defaultTheme.author"}<br />
     {i18n key=themes.theme.website} : <a href="{i18n key="themes.defaultTheme.website"}">{i18n key="themes.defaultTheme.website"}</a><br />
     {i18n key=themes.theme.description} : {i18n key="themes.defaultTheme.description"}<br />
     <br />
     <center>
     <input type="button" value="{i18n key="themes.theme.define"}" onclick="document.location = '{copixurl dest=admin|themes|doSelectTheme id_ctpt=''}'" />
     </center>
     <br />
   </td>
  </tr>
 {foreach from=$ppo->arThemes item=theme}
  <tr {cycle values=',class="alternate"'}>
   <td>
   <br />
  {if $theme->image!=null}
  <a href="{copixurl dest=admin|themes|doSelectTheme id_ctpt=$theme->id}">
	<img src="{copixurl dest=admin|themes|getImage id=$theme->id name=$theme->image}" border="0"
  /></a>
  {/if}
  <br /><br />
  </td>
  <td></td>
  <td width="100%">
  <br />
  <center>
  <b>{$theme->name}</b>{if $ppo->selectedTheme == $theme->id} ({i18n key="themes.theme.actual"}){/if}
  </center>
  
  <br />
	{if $theme->author!=null}
	{i18n key=themes.theme.author} : {$theme->author}<br />
	{/if}
	{if $theme->website!=null}
	{i18n key=themes.theme.website} : <a href="{$theme->website}">{$theme->website}</a><br />
	{/if}
	{if $theme->description!=null}
	{i18n key=themes.theme.description} : {$theme->description}<br />
	{/if}
  
  <br />
  <center>
  <input type="button" value="{i18n key="themes.theme.define"}" onclick="document.location = '{copixurl dest=admin|themes|doSelectTheme id_ctpt=$theme->id}'" />
  </center>
  <br />
  </td>
  </tr>
 {/foreach}
 </tbody>
 </table>

{/if}

<br />
<input type="button" value="{i18n key="copix:common.buttons.back"}" onclick="javascript:window.location='{copixurl dest="admin||"}'">