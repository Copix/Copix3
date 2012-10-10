{if count ($ppo->arErrors)}
<div class="errorMessage">
<h1>Erreurs</h1>
{ulli values=$ppo->arErrors}
</div>
{/if}

<form action="{copixurl dest="valid"}" method="POST">
<table class="CopixVerticalTable">
 <tr>
  <th>Libellé</th>
  <td><input type="text" name="caption_crud" value="{$ppo->toEdit->caption_crud|escape}" /></td>
 </tr>

 <tr>
  <th>Description</th>
  <td><textarea name="description_crud">{$ppo->toEdit->description_crud|escape}</textarea></td>
 </tr>

</table>

<input type="submit" value="Valider" />
<a href="{copixurl dest="|"}"><input type="button" value="Annuler" /></a>

</form>