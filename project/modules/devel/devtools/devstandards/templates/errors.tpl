Fichier : {$ppo->file}<br />
Erreur(s) : {$ppo->nbrErrors}
<br /><br />

{cycle values="" reset="true"}
<table class="CopixTable">
	<tr>
		<th width="45">Ligne</th>
		<th width="45">Type</th>
		<th width="18"></th>
		<th>Erreur</th>
		<th width="20 "></th>
	</tr>
	{foreach from=$ppo->arErrors key=errIndex item=errInfos}
		{cycle values=',class="alternate"' assign="alternate"}
		<tr {$alternate}>
			<td>{$errInfos.lineIndex}</td>
			<td>{$errInfos.typeI18n}&nbsp;</td>
			{if (count ($errInfos.subErrors) > 0)}
				<td>{showdiv id="errors_$errIndex" show="true"}</td>
				<td style="cursor:pointer" onclick="javascript: smarty_invert_show ('errors_{$errIndex}')">{$errInfos.error}</td>
				<td align="center">
					{if (!is_null ($errInfos.help))}
						{popupinformation}{$errInfos.help}{/popupinformation}
					{/if}
				</td>
				</tr>
				<tr {$alternate} id="errors_{$errIndex}">
				<td colspan="3"></td>
				<td>
					<div>
						<ul>
							{foreach from=$errInfos.subErrors key=subErrIndex item=subErrInfos}
								<li>{$subErrInfos}</li>
							{/foreach}
						</ul>
					</div>
				</td>
				<td></td>
			{else}
				<td></td>
				<td valign="middle">{$errInfos.error}</td>
				<td align="center">
					{if (!is_null ($errInfos.help))}
						{popupinformation}{$errInfos.help}{/popupinformation}
					{/if}
				</td>
			{/if}
			
		</tr>
	{/foreach}
</table>