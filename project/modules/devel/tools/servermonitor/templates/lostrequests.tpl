<h2>Requêtes perdues</h2>
<table class="CopixTable">
 <thead>
  <tr>
   <th>Id</th>
   <th>Url</th>
   <th>Date</th>
  </tr>
 </thead>
 <tbody>
  {foreach from=$ppo->arLostRequests item=request}
  <tr {cycle values=',class="alternate"'}>
   <td>{$request->id_smr}</td>
   <td>{$request->url_smr}</td>
   <td>{$request->datetime_smr|datetimei18n}</td>
  </tr>
  {/foreach}
 </tbody>
 <tfoot>
  <tr>
   <td colspan="2">Total</td>
   <td>{$ppo->arLostRequests|@count}</td>
  </tr>
 </tfoot>
</table>

<h2>Requêtes en cours</h2>
<table class="CopixTable">
 <thead>
  <tr>
   <th>Id</th>
   <th>Url</th>
   <th>Date</th>
  </tr>
 </thead>
 <tbody>
  {foreach from=$ppo->arCurrentRequests item=request}
  <tr {cycle values=',class="alternate"'}>
   <td>{$request->id_smr}</td>
   <td>{$request->url_smr}</td>
   <td>{$request->datetime_smr|datetimei18n}</td>
  </tr>
  {/foreach}
 </tbody>
 <tfoot>
  <tr>
   <td colspan="2">Total</td>
   <td>{$ppo->arCurrentRequests|@count}</td>
  </tr>
 </tfoot>
</table>

<h2>Résumé</h2>
<table class="CopixTable">
<thead>
<tr>
<th>Module</th>
<th>Groupe</th>
<th>Action</th>
<th>Nombre</th>
<th>Plus rapide</th>
<th>Durée moyenne</th>
<th>Plus longue</th>
</tr>
</thead>
<tr>
 <td></td>
 
</tr>
{foreach from=$ppo->arSummary item=element}
<tr {cycle values=',class="alternate"'}>
<td>{$element->module_smr}</td>
<td>{$element->group_smr}</td>
<td>{$element->action_smr}</td>
<td>{$element->count_smr}</td>
<td>{$element->quickestduration_smr/1000}</td>
<td>{$element->avgduration_smr/1000}</td>
<td>{$element->longuestduration_smr/1000}</td>
</tr>
{/foreach}
</table>