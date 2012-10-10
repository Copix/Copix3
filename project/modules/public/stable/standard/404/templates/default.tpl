<h2>{i18n key='404.error.title'}</h2>
<p>{i18n key='404.pagenotfound'}</p>
<a href="{$ppo->home_url}" alt="{i18n key='404.backtohomepage'}">{i18n key='404.backtohomepage'}</a>
|<a href="javascript:history.go(-1)" title="{i18n key='404.previouspage'}">{i18n key='404.previouspage'}</a>
{if $ppo->sitemap_url}
|<a href="{$ppo->sitemap_url}" title="{i18n key='404.sitemap'}">{i18n key='404.sitemap'}</a>
{/if}
{if $ppo->search_url}
|<a href="{$ppo->search_url}" title="{i18n key='404.searchsite'}">{i18n key='404.searchsite'}</a>
{/if}