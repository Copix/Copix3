<table class="CopixTable">
<thead>
<tr>
 <th>
 {i18n key="logs.profile"}
 </th>
 <th>
 {i18n key="logs.enabled"}
 </th>
 <th>
 {i18n key="copix:common.title.actions"}
 </th>
 </tr>
</thead>
{foreach from=$ppo->arRegistered key=logProfileName item=logProfile}
 <tr {cycle values=',class="alternate"' name="alternate"}>
  <td>
  {$logProfileName}
  </td>
  <td>
  {if $logProfile.enabled}
   {i18n key=copix:common.buttons.yes}
  {else}
   {i18n key=copix:common.buttons.no}
  {/if}
  </td>
  <td>
   <a href="{copixurl dest="log|edit" profile=$logProfileName}"><img src="{copixresource path="img/tools/select.png"}" /></a> 
   <a href="{copixurl dest="log|show" profile=$logProfileName}"><img src="{copixresource path="img/tools/show.png"}" /></a>
   <a href="{copixurl dest="log|deleteProfile" profile=$logProfileName}"><img src="{copixresource path="img/tools/delete.png"}" /></a>
  </td>
 </tr>
{/foreach}
<form action="{copixurl dest="log|create"}" method="post">
<tr {cycle values=',class="alternate"' name="alternate"}>
 <td colspan="2">
  <input type="text" name="profile" />
 </td>
 <td>
  <input type="image" src="{copixresource path="img/tools/add.png"}" value="{i18n key="copix:common.buttons.add"}" /> 
 </td>
</tr>
</form>
</table>
<a href="{copixurl dest="admin||"}"> <input type="button" value="{i18n key="copix:common.buttons.back"}" /></a>