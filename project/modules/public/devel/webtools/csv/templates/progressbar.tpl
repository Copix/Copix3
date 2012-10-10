{php}
_tag ('mootools', array ('plugin'=>'progressbar'));
{/php}

<br />
<div id="statusProgressBar" style="width: 300px;text-align: center">&nbsp;</div> 
<div id="progressBar" style="border: 1px solid #000; width: 300px;"></div>

{assign var=connection value=$ppo->connection}

<div id="resultat"></div>
<br />
<div id="affichage"></div>



{literal}
<script defer="1" language="Javascript">

	//Tableau contenant les liens pour exporter chaque table
	var linkList = new Array ();
	
	var position = 0;
	
	//Création du tableau contenant les liens pour l'exportation des tables
	{/literal}
		{foreach from=$ppo->tables item=table key=key}
			{literal}
			linkList[{/literal}{$key}{literal}] = '{/literal}{copixurl dest="export|export" tableName=$table connection=$connection notxml=true}{literal}';
			{/literal}
		{/foreach}
	{literal}

	
	//Instanciation de la barre de progression
	var progressBar1 = new ProgressBar ('progressBar', {steps: linkList.length, length: 300, statusBar: 'statusProgressBar'});
	
	//Après chaque exportation de table, on affiche le résultat
	ajaxOnSuccess = function (result) {
			//Récupération de la position que l'on vient de passer
			var ancPosition = position -1 ;
			
			//Récupération de l'adresse
			var adresse =  linkList[ancPosition];
		
			//Récupération de l'index ou se trouve =
			var debut = adresse.indexOf("=",0);
			
			//Récupération de l'index ou se trouve &
			var final = adresse.lastIndexOf("&");
			
			//Récupération du nom de la table
			var tableName = adresse.substring(debut+1,final);
			
			//Appel de la fonction permettant de mettre à jour le tableau
			majTable (tableName);
	}
	
	/* Fonction permettant de faire avancé la barre de progression, en executant l'export d'une table */
	function makeCall (){
		//Si toutes les tables n'ont pas été exportées
		if (position < linkList.length){
			//récupération du nom de la table
			adresse = linkList[position];
			var tableName = null;
		   	tableName = adresse.substring(adresse.indexOf("=",0)+1,adresse.lastIndexOf("&"));
		   	majTable (tableName, '1');
		   	  
			new Ajax (
				linkList[position], 
				{onComplete: makeCall,
				onSuccess:ajaxOnSuccess}
			      		
			).request ();
			progressBar1.step ();      
		}
		position = position+1;
	}


	/* Création du tableau temporaire contenant les statuts et toutes les données des tables */
	var  tabtable = new Array ();
	var i = 0; 
 	
 	/* Génération du tableau contenant la liste des tables a exporter */
	{/literal}
		{foreach from=$ppo->tabTable item=table key=key}
			var tabDetails = new Array ();
			{foreach from=$table item=data key=keyData}
				{if $data}
					{literal}
					tabDetails['{/literal}{$keyData}{literal}'] = '{/literal}{$data}{literal}';
					{/literal}
				{else}
					{literal}
					tabDetails['{/literal}{$keyData}{literal}'] = null;
					{/literal}
				{/if}
			{/foreach}
			{literal}
				tabtable[i] = tabDetails;
				i++;
			{/literal}
		{/foreach}
	{literal}
	
	
	/**
	* Mets à jours les statuts des tables dans le tableau tabtable
	* 2 = en attente de traitement
	* 1 = en cours de traitement
	* 0 = exportée
	*/

	
	/* Fonction permettant de changer le statut d'une table (de en cours a exporté) */
	function majTable (tableName, statut){
	
		//Mets le statut a executer pour le nom de la table passée en parametre
		for (j=0; j<tabtable.length; j++){
			if (tabtable[j]['tableName'] == tableName){
				tabtable[j]['statut'] = statut;
			}
		}
		makeTable ();
	}
	
	/* Fonction permettant de générer le tableau contenant la liste des tables et précisant si elles ont été exportée */
	function makeTable (){
	
		html =  '<table class="CopixTable" >';
		html += '<tr>';
		html += '<th>Tables</th>';
		html += '<th>Statut</th>';
		html += '</tr>';
		
		for (j=0; j<tabtable.length; j++){
		
			//si c'est un nombre impair, alors on mets la ligne d'une autre couleur
			if ( ((j-1)%2) == 0 ){
				html += '<tr class="alternate">';
			}else{
				html += '<tr>';
			}
			
			html += '<td>'+tabtable[j]['tableName']+'</td>';
			
			//Si elle est en cours d'exportation
			if (tabtable[j]['statut'] == 2){
				html += '<td>{/literal}{copixicon type="back"}{literal}</td>';
			}else if (tabtable[j]['statut'] == 1){
				html += '<img src="{/literal}{copixresource path="img/tools/load.gif"}"{literal} />'
			}else{
				html += '<td>{/literal}{copixicon type="validate"}{literal}</td>';
			}
		
			html += '</tr>';
		}
		
		html += '</table>';
		
		$('affichage').innerHTML = html;
	}
	
	//Lancement de l'exportation des tables
	makeCall ();
	
</script>
{/literal}

{literal}
<script defer="1" language="Javascript">
makeTable ();
</script>
{/literal}
<br />
<a href="{copixurl dest="export|list"}">{i18n key='csv.export.gotofileexportlist'}</a>
<br /><br />
<input type="button" value="{i18n key='csv.return'}" onclick="javascript:document.location.href='{copixurl dest="admin|default|"}'" />

