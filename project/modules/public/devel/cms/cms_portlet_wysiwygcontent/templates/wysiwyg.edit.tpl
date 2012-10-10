{*
* template de modification d'une enquête.
* param edited l'enquête a éditer.
* param: possibleKinds - tableau des templates possibles.
*}
{literal}
<script type="text/javascript">
//<![CDATA[
function doUrl (pUrl) {
{/literal}
{if $kind == "general"}
   var myForm = document.articleEdit;
   myForm.action = pUrl;
   if (typeof myForm.onsubmit == "function")
      myForm.onsubmit();
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

<ul class="copixCMSNav">
 <li {if $kind=="general"}class="copixCMSNavSelected"{/if}><a href="{copixurl dest="cms_portlet_article||validedit" kind="0"}" onclick="return doUrl ('{copixurl dest="cms_portlet_wysiwygcontent||validedit" kind="0"}')">{i18n key="wysiwyg.title.general"}</a></li>
 <li {if $kind=="preview"}class="copixCMSNavSelected"{/if}><a href="{copixurl dest="cms_portlet_article||validedit" kind="1"}" onclick="return doUrl ('{copixurl dest="cms_portlet_wysiwygcontent||validedit" kind="1"}')">{i18n key="wysiwyg.title.preview"}</a></li>
</ul>

{if $kind == "general"}
<form name="articleEdit" action="{copixurl dest="cms_portlet_wysiwygcontent||valid"}" method="post" class="copixForm">
<fieldset>
<table>
 <tr>
  <th><label for="subject">{i18n key="wysiwyg.messages.subject"}</label></th>
 </tr>
 <tr>
  <td><input size="48" type="text" id="subject" name="subject" value="{$edited->subject|escape}"/></td>
 </tr>
 <tr>
  <th><label for="text_content">{i18n key="wysiwyg.messages.content"}</label></th>
 </tr>
 <tr>
  <td>{htmleditor name="text_content" content=$edited->text_content}</td>
 </tr>
 <tr>
  <th><label for="template">{i18n key="wysiwyg.messages.displayKind"}</label></th>
 </tr>
 <tr>
  <td>{select name="template" values=$possibleKinds selected=$edited->templateId}</td>
 </tr>
</table>
</fieldset>
<p>
<input type="submit" value="{i18n key="copix:common.buttons.ok"}" />
<input type="button" value="{i18n key="copix:common.buttons.cancel"}" onclick="javascript:document.location.href='{copixurl dest="cms|admin|cancelPortlet"}'" />
</p>
</form>
{/if}

{formfocus id="subject"}

{if $kind == "preview"}
   {$show}
{/if}
