<table class="CopixTable">
 <thead>
  <tr>
   <th>Libellé</th>
   <th>Actions</th>   
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

<a href="{copixurl dest="edit" new=1}"><img src="{copixresource path="img/tools/new.png"}" />Nouveau</a>