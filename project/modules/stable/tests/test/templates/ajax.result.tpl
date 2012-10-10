{$HTML_HEAD}

{if ($ppo->arData->result == true) }
{assign var='result' value="#00ff00"}
{else}
{assign var='result' value="red"}
{/if} 

<table style="background-color:{$result}, width=100px">
	<tr>
			<td><div align="center"><b>{$ppo->arData->caption_test|escape}</b></div></td>
			<td><div align="center">{if $ppo->arData->result === true} {copixicon type="valid"} <font color="green"><b> {i18n key="test.launch.testok"} </b></font> {else}
			{copixicon type="delete"}{popupinformation handler=onclick displayimg=false text="Afficher les erreurs" zone='test|errors' data=$ppo->arData}  {/popupinformation}  {/if} 
	</tr>
</table>