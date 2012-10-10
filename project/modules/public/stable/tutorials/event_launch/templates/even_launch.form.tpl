  <h2>Evènement sans paramètre</h2>
  <p>Cet évènement sera capturé par le listener du module event_catch1 et event_catch2</p>
  <form action="{copixurl dest="newEventOnly"}"  method="post">
  <p><input value="lancer" type="submit"/></p>
  </form>
  <p>Code exécuté dans l'actiongroup :<br/>_notify ('newEventOnly');</p>
  <br/>
  
  <h2>Evènement avec paramètre</h2>
  <form action="{copixurl dest="newEvent"}"  method="post">
  <p>Cet évènement sera capturé par le listener du module event_catch2</p>
  <p><label for="information">Informations recherchées </label><input id="information" type="text" size="10" name="information"></p>
  <p><input value="lancer" type="submit"/></p>
  </form>
  <p>Code exécuté dans l'actiongroup :<br/>_notify ('newEvent', array ('information'=>_request('information', "rien n'a été mis")));</p>
  <br/>
  
  <p><a href="{copixurl dest="event_catch1||"}">Voir les évènements capturés par event_catch1</a></p>
  <p><a href="{copixurl dest="event_catch2||"}">Voir les évènements capturés par event_catch2</a></p> 