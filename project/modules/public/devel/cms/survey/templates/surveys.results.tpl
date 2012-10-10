<h2>{$survey->title_svy}</h2>
<table class="CopixTable">
{foreach from=$survey->option_svy item=option}
 <tr>
  <th>{$option->title}</th>
  <td><img src="{copixresource path="img/modules/survey/survey_left.gif"}" alt="leftBar" />{if $option->response neq 0 && $survey->response_svy neq 0}<img src="{copixresource path="img/modules/survey/survey_bar.gif"}" alt="Bar" height="12" width="{$option->response/$survey->response_svy*50}" alt="leftBar" />{/if}<img src="{copixresource path="img/modules/survey/survey_right.gif"}" alt="rightBar" /></td>
  <td>{if $option->response neq 0 && $survey->response_svy neq 0}{$option->response/$survey->response_svy*100|string_format:"%.2f"}% [{$option->response}]{else}0% [0]{/if}</td>
 </tr>
{/foreach}
</table>
<br />
<input type="button" value="{i18n key=copix:common.buttons.back}" onclick="javascript:document.location='{copixurl dest="copixheadings|admin|" browse="survey" level=$survey->id_head}'" />