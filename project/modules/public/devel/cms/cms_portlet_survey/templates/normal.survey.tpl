<h2>{$survey->title_svy}</h2>

{if $result}
 <table class="CopixTable">
  {foreach from=$survey->option_svy item=option}
   <tr>
    <th>{$option->title}</th>
    <td><img src="{copixresource path="img/modules/survey/survey_left.gif"}" alt="-" />{if $option->response neq 0 && $survey->response_svy neq 0}<img src="{copixresource path="img/modules/survey/survey_bar.gif"}" alt="Bar" height="12" width="{$option->response/$survey->response_svy*50}" alt="{$option->response/$survey->response_svy*100|string_format:"%.2f"}%" />{/if}<img src="{copixresource path="img/modules/survey/survey_right.gif"}" alt="-" /></td>
    <td>{if $option->response neq 0 && $survey->response_svy neq 0}{$option->response/$survey->response_svy*100|string_format:"%.2f"}% [{$option->response}]{else}0% [0]{/if}</td>
   </tr>
  {/foreach}
 </table>
{else}
 <form action="{copixurl dest="survey||vote"}" method="post" class="copixForm">
  <table>
   {foreach from=$survey->option_svy item=option key=index}
    <tr>
     <th>{$option->title}</th>
     <td><input type="radio" class="radio" name="{$survey->id_svy}" value="{$index}"/></td>
    </tr>
    {/foreach}
  </table>
  <input type="hidden" name="back" value="{$url|escape}" />
  <input type="hidden" name="id" value="{$survey->id_svy}" />
  <input type="submit" value="{i18n key=cms_portlet_survey|survey.button.vote}" />
 </form>

 <br />

 <a href="{$url}&amp;forceResult=true" >{i18n key="cms_portlet_survey|survey.messages.seeResult"}</a>
{/if}

{if isset($urllist)}
 <br /><a href="{$urllist}&amp;id_svy={$survey->id_svy}" >{i18n key="cms_portlet_survey|survey.messages.seeAllSurvey"}</a>
{/if}