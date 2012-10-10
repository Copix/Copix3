<table class="CopixTable">
<thead>
<tr>
 <th>
 {i18n key="cache.profile"}
 </th>
 <th>
 {i18n key="copix:common.title.actions"}
 </th>
 </tr>
</thead>
{foreach from=$ppo->arRegistered item=cacheType}
 <tr {cycle values=',class="alternate"' name="alternate"}>
  <td>
  {$cacheType}
  </td>
  <td>
   <a href="{copixurl dest="cache|edit" type=$cacheType}"><img src="{copixresource path="img/tools/select.png"}" /></a> 
   <a href="{copixurl dest="cache|deleteType" type=$cacheType}"><img src="{copixresource path="img/tools/delete.png"}" /></a>
  </td>
 </tr>
{/foreach}
<form action="{copixurl dest="cache|create"}" method="post">
<tr {cycle values=',class="alternate"' name="alternate"}>
 <td>
  <input type="text" name="type" />
 </td>
 <td>
  <input type="image" src="{copixresource path="img/tools/add.png"}" value="{i18n key="copix:common.buttons.add"}" /> 
 </td>
</tr>
</form>
</table>
<a href="{copixurl dest="admin||"}"> <input type="button" value="{i18n key="copix:common.buttons.back"}" /></a>