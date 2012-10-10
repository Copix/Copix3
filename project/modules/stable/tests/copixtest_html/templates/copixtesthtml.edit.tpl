{literal}
<script type="text/javascript">
function checkUrl () {
var pathCheck = document.getElementById('path').value;
var domainCheck = document.getElementById('domain').value;
window.open(domainCheck + pathCheck);
}
</script>
{/literal}

{if count ($ppo->arErrors)}
<div class="errorMessage">
<h1>Erreurs</h1>
{ulli values=$ppo->arErrors}
</div>
{/if}

<form name="form" action="{copixurl dest="admin|configure"}" enctype="multipart/form-data" method="POST">

<table width="100%" class="CopixTable">
	<tr>
		<th>
		<div align="left">{i18n key='copixtest_html.edit.caption'} *</div>
		</th>
		<td><input id="caption" type="text" style="width:300px" name="caption_test"
			value="{$ppo->toEdit->caption_test|escape}"></td>
	</tr>
	<tr>
		<th>
		<div align="left">{i18n key='copixtest_html.edit.url'} *</div>
		</th>
		<td>
		{select values=$ppo->arDomain id="domain" name=id_domain selected=$ppo->toEditHTML->domain objectMap="url_domain;url_domain"}
		<input id="path" type="text" style="width:300px" name="path"
			value="{$ppo->toEditHTML->path|escape}">
			<a href="javascript:checkUrl();"> {i18n key='copixtest_html.edit.checkurl'} </a>
		</td>
	</tr>
	<tr>
		<th>
		<div align="left">{i18n key='copixtest_html.edit.proxy'}</div>
		</th>
		<td>
		<input type="radio" name="proxy" value=1 {$ppo->proxyenabled} /> {i18n key='copixtest_html.edit.yes'}
		<input type="radio" name="proxy" value=0 {$ppo->proxydisabled} /> {i18n key='copixtest_html.edit.no'}
	</tr>
	<tr>
		<th>
			<div align="left"> {i18n key='copixtest_html.edit.session'} </div>
		</th>
		<td>
			{select values=$ppo->arSessions id="session" name=session selected=$ppo->toEditHTML->session objectMap="id_session;caption_session"}
		</td>
	</tr>
	<tr>
		<th>
		<div align="left">{i18n key='copixtest_html.edit.level'} *</div>
		</th>
		<td>{select values=$ppo->arLevel name=level_test
		selected=$ppo->toEdit->level_test objectMap="id_level;caption_level"}
		<a href="{copixurl dest="copixtest|adminlevel|create"}" target="_blank"> {i18n key='copixtest_html.edit.newlevel'}</a>
		</td>
	
	<tr>
		<th>
		<div align="left">{i18n key='copixtest_html.edit.category'} *</div>
		</th>
		<td>{select values=$ppo->arCategories name=category_test
		selected=$ppo->toEdit->category_test
		objectMap="id_ctest;caption_ctest"} 
		<a href="{copixurl dest="copixtest|admincategory|default"}" target="_blank"> {i18n key='copixtest_html.edit.newcategory'} </a>
		</td>
	</tr>
	<tr>
		<th>
		<div align="left">{i18n key='copixtest_html.edit.post'}</div>
		</th>
		<td><input type="text" style="width:200px" name="param_post"
			value="{$ppo->toEditHTML->param_post | escape}"> <br />
		</td>
	</tr>

	<tr>
		<th>
		<div align="left">{i18n key='copixtest_html.edit.file'}</div>
		</th>
		<td><input type="file" name="param_file"
			value="{$ppo->toEditHTML->param_file | escape}"> <br />
		</td>
	</tr>

	<tr>
		<th>
		<div align="left">{i18n key='copixtest_html.edit.cookies'}</div>
		</th>
		<td><input type="text" style="width:300px" name="param_cookies" accept="text/html"
			value="{$ppo->toEditHTML->param_cookies | escape}"><br />
		</td>
	</tr>
</table>
<br />
<div align="right">
	<input type="submit" style="width:100px" value="{i18n key='copixtest_html.edit.next'}" /><br />
	<input type="button" style="width:100px" onclick="location.href='{copixurl dest="admin|cancel"}'" name="back" value="{i18n key='copixtest_html.edit.cancel'}" />
 </div>
	<input type="button" style="width:100px" onclick="location.href='{copixurl dest="copixtest|admin|default"}'" name="back" value="{i18n key='copixtest_html.admindomain.back'}" />
 </form>