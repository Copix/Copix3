{if $subject}<h2>{$subject}</h2>{/if}
{foreach from=$arNews item=news name=news}
{if $subject}<h3>{else}<h2>{/if}{$news->title_news}[{$news->datewished_news|datei18n}]{if $subject}</h3>{else}</h2>{/if}
{$news->summary_news}{if ($news->content_news<>'') && ($url != null)}<a href="{copixurl dest=cms||get id=$url newsId=$news->id_news}">{i18n key=cms_portlet_news|news.action.more}</a>{/if}
{if $commentEnabled}{if $url !== null}{copixurl dest="cms||get" id=$url newsId=$news->id_news assign=urlDetail}{commentthis dest=$urlDetail id=$news->id_news type="news" displaytype="link"}{else}{commentthis back=$back|urlencode id=$news->id_news type="news" displaytype="link"}{/if}{/if}
{/foreach}