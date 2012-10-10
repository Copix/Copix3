{*
* param objSurvey .
* param: possibleKinds - tableau des templates possibles.
*}
{literal}
<script type="text/javascript">
//<![CDATA[
function doUrl (pUrl) {
{/literal}
   var myForm = document.surveyEdit;
   myForm.action = pUrl;
   myForm.submit ();
{literal}
}
//]]>
</script>
{/literal}

<form name="surveyEdit" action="{copixurl dest="cms_portlet_survey||valid"}" method="post" class="copixForm">
 <fieldset>
  <table>
   <tr>
	<th>{i18n key="cms_portlet_survey|survey.messages.heading"}</th>
	<td>{if $headingName}{$headingName}
        {else}{i18n key="copix:common.none"} {/if}
        {copixurl dest="cms_portlet_survey|default|edit" assign="urlSelect"}
        {copixurl dest="cms_portlet_survey|default|edit" assign="urlBack"}
        <a href="#" onclick="javascript:doUrl('{copixurl dest="copixheadings|admin|selectHeading" select=$urlSelect back=$urlBack}');"><img src="{copixresource path="img/tools/selectin.png"}" alt="{i18n key="cms_portlet_survey|survey.messages.select"}" /></a>
    </td>
   </tr>
   <tr>
    <th>{i18n key="cms_portlet_survey|survey.messages.listPage"}</th>
    <td>
	 {if $objSurvey->urllist}{$pageName}{else} {i18n key="cms_portlet_survey|survey.messages.noPage"}{/if}
     <a href="#" onclick="javascript:doUrl('{copixurl dest="cms_portlet_survey||fromPageUpdate"}');"><img src="{copixresource path="img/tools/selectin.png"}" alt="{i18n key="cms_portlet_survey|survey.messages.select"}" /></a>
     <input type="hidden" value="{$objSurvey->urllist}" name="urllist" />
    </td>
   </tr>
  </table>
 </fieldset>
 <fieldset>
  <table>
   <tr>
    <th>{i18n key="cms_portlet_survey|survey.messages.survey"}</th>
    <td>
     {if count($arSurvey)}
      <select name="id_svy">
       {foreach from=$arSurvey item=survey name=counterSurvey}
        {if ($objSurvey->id_svy eq $survey->id_svy) or ((strlen ($objSurvey->id_svy) <= 0) and ($smarty.foreach.counterSurvey.iteration == 0))}
         <option value="{$survey->id_svy}" selected="selected">{$survey->title_svy}</option>
        {else}
         <option value="{$survey->id_svy}" >{$survey->title_svy}</option>
        {/if}
      </select>
        {/foreach}
     {else}
      {i18n key="survey.messages.noSurvey"}
      {i18n key="survey.messages.addSurvey"} <a href="{copixurl dest="survey|admin|contrib"}">{i18n key="survey.messages.here"}</a>.
     {/if}
    </td>
   </tr>
   <tr>
	<th>{i18n key="cms_portlet_survey|survey.messages.kindDisplay"}</th>
    <td>{select name="template" values=$possibleKinds objectMap="id_ctpl;frontCaption" selected=$objSurvey->template}</td>
   </tr>
  </table>
 </fieldset>
 <input type="submit" value="{i18n key="copix:common.buttons.ok"}" />
 <input type="button" value="{i18n key="copix:common.buttons.cancel"}" onclick="javascript:document.location.href='{copixurl dest="cms|admin|cancelPortlet"}'" />
</form>