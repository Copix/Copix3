{foreach from=$ppo->arNews item=newsObject}
<h2>[{$newsObject->date_news|datei18n}]{$newsObject->title_news}</h2>
{$newsObject->summary_news}
{/foreach}