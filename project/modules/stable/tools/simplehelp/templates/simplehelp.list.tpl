<table class="CopixTable">
  <thead>
   <tr>
      <th>{i18n key="simplehelp|dao.simplehelp.fields.title_sh"}</th>
      <th>{i18n key="simplehelp|dao.simplehelp.fields.page_sh"}</th>
      <th>{i18n key="simplehelp|dao.simplehelp.fields.key_sh"}</th>
      <th>{i18n key="simplehelp|simplehelp.messages.action"}</th>
      <th>{i18n key="simplehelp|simplehelp.messages.preview"}</th>
   </tr>
   </thead>
   <tbody>
   {foreach from=$ppo->arAides item=aide}
   <tr {cycle values=',class="alternate"'}>
      <td>{$aide->title_sh}</td>
      <td>{$aide->page_sh}</td>
      <td>{$aide->key_sh}</td>
      <td>
	  	<a href="{copixurl dest="simplehelp|admin|prepareEdit" id_sh=$aide->id_sh}" title="{i18n key="copix:common.buttons.update"}"><img src="{copixresource path="img/tools/update.png"}" alt="{i18n key="copix:common.buttons.update"}" /></a>
	  	<a href="{copixurl dest="simplehelp|admin|delete" id_sh=$aide->id_sh}" title="{i18n key="copix:common.buttons.trash"}"><img src="{copixresource path="img/tools/delete.png"}" alt="{i18n key="copix:common.buttons.delete"}" /></a>
	  </td>
	  <td>{copixzone process='showaide' page_sh=$aide->page_sh key_sh=$aide->key_sh}</td>
   </tr>
   {foreachelse}
   <tr>
      <td colspan="4">&nbsp;{i18n key="simplehelp|simplehelp.messages.noAide"}</td>
   </tr>
   {/foreach}
   </tbody>
</table>  
<br>
<a href="{copixurl dest="simplehelp|admin|create"}" title="{i18n key="simplehelp|simplehelp.messages.new"}"><img src="{copixresource path="img/tools/new.png"}" alt="{i18n key="copix:common.buttons.new"}" />{i18n key="simplehelp|simplehelp.messages.new"}</a>

<br />
<input type="button" value="{i18n key="copix:common.buttons.back"}" onclick="javascript:document.location='{copixurl dest="admin||"}'" />