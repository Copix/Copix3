{if strlen ($title) > 0}
	<h2 class="first information">{$title}</h2>
{/if}
	
{$message}

{if count ($links) > 0}
	<br /><br />
	{foreach from=$links item=caption key=url}
		<img src="{copixresource path="img/tools/next.png"}" /> <a href="{$url}">{$caption}</a><br />
	{/foreach}
{else}
	<br /><br />
	<img src="{copixresource path="img/tools/next.png"}" /> <a href="{$back}">{i18n key="messages.action.back"}</a>
{/if}