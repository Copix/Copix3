{if $ppo->hidden_news eq true}
	<div class="news_a_paraitre">
	<strong>News à paraitre</strong>
	{foreach from=$ppo->arNewsNonParues item=newsObject}
	{assign var=id_news value=$newsObject->id_news}
	<h2>{$newsObject->title_news}<span class="date_news"> (le {$newsObject->date_news|datei18n:text} à {$newsObject->heure_news|hour_format:"%H:%i"})</span>
	{if $ppo->writeEnabled}
	<a href="{copixurl dest="admin|edit" id_news=$newsObject->id_news }">{copixicon type=update}</a>
	<a href="{copixurl dest="admin|delete" id_news=$newsObject->id_news }">{copixicon type=delete}</a>
	{/if}
	</h2>
	{copixzone process="tags|getTagsOfAssociation" idObject=$id_news kindObject="news"}
	{$newsObject->summary_news}
	<br />
	{copixurl dest="show" id_news=$newsObject->id_news title_news=$newsObject->title_news assign=moreUrl}
	<a href="{$moreUrl}">Lire la suite</a>
	{/foreach}
	</div>
{/if}
{foreach from=$ppo->arNews item=newsObject}
{assign var=id_news value=$newsObject->id_news}
<h2>{$newsObject->title_news}<span class="date_news"> (le {$newsObject->date_news|datei18n:text} à {$newsObject->heure_news|hour_format:"%H:%i"})</span>
{if $ppo->writeEnabled}
<a href="{copixurl dest="admin|edit" id_news=$newsObject->id_news }">{copixicon type=update}</a>
<a href="{copixurl dest="admin|delete" id_news=$newsObject->id_news }">{copixicon type=delete}</a>
{/if}
</h2>
{copixzone process="tags|getTagsOfAssociation" idObject=$id_news kindObject="news"}
{$newsObject->summary_news}
<br />
{copixurl dest="show" id_news=$newsObject->id_news comments=list title_news=$newsObject->title_news assign=moreUrl}
{copixzone process="comments|comment" id="module;group;action=show;id_news=$id_news"  required=false moreUrl=$moreUrl}

<br />
<a href="{$moreUrl}">Lire la suite</a>
{/foreach}

{if $ppo->writeEnabled}
<hr />
<a href="{copixurl dest="admin|edit" new=1}">{copixicon type=new}Ajouter une nouvelle</a>
{/if}