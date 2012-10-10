{mootools}
<h2>{i18n key='csv.export.choose.column'}</h2>
<br />
	<div style="width:750px;overflow:auto;">
		{copixlist_list list="test2" ct=$ppo->connection datasource="csv|requete" requete=$ppo->requete tpl='csv|list.resultrequete.tpl' max=20}
	</div>
<br />
