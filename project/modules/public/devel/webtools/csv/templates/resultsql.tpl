{mootools}
<br />
<form action="{copixurl dest="sql|export"}" method="POST" enctype="multipart/form-data">
	<fieldset>
		<h4>{i18n key="csv.sql.requete"}</h4>
	
		<textarea cols="91" rows="2" readonly="readonly">{$ppo->requete}</textarea>
		
		<br />
		<h4>{i18n key="csv.result"}</h4>
		<table class="CopixTable">

		{foreach from=$ppo->tabFields item=field}
			<th>{$field}</th>
		{/foreach}

		{foreach from=$ppo->arResults item=resultat}
			<tr>
				{foreach from=$ppo->tabFields item=field}
					<td>{$resultat->$field|escape}</td>
				{/foreach}
			</tr>
		{/foreach}
		
		</table>
		
	</fieldset>
<br />
	<input type="submit" value="{i18n key='csv.export'}"/>
	<input type="button" value="{i18n key='csv.cancel'}" onclick="javascript:document.location.href='{copixurl dest="sql|choosefield" requete = $ppo->requete}'"/>
</form>



