<h3>{i18n key='soap_server.handle.title'}</h3>
{if count ($ppo->arFunctions)}
 <ul>
 {foreach from=$ppo->arFunctions item=function}
  <li>{$function}</li>
 {/foreach}
 </ul>
{else}
 {i18n key=copix:common.none}
{/if}