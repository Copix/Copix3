{*
* Formulaire d'ajout de la portlet agenda
* param: possibleKinds - tableau des templates possibles.
*}
{literal}
<script type="text/javascript">
//<![CDATA[
function doUrl (pUrl) {
{/literal}
{if $kind == "general"}
   var myForm = document.scheduleEdit;
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
 <li {if $kind=="general"}class="copixCMSNavSelected"{/if}><a href="javascript:doUrl ('{copixurl dest="cms_portlet_schedule||validedit" kind="0"}')" alt="{i18n key="schedule.title.general"}">{i18n key="schedule.title.general"}</a></li>
 <li {if $kind=="preview"}class="copixCMSNavSelected"{/if}><a href="javascript:doUrl ('{copixurl dest="cms_portlet_schedule||validedit" kind="1"}')" alt="{i18n key="schedule.title.preview"}">{i18n key="schedule.title.preview"}</a></li>
</ul>
{if $kind == "general"}
<form name="scheduleEdit" action="{copixurl dest="cms_portlet_schedule||valid"}" method="post">
   <fieldset>
   <table>
      <tr>
			<th>{i18n key=schedule.message.heading}</th>
			<td>{if $headingName}{$headingName} <input type="hidden" value="{$objSchedule->id_head}" name="id_head" />{else}{i18n key="copix:common.none"} {/if}<a href="#" onclick="javascript:doUrl('{copixurl dest="cms_portlet_schedule||selectHeading"}');" alt"select page"><img src="{copixresource path="img/tools/selectin.png"}" alt="{i18n key="schedule.select"}" /></a></td>
      </tr>
    <tr>
	 <th>{i18n key="schedule.messages.detailPage"}</th>
	 <td>{if $objSchedule->id_page_subscribe}{$pageName}{else} Aucune{/if}
            <a href="#" onclick="javascript:doUrl('{copixurl dest="cms_portlet_schedule||selectPage"}');"><img src="{copixresource path="img/tools/selectin.png"}" alt="{i18n key="schedule.select"}" /></a>
     </td>
    </tr>
      <tr>
		 <th>{i18n key=schedule.message.templateType}</th>
		 <td>{select name="template" values=$possibleKinds selected=$objSchedule->templateId}</td>
   	</tr>

	</table>
	</fieldset>
	<p class="validButtons">
	<input type="submit" value="{i18n key="copix:common.buttons.ok"}" />
	<input type="button" value="{i18n key="copix:common.buttons.cancel"}" onClick="javascript:document.location.href='{copixurl dest="cms|admin|cancelPortlet"}'" />
   </p>
</form>
{/if}
{if $kind == "preview"}
   {$show}
{/if}