<h2>{i18n key="newsletter.title.sendGroup" titlePage=$newsletter->title_cmsp}</h2>
{if $error}
{$error}
{/if}
<form action="{copixurl dest="newsletter|admin|sendToGroup" id=$newsletter->publicid_cmsp}" method="post">
<p>{i18n key="newsletter.messages.groupSelect"}</p>
{i18n key="newsletter.messages.newsletterGroups"} :
<ul>{foreach from=$groups item=group}
<li><input type="checkbox" name="id_nlg[]" value="{$group->id_nlg}" {if $group->checked}checked="checked"{/if} />{$group->name_nlg}</li>
{/foreach}</ul>
{i18n key="newsletter.messages.copixGroups"} :
<ul>{foreach from=$copixGroups item=group}
<li><input type="checkbox" name="id_cgrp[]" value="{$group->id_cgrp}" {if $group->checked}checked="checked"{/if} />{$group->name_cgrp}</li>
{/foreach}</ul>
<p class="validButtons">
<input type="submit" value="{i18n key="copix:common.buttons.ok"}" />
<input type="button" value="{i18n key="copix:common.buttons.cancel"}" onclick="javascript:document.location='{copixurl dest="copixheadings|admin|" level=$newsletter->id_head browse="newsletter"}'" />
</p>
</form>
