<h2>[{$ppo->news->date_news|datei18n}]{$ppo->news->title_news}</h2>
{$ppo->news->summary_news}

<hr />

{$ppo->news->content_news}

<hr />

{copixzone process="comments|comment" id="module;group;action;id_news" required=false}
<br />
<a href="{copixurl dest="|"}">Retour à la liste</a>