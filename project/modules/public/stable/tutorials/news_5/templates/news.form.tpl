{if count ($ppo->arErrors)}
<div class="errorMessage">
<h1>Erreurs</h1>
{ulli values=$ppo->arErrors}
</div>
{/if}

<form action="{copixurl dest="admin|valid"}" method="POST">
<table class="CopixVerticalTable">
 <tr>
  <th>Titre</th>
  <td><input type="text" name="title_news" size="50" value="{$ppo->toEdit->title_news|escape}" /></td>
 </tr>

 <tr>
  <th>Date</th>
  <td>{calendar name=date_news value=$ppo->toEdit->date_news|datei18n}</td>
 </tr>

 <tr>
  <th>Résumé</th>
  <td><textarea cols="50" name=summary_news>{$ppo->toEdit->summary_news|escape}</textarea></td>
 </tr>

 <tr>
  <th>Contenu</th>
  <td>{htmleditor name=content_news content=$ppo->toEdit->content_news}</td>
 </tr>

</table>

<input type="submit" value="Valider" />
<a href="{copixurl dest="|"}"><input type="button" value="Annuler" /></a>

</form>