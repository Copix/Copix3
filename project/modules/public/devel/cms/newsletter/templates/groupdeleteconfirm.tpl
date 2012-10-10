<h2>{i18n key="newsletter.title.groupToDelete" group=$group->name_nlg}</h2>

{if $group->mail_count > 0}
   {i18n key="newsletter.messages.confirmDeleteMove"}
   <form action="{copixurl dest="newsletter|groups|delete" id_nlg=$group->id_nlg id_head=$id_head}" method="post">
      <br />
      {i18n key="newsletter.messages.moveQuestion"}
      {select name="moveTo" values=$groups objectMap="id_nlg;name_nlg"}
      <input type="submit" value="{i18n key="copix:common.buttons.select"}" />
      <input type="button" value="{i18n key="copix:common.buttons.cancel"}" onclick="javascript:document.location='{copixurl dest="copixheadings|admin|" browse="newsletter" level=$id_head kind="1"}'" />
   </form>
{else}
   {i18n key="newsletter.messages.confirmDelete"}
   <input type="button" value="{i18n key="copix:common.buttons.confirm"}" onclick="javascript:document.location='{copixurl dest="newsletter|groups|delete" id_nlg=$group->id_nlg forceDelete="1"}'" />
   <input type="button" value="{i18n key="copix:common.buttons.cancel"}" onclick="javascript:document.location='{copixurl dest="copixheadings|admin|" browse="newsletter" level=$id_head kind="1"}'" />
{/if}
