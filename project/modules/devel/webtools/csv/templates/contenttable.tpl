{mootools}
<br />
<fieldset>
	<div style="width:750px;overflow:auto;">
		{copixlist_list ct=$ppo->connection list=$ppo->nomTable dao=$ppo->nomTable tpl='csv|list.table.tpl' max=20}
	</div>
</fieldset>
<br />
<input type="button" value="{i18n key='csv.return'}" onclick="javascript:document.location.href='{copixurl dest="export|getConnection" connection=$ppo->connection}'" />
