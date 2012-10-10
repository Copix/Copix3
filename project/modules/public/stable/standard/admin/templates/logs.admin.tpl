<table class="CopixTable">
<thead>
<tr>
 <th>
 {i18n key="logs.profile"}
 </th>
 <th>
 {i18n key="copix:common.title.actions"}
 </th>
 </tr>
</thead>
{foreach from=$ppo->arRegistered item=logProfile}
 <tr {cycle values=',class="alternate"' name="alternate"}>
  <td>
  {$logProfile}
  </td>
  <td>
   <a href="{copixurl dest="log|edit" profile=$logProfile}"><img src="{copixresource path="img/tools/select.png"}" /></a> 
   <a href="{copixurl dest="log|show" profile=$logProfile}"><img src="{copixresource path="img/tools/show.png"}" /></a>
   <a href="{copixurl dest="log|deleteProfile" profile=$logProfile}"><img src="{copixresource path="img/tools/delete.png"}" /></a>
  </td>
 </tr>
{/foreach}
<form action="{copixurl dest="log|create"}" method="post">
<tr {cycle values=',class="alternate"' name="alternate"}>
 <td>
  <input type="text" name="profile" />
 </td>
 <td>
  <input type="image" src="{copixresource path="img/tools/add.png"}" value="{i18n key="copix:common.buttons.add"}" /> 
 </td>
</tr>
</form>
</table>
<a href="{copixurl dest="admin||"}"> <input type="button" value="{i18n key="copix:common.buttons.back"}" /></a>