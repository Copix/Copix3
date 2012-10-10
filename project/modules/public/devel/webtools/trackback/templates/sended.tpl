{if $ppo->response->error==true}
	<p>Une erreur est survenue:</p>
	<p>{$ppo->response->message}</p>
{else}
	<p>Le trackback a été créé</p>
{/if}