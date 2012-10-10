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

{if $ppo->showErrors}
<div class="errorMessage">
 <h1>{i18n key=copix:common.messages.error}</h1>
 {ulli values=$ppo->errors}
</div>
{/if}
<form action="{copixurl dest="simplehelp|admin|valid"}" method="post" name="simpleHelpEdit" class="copixForm">
<fieldset>
   <table class="CopixVerticalTable"> 
      <tr>
       <th><label for="title_sh">{i18n key=dao.simplehelp.fields.title_sh}</label></th>
       <td><input type="text" name="title_sh" value="{$ppo->toEdit->title_sh}" /></td>
      </tr>
      <tr>
       <th><label for="content_sh">{i18n key=dao.simplehelp.fields.content_sh}</label></th>
       <!--<td><textarea name="content_sh">{$ppo->toEdit->content_sh}</textarea></td>-->
       <td>{htmleditor content=$ppo->toEdit->content_sh|stripslashes name=content_sh}</td>
      </tr>
      <tr>
       <th><label for="title_sh">{i18n key=dao.simplehelp.fields.page_sh}</label></th>
       <td><input type="text" name="page_sh" value="{$ppo->toEdit->page_sh}" /></td>
      </tr>
      <tr>
       <th><label for="title_sh">{i18n key=dao.simplehelp.fields.key_sh}</label></th>
       <td><input type="text" name="key_sh" value="{$ppo->toEdit->key_sh}" /></td>
      </tr>
   </table>
</fieldset>
   <p class="validButtons">
   <input type="submit" value="{i18n key="copix:common.buttons.save"}" />
   <input type="button" value="{i18n key="copix:common.buttons.cancel"}" onclick="javascript:window.location='{copixurl dest="simplehelp|admin|cancelEdit"}'" />
   </p>
</form>

<br />
<input type="button" value="{i18n key="copix:common.buttons.back"}" onclick="javascript:document.location='{copixurl dest="admin||"}'" />