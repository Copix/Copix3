{foreach from=$ppo->arrComments item=comment name=comments}
{assign var="id" value=$comment->comment_id}
<form action="{copixurl dest="comments|admin|editComment" id=$id}"  method="post" class="comments-form">
	<input type="hidden" name="confirm" value="1">
{if isset($ppo->url_return)}
<input type="hidden" name="url_return" value="{$ppo->url_return}">
{/if}
	<fieldset>
	<legend>{i18n key="comments.list.addcomment"}</legend>
	<table class="CopixVerticalTable">
  	<tr>
  	 <th>
	  <label for="author">{i18n key="comments.list.author"}</label>
	 </th>
	 <td>
	  <input type="text" id="author" name="author" value="{$comment->authorlogin_comment|escape}"/>
	 </td>
	</tr>
	<tr>
	 <th>
	  <label for="mail">{i18n key="comments.list.mail"}</label>
	 </th>
	 <td>
	  <input type="text" id="mail" name="mail" value="{$comment->authoremail_comment|escape}"/>
	 </td>
	</tr>
	<tr>
	 <th> 
	  <label for="site">{i18n key="comments.list.site"}</label>
	 </th>
	 <td>
	  <input type="text" id="site" name="site" value="{$comment->authorsite_comment|escape}"/>
	 </td>
	</tr>
	<tr>
	 <th>
  	  <label for="content">{i18n key="comments.list.content"}</label>
  	 </th>
  	 <td>
  	  <textarea id="content" name="content">{$comment->content_comment|escape}</textarea>
  	 </td>
  	</tr>
	</table>
	<input type="submit" name="add" class="submit" value="{i18n key="comments.admin.confirm"}">
	</fieldset>
</form>
{/foreach}