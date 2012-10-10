<table class="CopixTable">
<tr>
<th>Titre</th>
<th>Date de génération</th>
<th>Information passée</th>
</tr>
{foreach from=$ppo->arrEvent item=event name=liste}
	{if $smarty.foreach.liste.iteration % 2 eq 0}
		<tr class="alternate">
	{else}
		<tr>
	{/if}
	<td>{$event->titre}</td>
	<td>{$event->dtcreation|date_format:"%d/%m/%Y"}</td>
	<td>{$event->information}</td>
	</tr>
{/foreach}
</table>
<p>{literal}Code des listeners ayant capturé ces évènements :<br/><br/>
public function processNewEventOnly ($pEvent, $pEventRep) {<br/>
&nbsp;&nbsp;&nbsp;&nbsp;$event = _record ('copix_event1');<br/>
&nbsp;&nbsp;&nbsp;&nbsp;// On affecte les valeurs aux champs<br/>
&nbsp;&nbsp;&nbsp;&nbsp;$event->titre = 'Evènement sans paramètre';<br/>
&nbsp;&nbsp;&nbsp;&nbsp;$event->dtcreation = date("Ymd");<br/>
&nbsp;&nbsp;&nbsp;&nbsp;$event->information = 'Pas de paramètre';<br/>
&nbsp;&nbsp;&nbsp;&nbsp;// On insert l'enregistrement<br/>
&nbsp;&nbsp;&nbsp;&nbsp;_dao ('copix_event1')->insert ($event);<br/>
}<br/><br/>
public function processNewEvent ($pEvent, $pEventRep) {<br/>
&nbsp;&nbsp;&nbsp;&nbsp;$event = _record ('copix_event2');<br/>
&nbsp;&nbsp;&nbsp;&nbsp;// On affecte les valeurs aux champs<br/>
&nbsp;&nbsp;&nbsp;&nbsp;$event->titre = 'Evènement avec paramètre';<br/>
&nbsp;&nbsp;&nbsp;&nbsp;$event->dtcreation = date("Ymd");<br/>
&nbsp;&nbsp;&nbsp;&nbsp;$event->information = $pEvent->getParam ('information');<br/>
&nbsp;&nbsp;&nbsp;&nbsp;// On insert l'enregistrement<br/>
&nbsp;&nbsp;&nbsp;&nbsp;_dao ('copix_event2')->insert ($event);<br/>
}
{/literal}
</p>
<p><a href="{copixurl dest="event_launch||"}">Retour à la page de lancement des évènements</a></p>