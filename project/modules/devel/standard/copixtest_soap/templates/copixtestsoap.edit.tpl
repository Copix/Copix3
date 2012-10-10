{literal}
<script type="text/javascript">
function checkWSDL () {
	var wsdl = document.getElementById('wsdl').value;
	window.open(wsdl);
} 
</script>
{/literal}

{if count ($ppo->arErrors)}
<div class="errorMessage">
<h1>Erreurs</h1>
{ulli values=$ppo->arErrors}
</div>
{/if}

<form name="form" action="{copixurl dest="admin|Configure"}" method="POST">
<p align="justify">

<table width="100%" class="CopixTable">
	<tr>
		<th>
		<div align="left">{i18n key='copixtest_soap.edit.caption'} *</div>
		</th>
		<td><input type="textfield" style="width:300px" name="caption_test"
			value="{$ppo->toEdit->caption_test | escape}" /></td>
	</tr>
	<tr>
		<th>
		<div align="left">{i18n key='copixtest_soap.edit.address'} *</div>
		</th>
		<td><input type="textfield" style="width:300px" id="wsdl" name="address"
			value="{$ppo->toEditSOAP->address_soap | escape}" />
			<a href="javascript:checkWSDL()" > 
				{i18n key='copixtest_soap.edit.checkWSDL'}
			 </a>
			
		</td>
	</tr>
	<tr>
		<th>
			<div align="left">{i18n key='copixtest_soap.edit.proxy'}</div>
		</th>
		<td>
			<input type="radio" name="proxy" value="1" {$ppo->proxyenabled} > {i18n key='copixtest_soap.edit.yes'}
			<input type="radio" name="proxy" value="0" {$ppo->proxydisabled} > {i18n key='copixtest_soap.edit.no'}
		</td>
	</tr>
	<tr>
		<th>
		<div align="left">{i18n key='copixtest_soap.edit.level'} *</div>
		</th>
		<td>{select values=$ppo->arLevel name=level_test
		selected=$ppo->toEdit->level_test objectMap="id_level;caption_level"}
		<a href="{copixurl dest="copixtest|adminlevel|create"}" target="_blank">
		{i18n key='copixtest_soap.edit.addLevel'}
		</a>
		</td>
	
	
	<tr>
		<th>
		<div align="left">{i18n key='copixtest_soap.edit.category'} *</div>
		</th>
		<td>{select values=$ppo->arCategories name=category_test
		selected=$ppo->toEdit->category_test
		objectMap="id_ctest;caption_ctest"}
		<a href="{copixurl dest="copixtest|admincategory|default"}" target="_blank">
		{i18n key='copixtest_soap.edit.addCategory'}
		</a>
		</td>
	</tr>
</table>
<br />
<br />
<div align="right"><a href="javascript:document.form.submit();" name="envoyer"> <input type="button" style="width:100px"  value="{i18n key='copixtest_soap.edit.submit'}" /><br />
<a href="{copixurl dest="admin|cancel" }"><input style="width:100px" type="button" value="{i18n key='copixtest_soap.configure.cancel'}" /></a></div>
</form>
<a href="{copixurl dest="admin|default|"}"><input type="button" style="width:100px" value="{i18n key='copixtest_soap.back'}" />
 </a>