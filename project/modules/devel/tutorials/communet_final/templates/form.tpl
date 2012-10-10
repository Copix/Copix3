<form action="{copixurl dest="communet_final||validsignup"}" method="POST">
<table>
  <tr>
    <td>Nom d'utilisateur: </td><td><input type="text" name="login" value="{$ppo->login}"/></td>
  </tr>
  <tr>
    <td>Mot de passe: </td><td><input type="password" name="password" /></td>
  </tr>
  <tr>
    <td>Confirmer votre mot de passe: </td><td><input type="password" name="confirmpassword" /></td>
  </tr>
  <tr>
    <td colspan="2"> Décrivez vous :<br/>
	  <textarea name="description" rows="10" cols="50">{$ppo->description}</textarea>
	</td>
  </tr>
 <tr>
    <td colspan="2"> <input type="submit" value="S'inscrire" />  </td>
 </tr>
</table>
</form>
<br />
<br />
<a href="{copixurl dest="communet_final||}">Retour à l'accueil</a>