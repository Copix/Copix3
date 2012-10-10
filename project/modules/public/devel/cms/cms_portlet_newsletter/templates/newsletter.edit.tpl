{literal}
<script type="text/javascript">
//<![CDATA[
function doUrl (pUrl) {
{/literal}
{if $kind == "general"}
   var myForm = document.newsletterEdit;
   myForm.action = pUrl;
   myForm.submit ();
{else}
   document.location = pUrl;
{/if}
{literal}
}
//]]>
</script>
{/literal}
<ul class="copixCMSNav">
 <li {if $kind=="general"}class="copixCMSNavSelected"{/if}><a href="javascript:doUrl ('{copixurl dest="cms_portlet_newsletter||validEdit" kind="0"}')">{i18n key="newsletter.title.general"}</a></li>
 <li {if $kind=="preview"}class="copixCMSNavSelected"{/if}><a href="javascript:doUrl ('{copixurl dest="cms_portlet_newsletter||validEdit" kind="1"}')">{i18n key="newsletter.title.preview"}</a></li>
</ul>

{if $kind == "general"}
<form name="newsletterEdit" action="{copixurl dest="cms_portlet_newsletter||valid"}" method="post">
<fieldset>
 <table class="verticalTable">
  <tr>
   <th>{i18n key="newsletter.title.title"}</th>
   <td><input type="text" size="48" name="title" value="{$toEdit->title|escape}" /></td>
  </tr>
  <tr>
   <th>{i18n key="newsletter.title.group"}</th>
   <td><select name="id_group">
    {foreach from=$listGroup item=group}
     <option value="{$group->id_nlg}" {if $toEdit->id_group==$group->id_nlg}selected="selected"{/if}>{$group->name_nlg}</option>
    {/foreach}
   </select>
  </td>
 </tr>
 <tr>
  <th>{i18n key="newsletter.title.affichage"}</th>
  <td>{select name="template" values=$possibleKinds selected=$toEdit->templateId}</td>
 </tr>
 </table>
</fieldset>
<p class="validButtons">
 <input type="submit" value="{i18n key="copix:common.buttons.ok"}" />
 <input type="button" value="{i18n key="copix:common.buttons.cancel"}" onclick="javascript:document.location.href='{copixurl dest="cms|admin|cancelPortlet"}'" />
</p>
</form>
{/if}
{if $kind == "preview"}
 {$show}
{/if}