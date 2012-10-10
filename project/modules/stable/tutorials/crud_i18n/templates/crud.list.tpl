<table class="CopixTable">
 <thead>
  <tr>
   <th>{i18n key="crud.caption_crud"}</th>
   <th>{i18n key="copix:common.title.actions"}</th>   
  </tr>
 </thead>
 <tbody>
 {foreach from=$ppo->arData item=element}
  <tr {cycle values=',class="alternate"'}>
   <td>{$element->caption_crud}</td>
   <td>
     <a href="{copixurl dest="delete" id_crud=$element->id_crud}"><img src="{copixresource path="img/tools/delete.png"}" /></a>
     <a href="{copixurl dest="edit" id_crud=$element->id_crud}"><img src="{copixresource path="img/tools/update.png"}" /></a>
   <td>
  </tr>
 {/foreach}
 </tbody>
</table>

<a href="{copixurl dest="edit" new=1}"><img src="{copixresource path="img/tools/new.png"}" />{i18n key="copix:common.buttons.new"}</a>