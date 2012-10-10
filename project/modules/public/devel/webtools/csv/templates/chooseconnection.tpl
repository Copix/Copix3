{mootools}
{literal}
<script type="text/javascript">

	/* Variable contenant la connexion selectionnée */
	var selectedConnection = null;

	/* Tableau servant a savoir si la connexion est choisie ou non */
	var tableau = new Array ();
	{/literal}
	{assign var=tabSelected value=$ppo->tabSelected}
	{foreach from=$tabSelected item=test key=key}
		{literal}
		tableau['{/literal}{$key}{literal}'] = '{/literal}{$test}{literal}';
		{/literal}
	{/foreach}
	{literal}

	/*Tableau contenant le nom des connexions*/
	var connections = new Array ();
	{/literal}
	{assign var=lesConnexions value=$ppo->connections}
	{foreach from=$lesConnexions item=connect key=key}
		{literal}
		connections['{/literal}{$key}{literal}'] = '{/literal}{$connect}{literal}';
		{/literal}
	{/foreach}
	{literal}

	/*
	 * Permet d'afficher dans le div la liste des tables correspondant à la connexion selectionnée
	 */
	ajaxOnSuccess = function (result) {
			$('selectTable').innerHTML = result;
	}
	
	/* Affichage de l'erreur dans le cas ou l'action showTables ne se serait pas déroulé correctement */
	ajaxOnFailure = function (result) {
		alert ('ajaxOnFailure : ' + result);
	}
	
	/* Fonction permettant de faire appel à une méthode de l'actiongroup pour récupérer la liste des tables correspondant
	*  à la connexion passée en paramètre et egalement d'afficher le champ permettant de saisir une requête
	*/
	function showTables (connection, requete){
		
		var myAjax = new Ajax(
			'{/literal}{copixurl dest="export|"}{literal}',
			{
				method: 'post',
				postBody: 'connection='+connection+'&requete='+requete,
				onSuccess : ajaxOnSuccess,
				onFailure : ajaxOnFailure
				
			});
		myAjax.request();
		
	}
	
	/* Fonction permettant de cocher ou décocher toutes les checkbox */
	function check (field) {
	
		if (document.getElementById('checkAll').checked == false){
			for (i = 0; i < field.length; i++) {
			  		field[i].checked = false;
			 }
		}else{
			for (i = 0; i < field.length; i++) {
			  		field[i].checked = true;
			 }
		}
	}
	
</script>
{/literal}

<br />

<!-- Affichage des erreurs -->
{if count ($ppo->arErrors)}
	<div class="errorMessage">
		<h1>Erreurs</h1>
		<!-- Génération de la liste d'erreur -->
		{ulli values=$ppo->arErrors}
	</div>
	<br />
{/if}

<!-- Affichage du tableau contenant les connexions existantes -->
<h2>{i18n key="csv.export.choose.connection"}</h2>

<br />
	
	<table class="CopixTable">
		<tr>
			<th>{i18n key="csv.name"}</th>
			<th>{i18n key="csv.driverName"}</th>
			<th>{i18n key="csv.user"}</th>
			<th>{i18n key="csv.connectionString"}</th>
			<th></th>
		</tr>
		
		{foreach from=$ppo->connections item=connection}
			<tr {cycle values=",class='alternate'"}>
				<td>{$connection}</td>
				
				<!-- Affichage des détails des connexions -->
				{foreach from=$ppo->tabConnections item=detailsConnection key=key}
					{if $key == $connection}
						<td>{$detailsConnection->user}</td>
						<td>{$detailsConnection->driverName}</td>
						<td>{$detailsConnection->connectionString}</td>
					{/if}
				{/foreach}
				
				
				<!-- Affichage des icones -->
				{foreach from=$tabSelected item=selectedConnection key = key}
					{if $key == $connection}
						{if $selectedConnection == true}
							<td>{CopixIcon type="validate"}</td>
						{else}
							{copixurl dest="export|getConnection" connection=$connection assign=nameUrl}
							<td>{copixicon type=select href=$nameUrl}</td>
						{/if}
					{/if}
				{/foreach}
			</tr>
		{/foreach}	
	</table>
<br />

<br />

{literal}
<script type="text/javascript">
	/* Affichage des tables de la connexion selectionnée */
	for (k=0; k<connections.length; k++){
			if (tableau[connections[k]] == true){
				showTables(connections[k],"{/literal}{$ppo->requete}{literal}");
			}
		
	}
</script>
{/literal}

<div id="selectTable"></div>
<br />

<input id="returnButton" type="button" value="{i18n key='csv.return'}" onclick="javascript:document.location.href='{copixurl dest="admin|default|"}'" /> 
