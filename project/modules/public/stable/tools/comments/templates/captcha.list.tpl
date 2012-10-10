{literal}
<script language="javascript">
<!--
function confirmLink(theLink, confirmMsg) {
	if (confirmMsg == '') {
		return false;
	}

	var is_confirmed = confirm(confirmMsg);
	if (is_confirmed) {
		theLink.href;
	}
	
	return is_confirmed;
}

-->
</script>
{/literal}

<table class="CopixTable">
  <tr>
  	<th>{i18n key=comments.admin.captcha.enabled} 
  	{if $ppo->boolCaptcha==1}
  	{i18n key=comments.admin.captcha.statusok}</th>
  	<td>
    {i18n key=comments.admin.captcha.disable} <a href="{copixurl dest="comments|admin|ListCaptcha" status=0}"><img src="{copixresource path=img/tools/test.png}" border="0" /></a>
    {else}
    {i18n key=comments.admin.captcha.statusnok}</th>
    <td>
    {i18n key=comments.admin.captcha.enable} <a href="{copixurl dest="comments|admin|ListCaptcha" status=1}"> <img src="{copixresource path=img/tools/test.png}" border="0" /></a>
	{/if}
	</th>
  </tr>
</table>
<br/>

<table class="CopixTable">
<thead>
 <tr>
  <th>{i18n key=comments.admin.captcha.question}</th>
  <th>{i18n key=comments.admin.captcha.answer}</th>
  <th>{i18n key=comments.admin.captcha.actions}</th>
 </tr>
</thead>
<tbody>
	{foreach from=$ppo->arrCaptcha item=session}
	{if $session->captcha_id == $ppo->editedCaptcha}
	<tr {cycle values=',class="alternate"' name="captcha"}>
	 <form name="editCaptcha" action="{copixurl dest="comments|admin|editcaptcha" captchaid=$session->captcha_id confirm=1"}" method="post">
	 <td><input type="texte" name="captcha_question" value="{$session->question}"/></td>
	 <td><input type="texte" name="captcha_answer" value="{$session->answer}"/</td>
	 <td><input type="image" src="{copixresource path=img/tools/ok.png}" /></a></td>
	 </form>
	</tr> 
	{else} 
	<tr {cycle values=',class="alternate"' name="captcha"}>
	 <td>{$session->captcha_question}</td>
	 <td>{$session->captcha_answer}</td>
	 <td><a href="{copixurl dest="comments|admin|editcaptcha" captchaid=$session->captcha_id }"><img src="{copixresource path=img/tools/update.png}" ?></a>&nbsp;<a href="{copixurl dest="comments|admin|deletecaptcha" captchaid=$session->captcha_id }"><img src="{copixresource path=img/tools/delete.png}" /></a></td>
	</tr>
	{/if}
	{/foreach}
	<tr {cycle values=',class="alternate"' name="captcha"}>
	 <form name="addCaptcha" action="{copixurl dest="comments|admin|addcaptcha"}" method="post">
	 <td><input type="texte" name="captcha_question" /></td>
	 <td><input type="texte" name="captcha_answer" /></td>
	 <td><input type="image" src="{copixresource path=img/tools/add.png}"/></td>
	 </form>
	</tr>

</tbody>
</table>

<a href="{copixurl dest="admin||"}"> <input type="button" value="{i18n key="copix:common.buttons.back"}" /></a>