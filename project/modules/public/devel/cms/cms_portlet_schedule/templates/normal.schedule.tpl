{copixurl dest=# assign=currentURL}
{if count ($tabEvents)>0}
 {foreach from=$tabEvents key=keyEvents item=objEvents}
 <h2>{if $id_page_subscribe && $objEvents->subscribeenabled_evnt}<a href="{copixurl dest=cms|default|get id_evt=$objEvents->id_evnt id=$id_page_subscribe backURL=$currentURL}">{/if}{$objEvents->title_evnt}{if $id_page_subscribe && $objEvents->subscribeenabled_evnt}</a>{/if}</h2></td>
 {if ($objEvents->dateto_evnt!="") && ($objEvents->dateto_evnt!=$objEvents->datefrom_evnt)}
  <h3>Du {$objEvents->datefrom_evnt|datei18n} au {$objEvents->dateto_evnt|datei18n}</h3>
 {else}
  <h3>Le {$objEvents->datefrom_evnt|datei18n}</h3>
 {/if}
 <p>{$objEvents->content_evnt}</p>
 {/foreach}
{/if}
