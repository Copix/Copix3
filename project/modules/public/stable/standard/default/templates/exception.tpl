<font color="red"><b>{$ppo->message}</b></font>
<br />
<br />
<b>Type</b> : {$ppo->type}<br />
<b>Fichier</b> : {$ppo->file}%<br />
<b>Ligne</b> : {$ppo->line}

<br /><br />
{if count ($ppo->trace)}
<table class="CopixTable">
<thead>
<tr>
 <th>&nbsp;</th>
 <th>Classe</th>
 <th>Fonction</th>
 <th>Arguments</th>
</tr>
</thead>
{foreach from=$ppo->trace item=item }
<tr {cycle values='class="alternate",'}>
 <td>{popupinformation}{$item.line}<br />{$item.file}{/popupinformation}</td>
 <td>{$item.class}</td>
 <td>{$item.function}</td>
 <td><pre>{$item.args|@var_export:true}</pre></td>
</tr>
{/foreach}
</table>
{/if}