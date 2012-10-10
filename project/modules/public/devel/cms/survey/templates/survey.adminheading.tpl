<table class="CopixTable">
<thead>
 <tr>
  <th>{i18n key="copix:common.messages.title"}</th>
  <th style="width:28%">{i18n key="copix:common.actions.title"}</th>
 </tr>
</thead>
<tbody>
 <tr>
  <th>{i18n key="copixheadings|workflow.status.online"} ({$arSurvey|@count} {i18n key="copixheadings|workflow.messages.item"})</th>
  <th>{if $writeEnabled}<a href="{copixurl dest="admin|create" id_head=$id_head}" title="{i18n key="copix:common.buttons.new"}"><img src="{copixresource path="img/tools/new.png"}" alt="{i18n key="copix:common.buttons.new"}" /></a>{/if}</th>
 </tr>
 {if isset($arSurvey) and count($arSurvey)}
  {foreach from=$arSurvey item=survey}
   <tr {cycle values=',class="alternate"' name="arSurvey"}>
    <td>{$survey->title_svy}</td>
    <td>
     <a href="{copixurl dest="survey|admin|viewResult" id_svy=$survey->id_svy}" title="{i18n key="copix:common.buttons.show"}"><img src="{copixresource path="img/tools/show.png"}" alt="{i18n key="copix:common.buttons.show"}" /></a>
	 <a href="{copixurl dest="survey|admin|prepareEdit" id_svy=$survey->id_svy}" title="{i18n key="copix:common.buttons.update"}"><img src="{copixresource path="img/tools/update.png"}" alt="{i18n key="copix:common.buttons.update"}" /></a>
	 <a href="{copixurl dest="survey|admin|delete" id_svy=$survey->id_svy}" title="{i18n key="copix:common.buttons.delete"}"><img src="{copixresource path="img/tools/delete.png"}" alt="{i18n key="copix:common.buttons.delete"}" /></a>
    </td>
   </tr>
  {/foreach}
 {/if}
</tbody>
</table>