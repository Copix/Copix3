<div class="errorMessage">
	<h1>{$ppo->type}</h1>
	{$ppo->message}
	{if !is_null ($ppo->urlBack)}
		<br /><br />
		<a href="{$ppo->urlBack}">{i18n key="generictools|messages.action.backLong"}</a>
	{/if}
</div>

{if ($ppo->mode == 'DEVEL' || $ppo->mode == 'UNKNOWN')}
	<br />
	<div class="errorMessage" style="text-align: left">
		<h1>{i18n key="generictools|messages.titlePage.debugInformation"}</h1>
		
		<table class="CopixTable">
			<tr>
				<th style="width: 150px">Information</th>
				<th>Valeur</th>
			</tr>
			<tr>
				<td>{i18n key="default.exception.type"}</td>
				<td>{$ppo->type}</td>
			</tr>
			<tr class="alternate">
				<td>{i18n key="default.exception.code"}</td>
				<td>{$ppo->code}</td>
			</tr>
			<tr>
				<td>{i18n key="default.exception.file"}</td>
				<td>{$ppo->file}</td>
			</tr>
			<tr class="alternate">
				<td>{i18n key="default.exception.line"}</td>
				<td>{$ppo->line}</td>
			</tr>
			{foreach from=$ppo->extras key=name item=extra}
				<tr {cycle values=',class="alternate"'}>
					<td>{$name}</td>
					<td>{$extra}</td>
				</tr>
			{/foreach}
		</table>
		
		<br />
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