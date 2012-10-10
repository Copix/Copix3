{if $showErrors}
<div class="errorMessage">
 <h1>{i18n key=copix:common.messages.error}</h1>
 {ulli values=$errors}
 {if isset($erreur)}
 <p>{$erreur}</p>
 {/if}
</div>
{/if}

<form action="{copixurl dest="pictures|admin|validTheme"}" method="post">
<table>
   <tr><th>{i18n key='dao.picturesthemes.fields.name_tpic'} *</th>
       <td><input type="text" name="name_tpic" value="{$toEdit->name_tpic|escape}" /></td></tr>
</table>
<p>
<input type="submit" value="{i18n key="copix:common.buttons.ok"}" />
<input type="button" value="{i18n key="copix:common.buttons.cancel"}" onclick="javascript:window.location='{copixurl dest="pictures|admin|cancelEditTheme"}'" />
</p>
</form>