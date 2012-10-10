{if (count ($ppo->arErrors) == 0)}true{else}
	{if count ($ppo->arErrors)}
		{if count ($ppo->arErrors) == 1}
			{assign var=title_key value='global.title.error'}
		{else}
			{assign var=title_key value='global.title.errors'}
		{/if}
		<div class="errorMessage">
		<h1>{i18n key="$title_key"}</h1>
		{ulli values=$ppo->arErrors}
		</div>
	{/if}
{/if}