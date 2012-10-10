{if $noNews}
 {i18n key="cms_portlet_newsdetail|cms_portlet_newsdetail.noValidNews"}
{else}
 <h2>{$detail_titre}</h2>
 <p>{$detail_content}</p>
 <p>{$detail_date|datei18n}</p>
{/if}

{if $detail_urlback}
 <p><a href="{copixurl dest="cms||get" id=$detail_urlback}">{i18n key="cms_portlet_newsdetail|cms_portlet_newsdetail.back"}</a></p>
{/if}