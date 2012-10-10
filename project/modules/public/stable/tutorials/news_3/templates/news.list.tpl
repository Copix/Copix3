{foreach from=$ppo->arNews item=newsObject}
<h2>[{$newsObject->date_news|datei18n}]{$newsObject->title_news}</h2>
{$newsObject->summary_news}
<a href="{copixurl dest="show" id_news=$newsObject->id_news} ">Lire la suite</a>
{/foreach}