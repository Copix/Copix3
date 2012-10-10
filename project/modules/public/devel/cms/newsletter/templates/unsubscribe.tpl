<p>{i18n key="newsletter.message.unsubscribe"}</p>
<form action="{copixurl dest="newsletter||validUnsubscribe" mail=$mail}" method="post">
<ul>{foreach from=$groups item=group}
   <li><input type="checkbox" name="id_nlg[]" value="{$group->id_nlg}" />{$group->name_nlg}</li>
   {/foreach}</ul>
    <input type="submit" value="{i18n key="copix:common.buttons.ok"}" />
    <input type="button" value="{i18n key="copix:common.buttons.cancel"}" onclick="javascript:window.location='{copixurl}'" />
</form>
