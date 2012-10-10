{mootools}
{assign var=connection value=$ppo->connection}

<p>
	<form action="{copixurl dest="export|ExportBarProgression" connection=$connection}" method="POST" enctype="multipart/form-data">
		<h2>{i18n key='csv.export.list.table'}</h2>
		<br />
		<table class="CopixTable">
			<thead>
				<tr>
					<th><input type="checkbox" id="checkAll" onclick="javascript:check(document.getElementsByName('nomTable[]'));" /></th>
					<th>{i18n key='csv.table'}</th>
					<th>{i18n key="csv.action"}</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$ppo->tabTable item=objTable}
				    <tr {cycle values=",class='alternate'"}>
				      <td><input type='checkbox' value='{$objTable}' name='nomTable[]' /></td>
					  <td>{$objTable}</td>
					  <td width="30">
					  	{copixurl dest="export|seecontent" nomTable=$objTable connection=$connection assign=nameUrl}
			  			<td>{copixicon type=show title="Visualiser le contenu de la table" href=$nameUrl}</td>
					  </td>
					</tr>
				{/foreach}
			</tbody>
		</table>
		<br />
		<input type="submit" value="{i18n key='csv.export'}" name="save" />
	</form>
</p>

<br />
<p>
	<form action="{copixurl dest="sql|" connection=$ppo->connection}" method="POST" enctype="multipart/form-data">
		<h2>{i18n key="csv.export.requetelibre"}</h2>
		<br />
		<textarea id="requetelibre" name="requetelibre" rows="3" cols="91" >{$ppo->requete}</textarea>
		<br />
	 	<input type="submit" value="{i18n key='csv.execute'}" name="save"/>
		<br />
	</form>
</p>
<br />
