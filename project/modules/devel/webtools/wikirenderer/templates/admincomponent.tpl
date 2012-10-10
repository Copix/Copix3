<form method="POST" action="{copixurl dest='wikirenderer||saveinstallcomponents'}" >
{foreach from=$ppo->components item=modulecomponent key=module}
	<h1>{$module}</h1>
	
		{checkbox name="components" values=$modulecomponent objectMap='class;name' selected=$ppo->installedComponents separator='<br />'}
	
{/foreach}
<br />
<input type="submit" />
<input type="button" value="retour" onclick="javascript:document.location.href='{copixurl dest='admin||'}'" />