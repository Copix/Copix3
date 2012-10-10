<marquee id="scroller" scrollamount="2" direction="up" height="90"
onmouseover="javascript:scroller.stop()" onmouseout="javascript:scroller.start()">
{foreach from=$arNews item=news name=news}
<h2>{$news->title_news}</h2>
<p>{$news->summary_news}</p>
<p>{if $news->content_news<>'' && ($url !== null)}<a href='{$url}&newsId={$news->id_news}'>{i18n key=cms_portlet_news|news.action.more}</a>{/if}</p>
{/foreach}
</marquee>