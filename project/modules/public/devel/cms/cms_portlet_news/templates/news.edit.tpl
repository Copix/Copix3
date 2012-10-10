{*
* template de modification d'une enquête.
* param objNews l'enquête a éditer.
* param: possibleKinds - tableau des templates possibles.
*}
{literal}
<script type="text/javascript">
//<![CDATA[
function doUrl (pUrl) {
	{/literal}
	{if $kind == "general"}
	var myForm = document.newsEdit;
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
 <li {if $kind=="general"}class="copixCMSNavSelected"{/if}><a href="javascript:doUrl ('{copixurl dest="cms_portlet_news||validedit" kind="0"}')">{i18n key="news.title.general"}</a></li>
 <li {if $kind=="preview"}class="copixCMSNavSelected"{/if}><a href="javascript:doUrl ('{copixurl dest="cms_portlet_news||validedit" kind="1"}')">{i18n key="news.title.preview"}</a></li>
</ul>

{if $kind == "general"}
<form name="newsEdit" action="{copixurl dest="cms_portlet_news||valid"}" method="post">
 <fieldset>
  <table>
   <tr>
	 <th><label for="subject">{i18n key="news.messages.subject"}</label></th>
	 <td><input type="text" size="48" id="subject" name="subject" value="{$objNews->subject|escape}"/></td>
	</tr>
    <tr>
     <th><label for="numToShow">{i18n key="news.messages.nbNews"}</label></th>
     <td><input type="text" size="4" value="{$objNews->numToShow}" name="numToShow" />
         <label for="fromCountLastNews">{i18n key="news.messages.fromCountLastNews"}</label> <input type="text" size="4" value="{$objNews->fromCountLastNews}" name="fromCountLastNews" />
     </td>
    </tr>
	<tr>
     <th>{i18n key="news.messages.heading"}</th>
     <td>{if $headingName}{$headingName} <input type="hidden" value="{$objNews->id_head}" name="id_head" />
             {else}{i18n key="copix:common.none"} {/if}<a href="#" onclick="javascript:doUrl('{copixurl dest="copixheadings|admin|selectHeading" select=$select back=$back}');"><img src="{copixresource path="img/tools/selectin.png"}" alt="{i18n key="news.select"}" /></a></td>
	</tr>
    <tr>
	 <th>{i18n key="news.messages.detailPage"}</th>
	 <td>{if $objNews->urldetail}{$pageName}{else} {i18n key="news.noPageLinked"}{/if}
            <a href="#" onclick="javascript:doUrl('{copixurl dest="cms_portlet_news||fromPageUpdate"}');"><img src="{copixresource path="img/tools/selectin.png"}" alt="{i18n key="news.select"}" /></a>
            <input type="hidden" value="{$objNews->urldetail}" name="urlback" />
     </td>
    </tr>
    <tr>
	 <th><label for="template">{i18n key="news.messages.displayKind"}</template></th>
	 <td>{select name="template" values=$possibleKinds selected=$objNews->templateId}</td>
    </tr>
   </table>
 </fieldset>

 <p class="validButtons">
  <input type="submit" value="{i18n key="copix:common.buttons.ok"}" />
  <input type="button" value="{i18n key="copix:common.buttons.cancel"}" onclick="javascript:document.location.href='{copixurl dest="cms|admin|cancelPortlet"}'" />
 </p>
</form>
{formfocus id=subject}
{/if}

{if $kind == "preview"}
   {$show}
{/if}
