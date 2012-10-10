{if count ($ppo->arErrors)}
 <div class="errorMessage">
  <h1>Erreurs</h1>
  {ulli values=$ppo->arErrors}
 </div>
{/if}

<h2>Informations générales</h2>
<form name="form" action="{copixurl dest="admin|valid"}" method="POST">
<table class="CopixTable">
 <tr>
  <th>{i18n key='test_soap.edit.caption'} *</th>
  <td><input type="textfield" style="width:300px" name="caption_test"
		value="{$ppo->toEdit->caption_test | escape}" /></td>
 </tr>
 <tr>
  <th>{i18n key='test_soap.edit.address'} *</th>
  <td><input type="textfield" style="width:300px" name="url_stest"
		value="{$ppo->toEdit->url_stest | escape}" />
		{if $ppo->soapFault}
		   {$ppo->soapFault}
		{/if}
  </td>
 </tr>
 <tr>
  <th>{i18n key='test_soap.edit.level'} *</th>
  <td>{select values=$ppo->arLevel name=level_test selected=$ppo->toEdit->level_test objectMap="id_level;caption_level"}</td>
 </tr>
 <tr>
  <th>{i18n key='test_soap.edit.category'} *</th>
  <td>{select values=$ppo->arCategories name=id_ctest
		selected=$ppo->toEdit->id_ctest
		objectMap="id_ctest;caption_ctest"}
  </td>
 </tr>
</table>

{if !$ppo->soapFault}
<h2>Fonction à tester</h2>
<table class="CopixVerticalTable">
 <tr>
  <th>{i18n key=test_soap.edit.function}</th>
  <td>{select name="function_stest" values=$ppo->toEdit->functions selected=$ppo->toEdit->function_stest}</td>
 </tr>
</table>
{/if}


{if $ppo->toEdit->function_stest}
<table class="CopixVerticalTable">
 {foreach from=$ppo->toEdit->tests item=test}
  <tr>
   <th>Type de test</th>
   <td>{select name="type" values="Existence;Appel"}</td>
  </tr>
 {/foreach}
</table>
{/if}

<input type="submit" name="configure" value="{i18n key='copix:common.buttons.valid'}" />
</form>

<a href="{copixurl dest="admin|default|"}">
 <input type="button" value="{i18n key='test_soap.back'}" />
</a>