<h2>Administration du module bugtrax</h2>
<h3>Liste des rubriques</h3>
<ul>

{foreach from=$ppo->headings item=head}
	<li>{$head->name} 
	({foreach from=$head->headings item=h}
		{$h->version_bughead} 
	{/foreach})
	maintenu par {$h->lead_bughead}
	</li>
{/foreach}
</ul>
<h3>Ajout d'une rubrique de bug</h3>
Une rubrique peut correspondre à un module, un plugin, un groupe de développement...

<form action="{copixurl dest="bugtrax|admin|addheading"}" method="POST">
	Nom de la rubrique: {autocomplete dao="bugtraxheadings" field="heading_bughead" name="heading_bughead"}<br />
	Version: <input type="text" name="version_bughead" /><br />
	Mainteneur principal: <select name="lead_bughead">
	{foreach from=$ppo->users item=user}
		<option value="{$user->id_dbuser}">{$user->login_dbuser}</option> 
	{/foreach}
	</select>
	<br />
	<input type="submit" value="{i18n key="copix:common.buttons.save"}" />
</form>