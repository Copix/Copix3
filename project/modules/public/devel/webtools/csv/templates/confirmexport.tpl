<br />

{if ($ppo->nomFichier)}
	{assign var=fileName value=$ppo->nomFichier}
	{i18n key='csv.export.confirm1'}&nbsp;"{$ppo->nomTable}"&nbsp;{i18n key='csv.export.confirm2'}:
	<ul>
		<li>
			{$fileName}
			&nbsp;
			{copixurl dest="export|download" nomfichier=$fileName assign=nameUrl}
			{CopixIcon href=$nameUrl  type="export" title="Télécharger le fichier" }
		</li>
	</ul>
{/if}

{if ($ppo->error)}
	{i18n key="csv.export.tablevide"}
	<ul><li>{$ppo->error}</li></ul>
{/if}