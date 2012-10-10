{foreach from=$ppo->sections key=sectionName item=infos}
<h2>{$sectionName}</h2>
<table class="CopixVerticalTable">
	{foreach from=$infos key=caption item=value}
	<tr {cycle values=',class="alternate"'}>
		<td width="270px">{$caption}</td>
		<td>
			{if is_array ($value)}
				<ul>
					{foreach from=$value key=key item=item}
						<li>{if (!is_int($key))}{$key}{else}{$item}{/if}</li>
						{if is_array ($item)}
							<ul>
								{foreach from=$item key=key2 item=item2}									
									{if is_array ($item2)}
										<li>{$key2}</li>
										<ul>
											{foreach from=$item2 key=key3 item=item3}
												<li>{if (!is_int($key3))}{$key3} : {/if}{$item3}</li>
											{/foreach}
										</ul>
									{else}
										<li>{if (!is_int($key2))}{$key2} : {/if}{$item2}</li>
									{/if}
								{/foreach}
							</ul>
						{/if}
					{/foreach}
				</ul>
			{else}
				{$value}
			{/if}
		</td>
	</tr>
	{/foreach}
</table>
{/foreach}

<br />
<input type="button" value="{i18n key="copix:common.buttons.back"}" onclick="javascript:window.location='{copixurl dest="admin||"}'">