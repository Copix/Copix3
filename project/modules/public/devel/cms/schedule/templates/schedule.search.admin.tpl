{**
*
*Gestion des évènements en ligne + archives
*
*}

<H2>Recherche d'évènements:</H2>
<br />
<form method='post' action='{copixurl dest="schedule|admin|searchEvent"}'>
<input type="hidden" name="adminpage" value="search">
<table>
       <tr>
           <td>Date de début d'évenement</td>
           <td>{calendar name="sdatefrom_evnt" value=$searchparams->datefrom_evnt|datei18n}</td>
       </tr>
       <tr>
           <td>Date de fin d'évènement</td>
           <td>{calendar name="sdateto_evnt" value=$searchparams->dateto_evnt|datei18n}</td>
       </tr>
       
       <tr>
           <td colspan=2><center><input type="submit" value="Recherche">
           <input type="button" value="Retour" onclick="javascript:document.location.href='{copixurl dest="schedule|admin|"}'">
           </center></td>
       </tr>
       
</table>
</form>

{if $evts|count > 0}
<h2>Résultat :</h2><br />
   {foreach from=$evts item=event key=varKey}


   <h2>{if $event->picture_evtc}<img src="?module=pictures&action=get&id_pict={$event->picture_evtc}">{/if} {$event->title_evnt}</h2> - <i>créé ou modifié le {$event->date_evnt|date_format:"%d/%m/%Y"}</i> -
   <br />
    <input type="button" onclick="javascript:document.location.href='{copixurl dest="schedule|admin|doDelete" adminpage="search" id_evnt=$event->id_evnt}'" value="Supprimer">
    <input type="button" onclick="javascript:document.location.href='{copixurl dest="schedule|admin|getAdminEvent" adminpage="search" id_evnt=$event->id_evnt}'" value="Modifier">
    <br />
   {$event->content_evnt}
   <br />
   <br />
   
   {/foreach}
{/if}
