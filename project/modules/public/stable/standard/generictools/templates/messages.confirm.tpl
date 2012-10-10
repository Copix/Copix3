<h2>{$title}</h2>
{if $message}
<p>{$message}</p>
{/if}

<a href="{$confirm}"><img src="{copixresource path="img/tools/valid.png"}" alt="copix:common.buttons.yes" />{i18n key="copix:common.buttons.yes"}</a>
<a href="{$cancel}"><img src="{copixresource path="img/tools/cancel.png"}" alt="copix:common.buttons.no" />{i18n key="copix:common.buttons.no"}</a>