<h2>{i18n key='404.error.title'}</h2>
<p>{i18n key='404.pagenotfound'}</p>
<ul>
	<li><a href="{$ppo->home_url}">{i18n key='404.backtohomepage'}</a></li>
{if $ppo->referrer}
	<li><a href="{$ppo->referrer}">{i18n key='404.previouspage'}</a></li>
{/if}
{if $ppo->sitemap_url}
	<li><a href="{$ppo->sitemap_url}">{i18n key='404.sitemap'}</a></li>
{/if}
{if $ppo->search_url}
	<li><a href="{$ppo->search_url}">{i18n key='404.searchsite'}</a></li>
{/if}
</ul>