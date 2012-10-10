<h1>Bienvenue sur {$ppo->site}</h1>
<p>
<b>1er bloc :</b>
<br />
{copixzone process="communet_final|description"}
{copixurl dest="communet_final||" assign="url"}
</p>

<p>
<b>2ème bloc :</b>
<br />
{if $ppo->user}
  Connecté sous le nom de : {$ppo->user}<br />
<a href="{copixurl dest="communet_final||page" login=$ppo->user}">Votre page</a><br/>
<a href="{copixurl dest="auth||" auth_url_return=$url}">Déconnectez vous</a>
{else}
<a href="{copixurl dest="communet_final||signup"}">Inscrivez vous</a><br/>
<a href="{copixurl dest="auth||" auth_url_return=$url}">Authentifiez vous</a>
{/if}
</p>

<p>
<b>3ème bloc</b>
<br />
{copixzone process="communet_final|alluserlist"}
</p>