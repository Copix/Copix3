{assign var=isFirst value=true}
{foreach from=$ppo->sections key=sectionName item=infos}
<h2{if $isFirst} class="first"{/if}>{$sectionName}</h2>
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
{assign var=isFirst value=false}
{/foreach}

<br />
<div style="text-align: right; width: 100%">
<a href="{copixurl dest="admin||"}"><img src="{copixresource path="img/tools/up.png"}" alt="back" /> {i18n key="copix:common.buttons.back"}</a>
</div>