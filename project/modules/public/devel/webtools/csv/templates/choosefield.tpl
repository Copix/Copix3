{mootools}
{if count ($ppo->arErrors)}
	<div class="errorMessage">
		<h1>Erreurs</h1>
		<!-- Génération de la liste d'erreur -->
		{ulli values=$ppo->arErrors}
	</div>
{/if}
<br />
<form action="{copixurl dest="sql|seeresult"}" method="POST" enctype="multipart/form-data">
	<br />
	<label>Sélectionner les champs que vous souhaitez afficher (maximum 4)</label>
			<br />
			<br />
		 
			<table class="CopixTable">
			<thead>
				<tr>
					<th>
						{i18n key="csv.field"}
					</th>
					<th></th>
				
				</tr>
			</thead>
			<tbody>

				{foreach from=$ppo->tabchamp item=unChamp}
				    <tr {cycle values=",class='alternate'"}>
				      <td>{$unChamp}</td>
				      <td><input type="checkbox" name="tabFields[]" value="{$unChamp}"/></td>
				{/foreach}
			</tbody>
		</table>

	<br />
	<input type="submit" value="{i18n key='csv.save'}"/>
	<input type="button" value="{i18n key='csv.cancel'}" onclick="javascript:document.location.href='{copixurl dest="sql|"}'"/>
</form>



