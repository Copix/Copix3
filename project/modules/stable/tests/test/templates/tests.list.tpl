{*
<div class="errorMessage">
<h1>Erreurs</h1>
{ulli values=$ppo->arErrors}
</div>
*}

<table class="CopixTable">
<thead>
 <tr>
  <th>{i18n key='test.list.caption'}</th>
  <th>{i18n key='test.list.category'}</th>
  <th> {i18n key='test.list.type'} </th>
  <th>{i18n key='test.list.level'}</th>
  <th>{i18n key='test.list.action'}</th>
 </tr>
</thead>
<tbody>
{if count($ppo->arTests)}
 {foreach from=$ppo->arTests item=test}
  <tr {cycle values=',class="alternate"'}>
   <td>{$test->caption_test|escape}</td>
   <td>{$test->category_test|escape}</td>
   <td>{$test->type_test|escape} </td>
   
   <td>{popupinformation displayimg=false text=$test->level_test} {assign var='level' value=$test->level_test} {$ppo->level.$level} {/popupinformation}</td>
   <td>
   {copixurl dest="admin|edit" id=$test->id_test type=$test->type_test assign=url}
   {copixicon type="update" href=$url}
  
   {copixurl dest="admin|delete" id=$test->id_test type=$test->type_test assign=url}
   {copixicon type="delete" href=$url}
  
   {copixurl dest=launchdetails id=$test->id_test assign=url}
   {copixicon type="test" href=$url}
   </td>
  </tr>
 {/foreach}
{else}
   <tr>
   <td colspan="5">
   {i18n key='test.test.novalue'}
   </td>
   </tr>
{/if}

</tbody>
</table>

<form action="{copixurl dest="admin|create"}" method="post">
{copixicon type="new"} {i18n key='test.list.new'} {select values=$ppo->arTypeTest|escape name="type"} 
<input type="submit" value="{i18n key='test.list.submit'}" />
</form>

<input type="button" onclick="location.href='{copixurl dest="admin|default|"}'" style="width:100px" value="{i18n key='test.historyback'}">