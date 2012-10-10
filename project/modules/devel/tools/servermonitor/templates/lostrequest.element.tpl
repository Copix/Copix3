<table class="CopixTable">
<thead>
<tr>
 <th class="copixlistorder{$idlist}" rel="id_smr">Id</th>
 <th class="copixlistorder{$idlist}" rel="url_smr">Url</th>
 <th class="copixlistorder{$idlist}" rel="datetime_smr">Date</th>
</tr>
</thead>
 <tbody>
{foreach from=$results item=line}
 <tr {cycle values=',class="alternate"'}>
  <td>{$line->id_smr}</td>
  <td>{$line->url_smr}</td>
  <td>{$line->datetime_smr|datetimei18n}</td>
 </tr>
{/foreach}
 </tbody>
</table>