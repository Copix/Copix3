{if count ($ppo->arErrors)}
<div class="errorMessage">
<h1>{i18n key="copix:common.messages.error"}</h1>
{ulli values=$ppo->arErrors}
</div>
{/if}

<form action="{copixurl dest="valid"}" method="POST">
<table class="CopixVerticalTable">
 <tr>
  <th>{i18n key="crud.caption_crud"}</th>
  <td><input type="text" name="caption_crud" value="{$ppo->toEdit->caption_crud|escape}" /></td>
 </tr>

 <tr>
  <th>{i18n key="crud.description_crud"}</th>
  <td><textarea name="description_crud">{$ppo->toEdit->description_crud|escape}</textarea></td>
 </tr>

</table>

<input type="submit" value="{i18n key="copix:common.buttons.valid"}" />
<a href="{copixurl dest="|"}"><input type="button" value="{i18n key="copix:common.buttons.cancel"}" /></a>

</form>
