{mootools}
<br />
<fieldset>
	<div style="width:750px;overflow:auto;">
		{copixlist_list list="list" datasource="csv|requete" requete="$ppo->requete" tpl='csv|resultrequete.tpl' max=2}
	</div>
</fieldset>
<br />
<input type="button" value="{i18n key='csv.return'}" onclick="javascript:document.location.href='{copixurl dest="export|getConnection"}'" />
