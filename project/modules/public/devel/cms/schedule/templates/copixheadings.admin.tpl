<h2>{i18n key=schedule.title.adminList}</h2>
{if $nbEventsToPublish && $publishEnabled }
   <a href="{copixurl dest="admin|" }" title="{i18n key="copix:common.buttons.warning"}"><img src="{copixresource path="img/tools/warning.png"}# alt="{i18n key="copix:common.buttons.warning"}" /></a>
{/if}
{if $nbEventsPublished }
   <a href="{copixurl dest="admin|" }" title="{i18n key="copix:common.buttons.show"}"><img src="{copixresource path="img/tools/loupe.png"}" alt="{i18n key="copix:common.buttons.show"}" /></a>
{/if}
{if $manageEnabled}
   <a href="{copixurl dest="admin|getAdminEvent"}" title="{i18n key="copix:common.buttons.new"}"><img src="{copixresource path="img/tools/new.png"}" alt="{i18n key="copix:common.buttons.new"}" /></a>
{/if}
