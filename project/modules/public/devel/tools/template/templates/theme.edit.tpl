{if count($errors) > 0}
<div class="errorMessage">
<h1>{i18n key="copix:common.messages.error"}</h1>
{ulli values=$errors}
</div>
{/if}

<form name="themeEdit" action="{copixurl dest="theme|valid"}" method="POST">
<fieldset>
<legend>{i18n key=template.messages.newTheme}</legend>
<table class="CopixVerticalTable">
  <tr>
   <th>{i18n key="template.theme.dao.caption_ctpt"}</th>
   <td><input type="text" name="caption_ctpt" value="{$edited->caption_ctpt|escape:html}" /></td>
  </tr>
 </table>
</fieldset>

{if count ($nonInstalledExistingTheme)}
<fieldset>
<legend>{i18n key=template.messages.importTheme}</legend>
 {select name=import_theme_from_harddrive values=$nonInstalledExistingTheme selected=null}
</fieldset>
{/if}
 <input type="submit" value="{i18n key="copix:common.buttons.ok"}" /> 
 <input type="button" onClick="history.back ();"  value="{i18n key="copix:common.buttons.back"}" />
</form>

{if $edited->id_ctpt !== null}
 {i18n key="template.messages.XTemplatesInThisTheme" templateCout=$templateCount}
{/if}