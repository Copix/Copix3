{php}
_tag ('mootools', array ('plugin'=>'progressbar'));
{/php}

<br />
<div id="statusProgressBar" style="width: 300px;text-align: center">&nbsp;</div> 
<div id="progressBar" style="border: 1px solid #000; width: 300px;"></div>

	{assign var = tableName value =$ppo->tableName}
	{assign var = path value=$ppo->path}
	{assign var = tabEnr value = $ppo->tabEnr}

<div id="resultat"></div>
<br />
<div id="affichage"></div>

{literal}
<script defer="1" language="Javascript">

	//Tableau contenant les liens pour importer les données en plusieurs parties
	var linkList = new Array ();
	
	//Position est la position à laquelle on se trouve dans le tableau de liens
	var position = 0;

	//La variable value contient le nombre d'enregistrement qui a été importé à ce moment précis
	var value = 0;

	//Création du tableau contenant les liens pour l'importation des données
	{/literal}
		{foreach from=$tabEnr item=enr key=key}
			{assign var=debut value=$enr.debut}
			{assign var=fin value=$enr.fin}
			{literal}
				linkList[{/literal}{$key}{literal}] = '{/literal}{copixurl dest="import|callimport" tableName=$tableName path=$path numEnrDepart=$debut numEnrFin=$fin notxml=true}{literal}';
			{/literal}
		{/foreach}
	{literal}

	//Récupération du nombre d'enregistrement total
	var nbEnrTotal = {/literal}{$ppo->nbEnrTotal}{literal}

	//Instanciation de la barre de progression
	var progressBar1 = new ProgressBar ('progressBar', {steps: nbEnrTotal, length: 300, statusBar: 'statusProgressBar'});
	
	//Après chaque exportation de table, on affiche le résultat
	ajaxOnSuccess = function (result) {
			//Ajout au résultat des insertions réalisée
			$('resultat').innerHTML =result;
	}
	
	/* Fonction permettant de faire avancé la barre de progression, en executant l'import d'une partie des données*/
	function makeCall (){
		//Si toutes les imports n'ont pas été effectués
		if (position < linkList.length){
			new Ajax (
				linkList[position], 
				{onComplete: makeCall,
				onSuccess:ajaxOnSuccess}
			).request ();
			//Mise à jour du nombre d'enregistrement qui a été importé jusqu'a présent
			value = value + 200;   
			if (value > nbEnrTotal ){
				value = nbEnrTotal;
			}
			   
			//Avancement de la barre de progression a cette valeur
			progressBar1.set (value);      
		}
		position = position+1;
	}

	//Lancement de l'exportation des tables
	makeCall ();
	
</script>
{/literal}


{literal}
<script defer="1" language="Javascript">
</script>
{/literal}

<input type="button" value="{i18n key='csv.return'}" onclick="javascript:document.location.href='{copixurl dest="import|beforereturn"}'" />

