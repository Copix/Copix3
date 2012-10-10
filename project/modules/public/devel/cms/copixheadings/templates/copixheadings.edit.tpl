{if count ($errors) > 0}
<div class="errorMessage">
 <h1>{i18n key=copix:common.messages.error}</h1>
 {ulli values=$errors}
</div>
{/if}

<form action="{copixurl dest="admin|valid"}" method="post" name="headingEdit">
<fieldset>
   <table>
      <tr>
       <th><label for="caption_head">{i18n key="copixheadings.fields.caption_head"} *</label></th>
       <td><input type="text" size="48" id="caption_head" name="caption_head" value="{$toEdit->caption_head|escape}" /></td>
      </tr>
      <tr>
       <th><label for="description_head">{i18n key="copixheadings.fields.description_head"}</label></th>
        <td><textarea cols="40" rows="5" name="description_head" id="description_head">{$toEdit->description_head|escape}</textarea></td>
      </tr>
      <tr>
       <th><label for="url_head">{i18n key="copixheadings.fields.url_head"}</label></th>
       <td><input type="text" size="48" maxlength="255" name="url_head" id="url_head" value="{$toEdit->url_head|escape}" /></td>
      </tr>
      {if $isNew === true}
      <tr>
       <th><label for="father_head">{i18n key="copixheadings.fields.father_head"}</label></th>
        <td>{select name="father_head" selected=$toEdit->father_head values=$arHeadings objectMap="id_head;caption_head"}</td>
      </tr>
      {/if}
   </table>
</fieldset>
   <p class="validButtons">
      <input type="submit" value="{i18n key="copix:common.buttons.valid"}" />
      <input type="button" value="{i18n key="copix:common.buttons.cancel"}" onclick="location.href='{copixurl dest="admin|cancelEdit"}'" />
   </p>
</form>

{formfocus id="caption_head"}