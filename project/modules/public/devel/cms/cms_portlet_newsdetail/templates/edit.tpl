{literal}
<script type="text/javascript">
//<![CDATA[
function doUrl (pUrl) {
{/literal}
{if $kind == "general"}
   var myForm = document.addNewsDetail;
   myForm.action = pUrl;
   myForm.submit ();
{else}
   document.location.href=pUrl;
{/if}
{literal}
}
//]]>
</script>
{/literal}

<ul class="copixCMSNav">
 <li {if $kind=="general"}class="copixCMSNavSelected"{/if}><a href="javascript:doUrl ('{copixurl dest="cms_portlet_newsdetail||validEdit" kind="0"}')">{i18n key="cms_portlet_newsdetail|cms_portlet_newsdetail.common.general"}</a></li>
 <li {if $kind=="preview"}class="copixCMSNavSelected"{/if}><a href="javascript:doUrl ('{copixurl dest="cms_portlet_newsdetail||validEdit" kind="1"}')">{i18n key="cms_portlet_newsdetail|cms_portlet_newsdetail.common.preview"}</a></li>
</ul>

{if $kind == "general"}
<form name="addNewsDetail" id="addNewsDetail" action="{copixurl dest="cms_portlet_newsdetail||valid"}" method="post">
<fieldset>
<table>
	<tr>
		<th>{i18n key="cms_portlet_newsdetail|cms_portlet_newsdetail.urlback"}</th>
      <td>{if $toEdit->detail_urlback}{$pageName}{else} {i18n key="cms_portlet_newsdetail|cms_portlet_newsdetail.noPageLinked"}{/if} <input type="hidden" value="{$toEdit->detail_urlback}" name="detail_urlback" />
          <a href="#" onclick="javascript:doUrl('{copixurl dest="cms_portlet_newsdetail||fromPageUpdate"}');"><img src="{copixresource path="img/tools/selectin.png"}" alt="{i18n key="cms_portlet_newsdetail|cms_portlet_newsdetail.select"}" /></a></td>
		</td>
	</tr>
    <tr>
	 <th><label for="template">{i18n key="cms_portlet_newsdetail.messages.displayKind"}</template></th>
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
