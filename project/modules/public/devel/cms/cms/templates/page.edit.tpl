{literal}
<script type="text/javascript">
//<![CDATA[
function doUrl (pUrl) {
{/literal}
{if $kind == "general"}
   var myForm = document.pageEdit;
   myForm.action = pUrl;
   myForm.submit ();
{else}
   document.location.href=pUrl;
{/if}
   return false;
{literal}
}
//]]>
</script>
{/literal}

{if $showErrors}
<div class="errorMessage">
 <h1>{i18n key=copix:common.messages.error}</h1>
 {ulli values=$arErrors}
</div>
{/if}

<ul class="copixCMSNav">
 <li {if $kind=="general"}class="copixCMSNavSelected"{/if}><a href="{copixurl dest="cms|admin|validedit" kind="0"}" onclick="return doUrl ('{copixurl dest="cms|admin|validedit" kind="0"}')">{i18n key="page.title.general"}</a></li>
 <li {if $kind=="content"}class="copixCMSNavSelected"{/if}><a href="{copixurl dest="cms|admin|validedit" kind="1"}" onclick="return doUrl ('{copixurl dest="cms|admin|validedit" kind="1"}')">{i18n key="page.title.content"}</a></li>
 <li {if $kind=="preview"}class="copixCMSNavSelected"{/if}><a href="{copixurl dest="cms|admin|validedit" kind="2"}" onclick="return doUrl ('{copixurl dest="cms|admin|validedit" kind="2"}')">{i18n key="page.title.preview"}</a></li>
</ul>

<form name="pageEdit" action="{copixurl dest="cms|admin|valid"}" method="post" class="copixForm">
{if $kind == "general"}
<fieldset>
   <table>
       <tr>
        <th>{i18n key=dao.cmsheading.fields.id_cmsh}</th>
        <td>{$heading->caption_head}</td>
       </tr>
       <tr>
        <th><label for="titlebar_cmsp">{i18n key=dao.cmspage.fields.titlebar_cmsp}</label></th>
        <td><input size="48" type="text" value="{$toEdit->titlebar_cmsp|escape}" id="titlebar_cmsp" name="titlebar_cmsp" /></td>
       </tr>
       <tr>
        <th><label for="title_cmsp">{i18n key=dao.cmspage.fields.title_cmsp}</label></th>
        <td><input size="48" type="text" value="{$toEdit->title_cmsp|escape}" id="title_cmsp" name="title_cmsp" /></td>
       </tr>
       <tr>
        <th><label for="summary_cmsp">{i18n key=dao.cmspage.fields.summary_cmsp}</label></th>
        <td><textarea cols="40" rows="5" id="summary_cmsp" name="summary_cmsp">{$toEdit->summary_cmsp|escape}</textarea></td>
       </tr>
       <tr>
        <th><label for="keywords_cmsp">{i18n key=dao.cmspage.fields.keywords_cmsp}</label></th>
        <td><textarea name="keywords_cmsp" id="keywords_cmsp" cols="40" rows="5">{$toEdit->keywords_cmsp|escape}</textarea></td>
       </tr>
   </table>
</fieldset>

<br />

<fieldset>
   <table>
       <tr>
        <th><label for="datemin_cmsp">{i18n key=dao.cmspage.fields.datemin_cmsp}</label></th>
        {assign var=myDate value=$toEdit->datemin_cmsp}
        <td>{calendar name="datemin_cmsp" value=$myDate|datei18n}</td>
       </tr>
       <tr>
        <th><label for="datemax_cmsp">{i18n key=dao.cmspage.fields.datemax_cmsp}</label></th>
        {assign var=myDate value=$toEdit->datemax_cmsp}
        <td>{calendar name="datemax_cmsp" value=$myDate|datei18n}</td>
       </tr>
   </table>
</fieldset>

<br />

<fieldset>
   <table>
       <tr>
        <th><label for="templateId">{i18n key="page.title.template"}</label></th>
        <td>{select name="templateId" values=$possibleKinds selected=$toEdit->templateId}</td>
       </tr>
   </table>
</fieldset>
{/if}

{if $kind == "content" || $kind == "preview"}
   {$parsedToEdit}
{/if}
<br style="clear: both" />
<p class="validButtons">
	<input type="button" value="{i18n key=copix:common.buttons.save}"  onclick="return doUrl('{copixurl dest=cms|admin|valid}')"/>
	<input type="button" value="{$WFLBestActionCaption}" onclick="return doUrl('{copixurl dest=cms|admin|valid doBest=1}')" />
	<input type="button" value="{i18n key=copix:common.buttons.cancel}" onclick="document.location.href='{copixurl dest="cms|admin|cancel"}'" />
</p>
</form>
