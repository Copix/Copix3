{*
* template de modification d'une de la zone de recherche
* param objArticle l'enquête a éditer.
* param: possibleKinds - tableau des templates possibles.
*}
{literal}
<script type="text/javascript">
//<![CDATA[
function doUrl (pUrl) {
{/literal}
    {if $kind == "general"}
    var myForm = document.formsEdit;
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
  <li {if $kind=="general"}class="copixCMSNavSelected"{/if}><a href="javascript:doUrl ('{copixurl dest="cms_portlet_searchengine||validedit" kind="0"}')">{i18n key="searchengine.tab.general"}</a></li>
  <li {if $kind=="preview"}class="copixCMSNavSelected"{/if}><a href="javascript:doUrl ('{copixurl dest="cms_portlet_searchengine||validedit" kind="1"}')">{i18n key="searchengine.tab.preview"}</a></li>
</ul>

{if $kind == "general"}
<form name="formsEdit" action="{copixurl dest="cms_portlet_searchengine||valid"}" method="post">
   <fieldset>
   <table class="verticalTable">
      <tr>
        <th>{i18n key="searchengine.input.title"} :</th>
		<td><input type="text" name="title" value="{$toEdit->title|escape:"html"}" /></td>
	  </tr>
      <tr>
        <th>{i18n key="searchengine.input.size"} :</th>
		<td><input type="text" name="size" value="{$toEdit->size|escape:"html"}" /></td>
	  </tr>
      <tr>
        <th>{i18n key="searchengine.input.presentation"} :</th>
		<td><textarea name="presentation_text" cols="40" rows="5">{$toEdit->presentation_text|escape:"html"}</textarea></td>
	  </tr>
      <tr>
		 <th>{i18n key="searchengine.title.affichage"} :</th>
		 <td>{select name="template" values=$possibleKinds selected=$toEdit->templateId}</td>
		</tr>
		<tr>
			<th>{i18n key="searchengine.title.detailPage"} </th>
         <td>{if $toEdit->idPortletResultPage}{$pageName}{else}{i18n key="searchengine.title.noPage"} {/if}
              <input type="hidden" value="{$toEdit->idPortletResultPage}" name="idPortletResultPage" />
              <a href="#" onclick="javascript:doUrl('{copixurl dest="cms_portlet_searchengine||fromPage"}');"><img src="{copixresource path="img/tools/selectin.png"}" alt="{i18n key="copix:common.buttons.select"}" /></a>
         </td>
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
