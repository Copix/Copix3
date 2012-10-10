{*
* param objDocs .
* param: possibleKinds - tableau des templates possibles.
*}
{literal}
<script type="text/javascript">
//<![CDATA[
function doUrl (pUrl) {
{/literal}
   var myForm = document.docsEdit;
   myForm.action = pUrl;
   myForm.submit ();
{literal}
}
//]]>
</script>
{/literal}

<ul class="copixCMSNav">
 <li {if $kind=="general"}class="copixCMSNavSelected"{/if}><a href="javascript:doUrl ('{copixurl dest="cms_portlet_document||edit" kind="0"}')">{i18n key="document.tab.general"}</a></li>
 <li {if $kind=="preview"}class="copixCMSNavSelected"{/if}><a href="javascript:doUrl ('{copixurl dest="cms_portlet_document||edit" kind="1"}')">{i18n key="document.tab.preview"}</a></li>
</ul>

<form name="docsEdit" action="{copixurl dest="cms_portlet_document||valid"}" method="post">
{if $kind == "general"}
<fieldset>
<table>
  <tr>
   <th>{i18n key="cms_portlet_document|document.messages.subject"}</th>
   <td><input type="text" size="48" name="subject" value="{$objDocs->subject|escape}"/></td>
  </tr>
  <tr>
  <th>{i18n key="cms_portlet_document|document.messages.documents"}</th>
  <td>
      {if count ($arDocs)>0}
          <table>
              {foreach from=$arDocs item=document key=index}
              <tr>
                 <td>{$document->title_doc}</td><td>
                   <a href="javascript:doUrl ('{copixurl dest="cms_portlet_document||moveUp" id=$index}');"><img src="{copixresource path="img/tools/up.png"}" /></a>
                   <a href="javascript:doUrl ('{copixurl dest="cms_portlet_document||moveDown" id=$index}');"><img src="{copixresource path="img/tools/down.png"}" /></a>
                   <a href="#" onclick="javascript:doUrl('{copixurl dest="cms_portlet_document||deleteDocument" index=$index}');" alt="{i18n key="cms_portlet_document|document.messages.delete"}"><img src="{copixresource path="img/tools/delete.png"}" /></a>
                 </td>
              </tr>
              {/foreach}
          </table>
     {/if}
      <a href="#" onclick="javascript:doUrl('{copixurl dest="cms_portlet_document||selectDocument"}');"><img src="{copixresource path="img/tools/selectin.png"}" alt="{i18n key=copix:common.buttons.select}" /></a>
      </td>
  </tr>
  <tr>
   <th>{i18n key="cms_portlet_document|document.messages.kindDisplay"}</th>
   <td>{select name="template" values=$possibleKinds selected=$objDocs->templateId}</td>
  </tr>
</table>
</fieldset>
{else}
   {$preview}
{/if}
<p class="validButtons">
<input type="submit" value="{i18n key="copix:common.buttons.ok"}" />
<input type="button" value="{i18n key="copix:common.buttons.cancel"}" onclick="javascript:document.location.href='{copixurl dest="cms|admin|cancelPortlet"}'" />
</p>
</form>