{if count($arTheme)}
 <ul>
   <li>{i18n key=copix:common.none}<a href="{copixurl appendFrom=$validUrl id_ctpt=''}"><img src="{copixurl}img/tools/{if $selectedTheme == $theme->id_ctpt}selected.png{else}select.png{/if}" alt="{i18n key=copix:common.buttons.select}"></a></li>
 {foreach from=$arTheme item=theme}
  <li>{$theme}<a href="{copixurl appendFrom=$validUrl id_ctpt=$theme}"><img src="{copixurl}img/tools/{if $selectedTheme == $theme}selected.png{else}select.png{/if}" alt="{i18n key=copix:common.buttons.select}"></a></li>
 {/foreach}
 </ul>
{/if}