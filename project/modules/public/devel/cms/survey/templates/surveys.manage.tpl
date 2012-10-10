{if $arSurveys}
<table class="copixTable">
   <thead>
   <tr>
      <th>{i18n key="survey.messages.title"}</th>
      <th>{i18n key="copix:common.actions.title"}</th>
   </tr>
   </thead>
   <tbody>
   {foreach from=$arSurveys item=survey}
   <tr>
      <td>{$survey->title_svy}</td>
      <td><input type="button" onclick="document.location='{copixurl dest="survey|admin|prepareEdit" id_svy=$survey->id_svy}'" value="{i18n key="copix:common.buttons.update"}" />
          <input type="button" onclick="document.location='{copixurl dest="survey|admin|deleteSurvey" id_svy=$survey->id_svy}'" value="{i18n key="copix:common.buttons.delete"}" />
      </td>
   </tr>
   {/foreach}
   </tbody>
</table>
{/if}
<div align="center">{$pager}</div>
<br />
<br />
<input type="button" value="{i18n key=copix:common.buttons.back}" onclick="javascript:document.location='{copixurl dest="survey|admin|"}'" />
