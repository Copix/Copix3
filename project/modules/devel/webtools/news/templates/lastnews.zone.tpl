<div class="zone_news">
<h2>Actualités <a class="lien_rss" href="{copixurl dest="news|default|rss"}"><img alt="RSS News" src="{copixresource path="img/icons/syndication.gif"}" /></a></h2>
{foreach from=$newsList item=newsObject}
<h3>{$newsObject->title_news|truncate:$nbCarMaxTitle:"..."|escape}<span class="date_news"> (le {$newsObject->date_news|datei18n})</span></h3>
<p>{$newsObject->summary_news|truncate:$nbCarMaxResume:"..."|escape}</p>
{copixurl dest="show" id_news=$newsObject->id_news comments=list title_news=$newsObject->title_news assign=moreUrl}
{assign var=id_news value=$newsObject->id_news}
{copixzone process="comments|comment" id="module=news;group=default;action=show;id_news=$id_news"  required=false moreUrl=$moreUrl}
<br />
<a href="{$moreUrl}">Lire la suite</a>
{/foreach}
<hr />
<a href="{copixurl dest="|"}">Accéder à la liste des news</a>
</div>