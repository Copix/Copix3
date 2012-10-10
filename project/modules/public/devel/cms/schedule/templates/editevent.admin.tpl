{if $showErrors}
<div class="errorMessage">
 <h1>{i18n key=copix:common.messages.error}</h1>
 {ulli values=$errors}
</div>
{/if}

{literal}
<script type="text/javascript">
//<![CDATA[
function doUrl (pUrl) {
   var myForm = document.scheduleEdit;
   myForm.action = pUrl;
   {/literal}
   {if $toEdit->editionkind_news == 'HTMLAREA'}
   if (typeof myForm.onsubmit == "function")// Form is submited only if a submit event handler is set.
      myForm.onsubmit();
   {/if}
   {literal}
   myForm.submit ();

}
//]]>
</script>
{/literal}

<form method="post" action="{copixurl dest="schedule|admin|validEvnt"}" name="scheduleEdit" class="copixForm">
<fieldset>
<table>
 <tr>
  <th valign="top">{i18n key=dao.schedule.fields.title_evnt} : </th>
 </tr>
 <tr>
   <td><input size="48" type="text" name="title_evnt" value="{$toEdit->title_evnt|stripslashes}"></td>
 </tr>
 <tr>
   <th valign="top">{i18n key=dao.schedule.fields.preview_evnt} : </th>
 </tr>
 <tr>
   <td>
      <textarea name="preview_evnt" cols="40" rows="8">{$toEdit->preview_evnt}</textarea>
   </td>
 </tr>
 <tr>
   <th valign="top">{i18n key=dao.schedule.fields.content_evnt} : </th>
 </tr>
 <tr>
   <td>
      {if $editionKind == 'HTMLAREA'}
         {htmleditor name="content_evnt" content=$toEdit->content_evnt|stripslashes}
      {else}
         <textarea name="content_evnt" cols="40" rows="8">{$toEdit->content_evnt}</textarea>
      {/if}
   </td>
 </tr>
 <tr>
   <th valign="top">{i18n key=dao.schedule.fields.subscribe_evnt} : </th>
 </tr>
 <tr>
   <td valign="top">{radiobutton name=subscribeenabled_evnt values=$subcribedValues selected=$toEdit->subscribeenabled_evnt}</td>
 </tr>
</table>
</fieldset>
<br />
<fieldset>
<table>
{if $toEdit->id_evnt}
<tr>
   <th valign="top">{i18n key=dao.schedule.fields.date_evnt}</th><td>{$toEdit->date_evnt|datei18n}</td>
</tr>
{/if}
<tr>
   <th valign="top">{i18n key=dao.schedule.fields.datedisplayfrom_evnt}</th><td>{calendar name="datedisplayfrom_evnt" value=$toEdit->datedisplayfrom_evnt|datei18n}</td>
</tr>
<tr>
   <th valign="top">{i18n key=dao.schedule.fields.datedisplayto_evnt}</th><td>{calendar name="datedisplayto_evnt" value=$toEdit->datedisplayto_evnt|datei18n}</td>
</tr>
<tr>
   <th valign="top">{i18n key=dao.schedule.fields.datefrom_evnt}</th><td>{calendar name="datefrom_evnt" value=$toEdit->datefrom_evnt|datei18n}</td>
</tr>
<tr>
   <th valign="top">{i18n key=dao.schedule.fields.dateto_evnt} : </th>
   <td>{calendar name="dateto_evnt" value=$toEdit->dateto_evnt|datei18n}</td>
</tr>
</table>
</fieldset>

<p class="validButtons">
<input type="submit" value="{i18n key=copix:common.buttons.ok}">
<input type="button" value="{$WFLBestActionCaption}" onclick="return doUrl('{copixurl dest="schedule|admin|validEvnt" doBest=1}')" />
<input type="button" value="{i18n key="copix:common.buttons.cancel"}" onclick="javascript:window.location='{copixurl dest="schedule|admin|cancelEditEvnt"}'" />
</p>
</form>
