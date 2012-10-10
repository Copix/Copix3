{assign var=test value=__PHP_Incomplete_Class_Name}
<br />
{$ppo->nbInserer} {i18n key='csv.import.nb.import'}.<br />
<br />
{if $ppo->tabErreur}
	<div>	
		{$ppo->nbErreur} {i18n key='csv.import.errorimport'} :<br /><br/>
		
			{foreach from=$ppo->tabErreur item=lesErreurs key=key_tabErreur}
	
				{foreach from=$ppo->tabEnr item=lesEnrs key=key_tabEnr}
					{if $key_tabErreur === $key_tabEnr}
						{i18n key="csv.import.error.line"} :
						<br />
						{foreach from=$lesEnrs item=unEnr key=key_enr}
							{if $key_enr == $test}
								
							{else}
							{$unEnr}&nbsp;
							{/if}
						{/foreach}
					{/if}
				{/foreach}
				
				<table class="CopixTable">
					<tr>
						<th>{i18n key='csv.field'}</th>
						<th>{i18n key='csv.value'}</th>
						<th>{i18n key='csv.error'}</th>
					</tr>
				{assign var=uneErreur value=$lesErreurs key=key}
				{foreach from=$uneErreur item=detail key=key_error}
				
				<tr {cycle values=",class='alternate'"}>
					<td>"{$key_error}"</td>
					<!-- Affichage de la valeur du champ qui est incorrecte -->
					{foreach from=$ppo->tabEnr item=lesEnrs key=key_enr}
						{if $key_enr === $key_tabErreur}
							{foreach from=$lesEnrs item=unEnr key=k_enr}
								{if $k_enr === $key_error}
									<td>{$unEnr}</td>
								{/if}
							{/foreach}
						{/if}
					{/foreach}
					<td>{$detail}</td>
				</tr>	
				{/foreach}
			</table>
			<br/>
			{/foreach} 
		
	</div>	
{/if}
<br />

