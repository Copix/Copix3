{mootools}
<form action="{copixurl dest="import|SelectColumn"}" method="POST" enctype="multipart/form-data">
	<h2>{i18n key="csv.import.choose.csvfile"}</h2>
			<br />
			<table class="CopixTable">
			<thead>
				<tr>
					<th></th>
					<th>{i18n key='csv.nomfichier'}</th>
					<th>{i18n key='csv.datehourcreation'}</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				{assign var=date value=$ppo->tabDate}
				{assign var=heure value=$ppo->tabHeure}
				
				{foreach from=$ppo->csvFile item=unFichier}
					{assign var=id value=$unFichier->id_csvfile}
					{assign var=popup value=""}
		
					 <tr {cycle values=",class='alternate'"}>
		 				<td>{popupinformation}
					      {$unFichier->nomfichier_csvfile|escape}<br />
					      {$date.$id}<br />
					      {$heure.$id}
					      {/popupinformation}</td>
					      <td>{$unFichier->nomfichier_csvfile}</td>
					      <td>{$date.$id}&nbsp;&nbsp;{$heure.$id}</td>
					      <td><input type="radio" name="id_fichier" value="{$id}" checked="checked"/></td>
					 </tr>
				{/foreach}
			</tbody>
		</table>
			
	<br />
	<input type="submit" value="{i18n key='csv.save'}"/>
	<input type="button" value="{i18n key='csv.cancel'}" onclick="javascript:document.location.href='{copixurl dest="import|choosefile"}'"/>
</form>



