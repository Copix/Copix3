<form action="{copixurl dest="default|launch"}" name="form">
<div align="center"><a href="{copixurl dest="default|launchall"}">
Lancement de tous les tests </a> | <a
	href="javascript:document.form.submit();" name="envoyer"> Lancement des
tests séléctionnés </a></div>
<br />
{foreach from=$ppo->arCategories item=category key=cle}
<table class="CopixTable">
	{if ($ppo->arData[$cle]|@count)}
	<thead>
		<tr>
			 <th><input type="checkbox" name="cat[]" value="{$category->id_ctest}" />
			{$category->caption_ctest|escape}</th>
		</tr>
	</thead>
	{/if}
	<tbody>
		{foreach from=$ppo->arData[$cle] item=value}
		<tr>
			<td><input type="checkbox" name="id[]" value="{$value->id_test}" />
			{popupinformation displayimg=false text=$value->caption_test|escape}
			{assign var='level' value=$value->level_test|escape}
			{$ppo->level.$level}
			 {/popupinformation}
			</td>
		</tr>
		{/foreach}
	</tbody>
</table>
{/foreach}
<br />
 <a href="{copixurl dest="default|launchall"}">
<input type="button" name="send" value="{i18n key='test.launch.test'}" /> </a>
<br /><br />
<input type="button" onclick=location.href='{copixurl dest="admin|default|"}' style="width:100px" value="{i18n key='test.historyback'}" />
 </form>
