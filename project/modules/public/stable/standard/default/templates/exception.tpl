<div class="errorMessage">
	<h1>{$ppo->type}</h1>
	{$ppo->message}
	<br /><br />
	<a href="{$ppo->urlBack}">{i18n key="generictools|messages.action.backLong"}</a>
</div>

{if ($ppo->mode == 'DEVEL' || $ppo->mode == 'UNKNOWN')}
	<br />
	<div class="errorMessage" style="text-align: left">
		<h1>{i18n key="generictools|messages.titlePage.debugInformation"}</h1>
		<b>Type</b> : {$ppo->type}<br />
		<b>Fichier</b> : {$ppo->file}<br />
		<b>Ligne</b> : {$ppo->line}
		
		<br /><br />
		<center>
			{showdiv id=`$ppo->id` show="false" captioni18n="generictools|messages.action.debugMoreInfos"}
		</center>
		<div id="{$ppo->id}" style="display:none">
			<br />
			{if count ($ppo->trace)}
				<table class="CopixTable">
					<thead>
						<tr>
							<th>&nbsp;</th>
							<th>Classe</th>
						 	<th>Fonction</th>
						 	<th>Arguments</th>
						</tr>
					</thead>
					<tbody>
					{foreach from=$ppo->trace item=item key=index}
						<tr {cycle values='class="alternate",'}>
			 				<td>
			 					{popupinformation}
			 						{i18n key="generictools|messages.line"} : {$item.line}
			 						<br />
			 						{i18n key="generictools|messages.file"} : {$item.file}
			 					{/popupinformation}
			 				</td>
			 				<td>{if isset($item.class)}{$item.class}{/if}</td>
			 				<td><b>{$item.function}</b></td>
			 				<td><pre style="overflow: auto; max-width: 640px; max-height: 400px">{$item.args|@var_export:true}</pre></td>
						</tr>
					</tbody>
					{/foreach}
				</table>
			{/if}
		</div>
	</div>
{/if}