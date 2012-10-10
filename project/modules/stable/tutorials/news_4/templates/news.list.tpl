{foreach from=$ppo->arNews item=newsObject}
<h2>[{$newsObject->date_news|datei18n}]{$newsObject->title_news}</h2>
{$newsObject->summary_news}

<br />
{copixurl dest="show" id_news=$newsObject->id_news comments=list assign=moreUrl}
{assign var=id_news value=$newsObject->id_news}
{copixzone process="comments|comment" id="module;group;action=show;id_news=$id_news"  required=false moreUrl=$moreUrl}

<br />
<a href="{$moreUrl}">Lire la suite</a>
{/foreach}