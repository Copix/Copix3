{if $title}<h2>{$title}</h2>{/if}

{if not $noForm}
 <form name="setCriteria" action="{copixurl dest="cms||get" id=$toShow->idPortletResultPage}" method="post">
{/if}
{if $toShow->presentation_text}
 <p>{$toShow->presentation_text}</p>
{/if}

{if not $defaultText}
 {i18n key="cms_portlet_searchengine|searchengine.default.text" assign=defaultText}
{/if}

<input type="text" name="criteria" value="{$keywords|default:$defaultText|escape}" size="{$size}" /><input type="{if $noForm}button{else}submit{/if}" value="{i18n key=copix:common.buttons.search}" />

{if not $noForm}
 </form>
{/if}