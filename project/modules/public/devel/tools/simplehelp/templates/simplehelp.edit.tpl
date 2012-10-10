{literal}
<script type="text/javascript">
//<![CDATA[
function doUrl (pUrl) {
   var myForm = document.newsEdit;
   myForm.action = pUrl;
   if (typeof myForm.onsubmit == "function")// Form is submited only if a submit event handler is set.
      myForm.onsubmit();
   myForm.submit ();
}
//]]>
</script>
{/literal}

{if $showErrors}
<div class="errorMessage">
 <h1>{i18n key=copix:common.messages.error}</h1>
 {ulli values=$errors}
</div>
{/if}
<form action="{copixurl dest="simplehelp|admin|valid"}" method="post" name="simpleHelpEdit" class="copixForm">
<fieldset>
   <table>
      <tr>
       <th><label for="title_sh">{i18n key=dao.simplehelp.fields.title_sh}</label></th>
       <td><input type="text" name="title_sh" value="{$toEdit->title_sh}" /></td>
      </tr>
      <tr>
       <th><label for="contenu_sh">{i18n key=dao.simplehelp.fields.contenu_sh}</label></th>
       <!--<td><textarea name="contenu_sh">{$toEdit->contenu_sh}</textarea></td>-->
       <td>{htmleditor content=$toEdit->contenu_sh|stripslashes name=contenu_sh}</td>
      </tr>
      <tr>
       <th><label for="title_sh">{i18n key=dao.simplehelp.fields.page_sh}</label></th>
       <td><input type="text" name="page_sh" value="{$toEdit->page_sh}" /></td>
      </tr>
      <tr>
       <th><label for="title_sh">{i18n key=dao.simplehelp.fields.key_sh}</label></th>
       <td><input type="text" name="key_sh" value="{$toEdit->key_sh}" /></td>
      </tr>
   </table>
</fieldset>
   <p class="validButtons">
   <input type="submit" value="{i18n key="copix:common.buttons.save"}" />
   <input type="button" value="{i18n key="copix:common.buttons.cancel"}" onclick="javascript:window.location='{copixurl dest="simplehelp|admin|cancelEdit"}'" />
   </p>
</form>