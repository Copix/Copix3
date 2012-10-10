{mootools}
{if count ($ppo->arErrors)}
	<div class="errorMessage">
		<h1>Erreurs</h1>
		<!-- Génération de la liste d'erreur -->
		{ulli values=$ppo->arErrors}
	</div>
{/if}

<p>{i18n key='csv.sql.realizeinfile'} <p>
{assign var=fileName value=$ppo->file}
<ul>
	<li>
		{$ppo->file}
		&nbsp;
		{copixurl dest="export|download" nomfichier=$fileName assign=nameUrl}
		{CopixIcon href=$nameUrl  type="export" title="Télécharger le fichier" }
	</li>
</ul>

<a href="{copixurl dest="export|list"}">{i18n key='csv.export.gotofileexportlist'}</a>
<br />
<br />
<input type="button" value="{i18n key='csv.return'}" onclick="javascript:document.location.href='{copixurl dest="admin|default|"}'" />