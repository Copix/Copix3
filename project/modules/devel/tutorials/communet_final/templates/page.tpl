<h1>Bienvenue sur {$ppo->site}</h1>
<p>
{$ppo->description}
</p>
{if $ppo->isUserConnected}
	{if $ppo->isUserPage}
		<a href="{copixurl dest="communet_final||editpage" login=$ppo->login}">Editez votre page</a><br/>
	{else}
		{$ppo->isfriend}
		{if $ppo->id}
			<a href="{copixurl dest="communet_final||addtofriend" id=$ppo->id}">Ajouter à vos amis</a>
		{/if}
	{/if}
{/if}
<br/>
<br/>
{$ppo->friendlist}
<br />
Commentaires : 
{assign var=page value=$ppo->site}
{copixzone process="comments|comment" id="page=$page" required=false}
<br />
<br />
<a href="{copixurl dest="communet_final||}">Retour à l'accueil</a>