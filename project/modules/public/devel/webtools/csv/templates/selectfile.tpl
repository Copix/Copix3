{copixhtmlheader kind="jsCode"}
{literal}


		/*Masque le div de selection et affichage du resultat dans le div*/
		ajaxOnSuccess = function (result) {
			document.getElementById('selection').style.display='none';
			$('resultat').innerHTML = result;
		}
		
		ajaxOnFailure = function (result) {
			alert ('ajaxOnFailure : ' + result);
		}

		/* Fonction permettant de faire un appel à une méthode de l'actiongroup*/
		function valider(choix){
		   
			 var adresse = null;
		
			/*Création de l'adresse de destination selon le choix effectué*/
			if (choix == 'upload'){
				adresse = '{/literal}{copixurl dest="import|upload"}{literal}';
			}else{
				adresse = '{/literal}{copixurl dest="import|list"}{literal}';
			}
		
			var myAjax = new Ajax(
				adresse,
				{
					method: 'post',
					onSuccess : ajaxOnSuccess,
					onFailure : ajaxOnFailure
				});
			myAjax.request();
		}
	

{/literal}
{/copixhtmlheader}

{mootools}
{if count ($ppo->arErrors)}
	<div class="errorMessage">
		<h1>Erreurs</h1>
		<!-- Génération de la liste d'erreur -->
		{ulli values=$ppo->arErrors}
	</div>
{/if}
<br />
	<div id="selection" >
			<h2>{i18n key='csv.import.title.type'}</h2>
			<br />
			<table class="CopixTable">
				<tr>
					<th>{i18n key='csv.import.type'}</th>
					<th></th>
				</tr>
				<tr {cycle values=",class='alternate'"}>
					<td>{i18n key='csv.import.type.poste'}</td>
					{copixurl dest="import|upload" assign=nameUrl}
					<td>{CopixIcon type="select" href=$nameUrl }</td>
				</tr>
				<tr {cycle values=",class='alternate'"}>
					<td>{i18n key='csv.import.type.export'}</td>
					{copixurl dest="import|list" assign=nameUrlList}
					 <td>{CopixIcon type="select" href=$nameUrlList }</td>
				</tr>
			</table>
			<br />
			<br />
		<input type="submit" value="{i18n key='csv.return'}" onclick="javascript:document.location.href='{copixurl dest="import|"}'" />
	</div>
	<div id="resultat"></div>
	
<br />
