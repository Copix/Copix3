<h2>{$portletTitle}</h2>
 <form name="suscribe_nl" action="{copixurl dest="newsletter||subscribe"}" method="post" >
 <div style="text-align:left">
  <input type="hidden" name="id_nlg[]" value="{$idGroup}" />
  <input type="text" name="mail" value="{i18n key="cms_portlet_newsletter|newsletter.portlet.email"}" />
  {if $submit}
   <input type="submit" value="{i18n key=copix:common.buttons.ok}" />
  {else}
   <input type="button" value="{i18n key=copix:common.buttons.ok}" />
  {/if}
 </div>
</form>