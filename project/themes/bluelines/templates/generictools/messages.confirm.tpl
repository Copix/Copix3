{if ($title)}
	<h2 class="first">{$title}</h2>
{/if}

<center>
	{if $message}
		{$message}
		<br /><br />
	{/if}

	{button img="img/tools/valid.png" captioni18n="copix:common.buttons.yes" url=$confirm}
	&nbsp;
	{button img="img/tools/cancel.png" captioni18n="copix:common.buttons.no" url=$cancel}
</center>