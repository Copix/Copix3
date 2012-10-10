<form action="{copixurl dest="communet_final||validedit"}" method="POST">
<input type="hidden" name="login" value="{$ppo->login}" />
<h1>Nom d'utilisateur: {$ppo->login} </h1>
Changez votre description :<br/>
<textarea name="description" rows="10" cols="50">{$ppo->description}</textarea><br/>
<input type="submit" value="Mettre à jour"/>
</form>
<br/>
<a href="{copixurl dest="communet_final||page" login=$ppo->login}">Retour sans modifier</a>