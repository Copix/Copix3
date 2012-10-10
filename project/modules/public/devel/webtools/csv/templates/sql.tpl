{mootools}
{literal}
<script type="text/javascript">

	/* Si la fonction execute s'est bien réalisé */
	ajaxOnSuccess = function (result) {
			//Si un résultat a bien été retourné, alors on affiche le resultat et on rends le bouton exporter visible
			if (result){
				$('result').innerHTML = result;
				//document.getElementById('exporter').style.display="block";
				document.getElementById('exporter').style.visibility='visible';
				$('errors').style.display = "none";
			}else{
				$('errors').innerHTML = "<h1>Erreurs</h1><ul><li>{/literal}{i18n key='csv.export.error.syntaxe.sql'}{literal}</li></ul>";
				$('errors').style.display = "block";
			}
	}
	
	/* si la fonction execute ne s'est pas bien réalisé */
	ajaxOnFailure = function (result) {
		alert ('ajaxOnFailure : ' + result);
	}

	/* Fonction permettant d'exectuer la requete sur la connexion choisie */
	function execute (requete, connection){
		
		//On supprime le contenu du div result et on rends le bouton exporter invisible
		$('result').innerHTML = '';
		//document.getElementById('exporter').style.display="none;";
		//document.getElementById('exporter').style.visibility="visible;";
		document.getElementById('exporter').style.visibility='hidden';
		//$('exporter').style.display = "none;";
		
		var myAjax = new Ajax(
				'{/literal}{copixurl dest="sql|Execute"}{literal}',
				{
					method: 'post',
					postBody: 'requete='+requete+'&connection='+connection,
					onSuccess : ajaxOnSuccess,
					onFailure : ajaxOnFailure
				});
		myAjax.request();
	}
	
	/* Fonction permettant de rediriger l'utilisateur vers la pgae ou l'on choisit la connexion */
	function redirect (connection){
		//Création de l'adresse
		var adresse =  '{/literal}{copixurl dest="export|getconnection" connection=connexionSelectionnee}{literal}';
		//Remplacement de connexionSelectionnee par le nom de la connexion selectionnée par l'utilisateur (passé en paramètre)
		var lien = adresse.replace('connexionSelectionnee',connection);
		//Effectue la redirection
		document.location.href = lien;
	}
	

</script>
{/literal}
<br />

<!-- div permettant d'afficher les messages d'erreurs dans le cas ou la requête n'a pu être executée -->
<div id="errors" style="display:none;" class="errorMessage"></div>

{if count ($ppo->arErrors)}
	<div class="errorMessage">
		<h1>Erreurs</h1>
		<!-- Génération de la liste d'erreur -->
		{ulli values=$ppo->arErrors}
	</div>
{/if}

<br />
<div id="larequete">
	<h2>{i18n key='csv.sql.subtitle.requete'}</h2>
	<p>
		<textarea id="requete" rows="3" cols="91" >{$ppo->requete}</textarea>
	</p>
	<br />
	<p>
		Connexion  : <select id="connection">
		{foreach from=$ppo->connections item=connection}
			{if $ppo->connection === $connection}
				<option value="{$connection}" selected="selected">{$connection}</option>
			{else}
				<option value="{$connection}">{$connection}</option>
			{/if}
		{/foreach}
		</select>
	</p>
	<br />
	 <input type="button" id="execute" value="{i18n key='csv.execute'}" onclick="javascript:execute(document.getElementById('requete').value,document.getElementById('connection').value);" /> 
</div>

<br />

<form action="{copixurl dest="sql|export"}" method="POST" enctype="multipart/form-data">
	<div id="result"></div>
	<input id="exporter" type="submit" value="{i18n key='csv.export'}" />
</form>
<input id="annuler" type="button" value="{i18n key='csv.return'}"  onclick="javascript:redirect(document.getElementById ('connection').value);"/>

{literal}
<script type="text/javascript">
	{/literal}
	 //Si il n'y a pas d'erreur, alors on clique directement sur le bouton executer pour afficher le resultat de la requete
	 {if count ($ppo->arErrors)}
	 {else}
		$('execute').click();
	 {/if}
	 {literal}
</script>
{/literal}
